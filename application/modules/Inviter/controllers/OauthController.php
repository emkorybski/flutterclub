<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: OauthController.php 2011-03-30 9:26 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_OauthController extends Core_Controller_Action_Standard
{
  private $config = array();

  /**
   * @var Inviter_Model_DbTable_Tokens
   */
  private $tokensTbl;

  private $provider;

  /**
   * @var Inviter_Api_Provider
   */
  private $providerApi;

  public function init()
  {
    $this->provider = $this->_getParam('provider', 'twitter');
    $this->tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');
    $this->providerApi = Engine_Api::_()->getApi('provider', 'inviter');
    $this->config = $this->providerApi->getProviderConfig($this->provider);

    // in smoothbox
    $this->_helper->layout->setLayout('default-simple');

    // set default callback url
    $url = $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => $this->provider), 'default');
    $this->_setCallbackUrl($url);

    $session = new Zend_Session_Namespace('inviter');

    if ($this->_getParam('signup', false)) {
      $session->__set('inviter_signup', 1);
    }
  }

  public function requestAction()
  {
    $new_token = $this->_getParam('new', false);
    $viewer = $this->_helper->api()->user()->getViewer();
    $session = new Zend_Session_Namespace('inviter');
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    $tokenRow = $this->tokensTbl->findUserToken($viewer->getIdentity(), $this->providerApi->checkProvider($this->provider));

    if ($tokenRow && !$new_token) {
      $this->view->tokenRow = $tokenRow;
      $this->view->form = $this->_generateForm($tokenRow);
      return;
    }

    if ($this->provider == 'facebook') {
      $app_id = $settings->getSetting('inviter.facebook.consumer.key', false);
      $secret = $settings->getSetting('inviter.facebook.consumer.secret', false);
      $fbApi = Engine_Api::_()->loadClass('Inviter_Plugin_fbApi');
      $fbApi->init($app_id, $secret);
      $url = $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => $this->provider), 'default');
      $redirect_url = $host_url . $url;

      $login_url = $fbApi->getLoginUrl($redirect_url, 'email');
      if ($new_token && $tokenRow) {
        $access_token = $tokenRow->toArray();
        $redirect_url = $host_url .
          $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'provider' => $this->provider, 'new' => null), 'default');
        $logout_url = $fbApi->getLogoutUrl($access_token['oauth_token'], $redirect_url);
        $tokenRow->delete();
        $this->_redirect($logout_url);
      }
      else
        $this->_redirect($login_url);

    } elseif ($this->provider == 'hotmail') {
      $this->_getHotmailRequest($tokenRow, $new_token);
    } elseif ($this->provider == 'last.fm') {
      $api_key = $settings->getSetting('inviter.lastfm.api.key');
      $auth_url = 'http://www.last.fm/api/auth/?api_key=' . $api_key;
      $this->_redirect($auth_url);
    } elseif ($this->provider == 'foursquare') {
      $client_id = $settings->getSetting('inviter.foursquare.consumer.key');

      $url = $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => $this->provider), 'default');
      $redirect_url = $host_url . $url;

      $auth_url = 'https://foursquare.com/oauth2/authenticate?client_id=' . $client_id . '&response_type=code&redirect_uri=' . $redirect_url;
      if ($new_token) {
        $this->_redirect($auth_url);
      }
      else
        $this->_redirect($auth_url);
    } elseif ($this->provider == 'mail.ru') {
      $client_id = $settings->getSetting('inviter.mailru.id');
      $secret = $settings->getSetting('inviter.mailru.secret.key');

      $url = $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => 'mailru'), 'default');
      $redirect_url = $host_url . $url;

      $auth_url = 'https://connect.mail.ru/oauth/authorize' . '?client_id=' . $client_id . '&response_type=code&redirect_uri=' . $redirect_url . '&scope=messages';

      $logout_url = 'http://auth.mail.ru/cgi-bin/logout?Page=' . urlencode($auth_url);
      if ($new_token)
        $this->_redirect($logout_url);
      else {
        $this->_redirect($auth_url);
      }

    } elseif ($this->provider == 'aol') {
      $aol_plugin = Engine_Api::_()->loadClass('Inviter_Plugin_AOL');
      $aol_plugin->init();
      $redirect_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => 'aol', 'format' => null), 'default');
      $auth_url = $aol_plugin->getLoginUrl($redirect_url);
      $this->_redirect($auth_url);

    }
    // elseif($this->provider == 'myspace') {
    //
    //            $config = $this->providerApi->getMySpaceConfig(1);
    //            $token = $this->providerApi->myspace_request($config);
    //
    //            if(!isset($token['oauth_problem'])){
    //                $session->__set('token_request', Zend_Json::encode(array(
    //                      'oauth_token' => $token['oauth_token'],
    //                      'oauth_token_secret' => $token['oauth_token_secret'],
    //                      'oauth_callback_confirmed' => $token['oauth_callback_confirmed']
    //                    )));
    //                $host_url = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'];
    //
    //                $url = $host_url . $this->view->baseUrl() . '/inviter/oauth/access/provider/myspace/';
    //                $redirect_url = $this->view->url(array('module'=>'Inviter', 'controller'=>'oauth','action'=>'access', 'provider'=>'myspace', 'format'=>null), 'default');
    //                $auth_url = 'http://api.myspace.com/authorize?oauth_token='.urldecode($token['oauth_token']).'&oauth_callback='. $redirect_url . '&myspaceid.permissions='.urlencode('ViewFullProfileInfo|AllowReceivingNotifications');
    //                $this->_redirect( $auth_url );
    //            }
    //        }

    $consumer = new Zend_Oauth_Consumer($this->config);

    try {
      if ($this->provider == 'gmail') {
        $token = $consumer->getRequestToken(array('scope' => 'http://www.google.com/m8/feeds/'));
      } elseif ($this->provider == 'orkut') {
        $token = $consumer->getRequestToken(array('scope' => 'http://orkut.gmodules.com/social/'));
      } else {
        $token = $consumer->getRequestToken();
      }
    }
    catch (Exception $e) {
      //            $this->view->error = $e->getMessage();
      $this->view->error = "This service is not available now. Please try later.";
      return;
    }
    $session->__set('token_request', Zend_Json::encode(array(
      'oauth_token' => $token->getParam('oauth_token'),
      'oauth_token_secret' => $token->getParam('oauth_token_secret'),
      'oauth_callback_confirmed' => $token->getParam('oauth_callback_confirmed')
    )));
    $consumer->redirect();
  }

  public function accessAction()
  {

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    $token = null;
    $viewer = $this->_helper->api()->user()->getViewer();
    $params = $this->_getAllParams();
    if (isset($params['denied']) && $params['denied']) {
      $this->view->denied = true;
      return;
    }

    $url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'callback', 'provider' => $this->provider), 'default');

    $this->_setCallbackUrl($url);

    $session = new Zend_Session_Namespace('inviter');

    if ($this->provider == 'facebook') {
      $code = $this->_getParam('code', null);
      $error = $this->_getParam('error', false);
      if ($error) {
        $redirect_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'format' => null, 'provider' => 'facebook'), 'default');
        $this->_redirect($redirect_url);
      }
      if ($code) {
        $redirect_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => $this->provider), 'default');
        $redirect_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'format' => 'smoothbox', 'provider' => 'facebook'), 'default');
        // '/inviter/oauth/access/format/smoothbox/provider/facebook';

        $app_id = $settings->getSetting('inviter.facebook.consumer.key', false);
        $secret = $settings->getSetting('inviter.facebook.consumer.secret', false);
        $fbApi = Engine_Api::_()->loadClass('Inviter_Plugin_fbApi');
        $fbApi->init($app_id, $secret);

        $token = $fbApi->getAccessToken($redirect_url, $code);

        if ((!$token))
          $this->_redirect($host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'provider' => $this->provider), 'default'));
      }

    }
    elseif ($this->provider == 'hotmail') {
    }
    elseif ($this->provider == 'lastfm') {
      $token = $this->_getParam('token', null);
    }
    elseif ($this->provider == 'foursquare') {
      $code = $this->_getParam('code', null);
      if ($code) {
        $url = $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => 'foursquare'), 'default');
        $redirect_url = $host_url . $url;
        $four_plugin = Engine_Api::_()->loadClass('Inviter_Plugin_Foursquare');
        $four_plugin->init();
        $token = $four_plugin->getAccessToken($code, $redirect_url);
      }
    }
    elseif ($this->provider == 'mailru') {
      $old = $this->_getParam('old', null);
      if (!$old) {
        $code = $this->_getParam('code', null);
        if ($code) {
          $url = $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => 'mailru'), 'default');
          $redirect_url = $host_url . $url;
          $mail_plugin = Engine_Api::_()->loadClass('Inviter_Plugin_MyMail');
          $mail_plugin->init();
          $token = $mail_plugin->getAccessToken($redirect_url, $code);
        }
      } else {
        $tokenRow = $this->tokensTbl->findUserToken($viewer->getIdentity(), $this->providerApi->checkProvider($this->provider));
        $token = $tokenRow->getParam('oauth_token');
      }
    }
    elseif ($this->provider == 'aol') {
      $code = $this->_getParam('statusCode', false);
      if ($code == 200) {
        $token = $this->_getParam('token_a', false);
      } else {
        exit('Invalid callback request. Oops. Sorry.');
      }
    }
//        elseif ($this->provider == 'myspace') {
//
//            if (!empty($params) && $session->__isset('token_request')) {
//                $token_request_params = Zend_Json::decode($session->__get('token_request'), Zend_Json::TYPE_ARRAY);
//
//                $config = $this->providerApi->getMySpaceConfig(2, ($token_request_params['oauth_token']), $token_request_params['oauth_token_secret']);
//                $config['oauth_token'] = str_replace('%2', '%252', $token_request_params['oauth_token']);
//
//                $token = $this->providerApi->myspace_request($config);
//            }
//        }
    elseif (!empty($params) && $session->__isset('token_request')) {
      if ($this->provider == 'yahoo') {
        sleep(1);
      }
      $token_request_params = Zend_Json::decode($session->__get('token_request'), Zend_Json::TYPE_ARRAY);

      $requestToken = new Zend_Oauth_Token_Request();
      $requestToken->setParams($token_request_params);
      $consumer = new Zend_Oauth_Consumer($this->config);
      $token = $consumer->getAccessToken($params, $requestToken);

      $token_access_params = ($this->provider == 'twitter')
        ? array(
          'oauth_token' => $token->getParam('oauth_token'),
          'oauth_token_secret' => $token->getParam('oauth_token_secret'),
          'user_id' => $token->getParam('object_id'),
          'screen_name' => $token->getParam('object_name')
        )
        : array(
          'oauth_token' => $token->getParam('oauth_token'),
          'oauth_token_secret' => $token->getParam('oauth_token_secret')
        );

      $session->__set('access_token', Zend_Json::encode($token_access_params));

      // Now that we have an Access Token, we can discard the Request Token
      $session->__unset('token_request');

    }
    else {

      exit('Invalid callback request. Oops. Sorry.');
    }

    $account_info = $this->_getAccountInfo($token);

    // check fb session expired
    if ($account_info === false) {
      $params = array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'provider' => $this->provider);
      $this->_redirect($this->view->url($params, 'default'));
    }

    if (!$account_info) {
      $this->view->error = "This service is not available now. Please try later.";
      return;
    }

    if ($viewer->getIdentity() != 0) {
      // delete duplicates
      $this->tokensTbl->delete(array("user_id = {$account_info['user_id']}", "object_id = '{$account_info['object_id']}'", "provider = '{$account_info['provider']}'"));

      // update tokens
      $tokensSel = $this->tokensTbl->select()
        ->where('user_id = ?', $account_info['user_id'])
        ->where('provider = ?', $account_info['provider'])
        ->where('active = ?', 1);

      $otherTokens = $this->tokensTbl->fetchAll($tokensSel);
      foreach ($otherTokens as $otherToken) {
        $otherToken->active = 0;
        $otherToken->save();
      }

      $tokenRow = $this->tokensTbl->createRow();
      $tokenRow->setFromArray($account_info);

      $tokenRow->save();
    } else {
      unset($account_info['creation_date']);
      $session->__set('account_info', Zend_Json::encode($account_info));
    }
    $url = $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'callback', 'provider' => $this->provider), 'default');
    $this->_redirect($host_url . $url);
  }

  public function callbackAction()
  {
    $viewer = $this->_helper->api()->user()->getViewer();
    $session = new Zend_Session_Namespace('inviter');
    $this->view->provider = $this->provider;

    /**
     * @var $providerApi Inviter_Api_Provider
     */
    $providerApi = Engine_Api::_()->getApi('provider', 'inviter');
    $token = $this->tokensTbl->getUserToken($viewer->getIdentity(), $providerApi->checkProvider($this->provider));

    if ($token === false && $session->__isset('account_info')) {
      $access_token_params = Zend_Json::decode($session->__get('account_info'), Zend_Json::TYPE_ARRAY);
      $token = $this->tokensTbl->getUserTokenByArray($access_token_params);
    }

    $contacts = $providerApi->getContacts($token, $providerApi->checkProvider($this->provider));

    if ($contacts === false) {
      $this->tokensTbl->delete(array("user_id = {$viewer->getIdentity()}", "provider = '{$this->provider}'", "active = 1"));
      $this->_redirect($this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'provider' => $this->provider), 'default'));
      return;
    }

    $session->__set('provider', $this->provider);
    $session->__set('user_id', $token->getParam('user_id'));

    $this->view->contact_count = count($contacts);
    $this->view->signup_page = (int)($session->__isset('inviter_signup') && $session->__get('inviter_signup'));

    $params = $this->_getAllParams();

    if ($params['provider'] == 'facebook') {
      if (isset($params['way']) && $params['way'] == '1') {
        $url = $this->view->url(array('module' => 'inviter', 'controller' => 'facebook', 'action' => 'response', 'state' => true), 'default');
        $this->_redirect($url);
      }
    }
  }

  public function _setCallbackUrl($url)
  {
    $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

    $this->config['callbackUrl'] = $host_url . $url;
  }

  private function _generateForm($tokenRow)
  {
    $form = new Engine_Form();

    $form->setDisableTranslator(true);

    $params = array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'callback');
    $form->setAction($this->view->url($params, 'default'));
    $form->setTitle(Zend_Registry::get('Zend_Translate')->_('INVITER_Confirm Account'));
    $form->getDecorator('Description')->setOption('escape', false);

    $object_name = ($tokenRow->provider != 'gmail' && $tokenRow->provider != 'yahoo') ? $tokenRow->object_name : "{$tokenRow->object_name} ({$tokenRow->object_id})";
    $form->setDescription($this->view->translate('INVITER_FORM_CONFIRM_ACCOUNT_DESC', $object_name));
    $provider = $tokenRow->provider;
    $form->addElement('Button', 'submit', array(
      'type' => 'submit',
      'label' => 'INVITER_Continue'
    ));
    $params['action'] = 'request';
    $params['new'] = 1;
    $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    $url = $host_url . $this->view->url($params, 'default');
    if ($provider == 'hotmail') {
      $wll = Engine_Api::_()->loadClass('Inviter_Plugin_WindowsLiveLogin');
      $settings = Engine_Api::_()->getDbTable('settings', 'core');
      $params = array('appid' => $settings->getSetting('inviter.hotmail.consumer.key', false),
        'secret' => $settings->getSetting('inviter.hotmail.consumer.secret', false),
        'securityalgorithm' => 'wsignin1.0'
      );
      $wll = Inviter_Plugin_WindowsLiveLogin::initMe($params);
      $logout_url = $wll->getTrustedLogoutUrl();
      $form->addElement('Cancel', 'cancel', array(
        'label' => 'INVTTER_Use another account',
        'link' => true,
        'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
        'href' => "javascript:void(0);",
        'onclick' => "inviter.live_logout('" . $logout_url . "','" . $url . "');",
        'decorators' => array(
          'ViewHelper'
        )
      ));
    } else {
      $form->addElement('Cancel', 'cancel', array(
        'label' => 'INVTTER_Use another account',
        'link' => true,
        'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
        'href' => $url,
        'decorators' => array(
          'ViewHelper'
        )
      ));
    }
    $form->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    return $form;
  }

  private function _getAccountInfo($token)
  {
    $session = new Zend_Session_Namespace('inviter');
    $viewer = $this->_helper->api()->user()->getViewer();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    $user_info = array(
      'user_id' => $viewer->getIdentity(),
      'provider' => $this->provider,
      'creation_date' => new Zend_Db_Expr('NOW()'),
      'active' => 1,
    );

    switch ($this->provider)
    {
      case 'twitter' :

        $user_info['oauth_token'] = $token->getParam('oauth_token');
        $user_info['oauth_token_secret'] = $token->getParam('oauth_token_secret');
        $user_info['object_id'] = $token->getParam('user_id');
        $user_info['object_name'] = $token->getParam('screen_name');

        break;

      case 'linkedin':

        $client = $token->getHttpClient($this->config);
        $client->setUri('http://api.linkedin.com/v1/people/~:(id,first-name,last-name)');
        $client->setMethod(Zend_Http_Client::GET);
        $response = $client->request();

        $status = $response->getStatus();

        if ($status != 200) {
          return false;
        }

        $content = $response->getBody();
        $xml = simplexml_load_string($content);

        $user_info['oauth_token'] = $token->getParam('oauth_token');
        $user_info['oauth_token_secret'] = $token->getParam('oauth_token_secret');
        $user_info['object_id'] = $xml->{'id'} . '';
        $user_info['object_name'] = $xml->{'first-name'} . ' ' . $xml->{'last-name'};

        break;

      case 'facebook':
        $res = $this->_getFacebookUserInfo($token);
        $user_info = array_merge($res, $user_info);
        break;

      case 'gmail':

        $client = $token->getHttpClient($this->config);
        $client->setUri('https://www.google.com/m8/feeds/contacts/default/thin');
        $client->setMethod(Zend_Http_Client::GET);
        $client->setParameterGet('max-results', 0);

        $response = $client->request();
        $status = $response->getStatus();

        if ($status != 200) {
          return false;
        }

        $content = $response->getBody();
        $xml = simplexml_load_string($content);

        $user_info['oauth_token'] = $token->getParam('oauth_token');
        $user_info['oauth_token_secret'] = $token->getParam('oauth_token_secret');
        $user_info['object_id'] = $xml->{'author'}->{'email'} . '';
        $user_info['object_name'] = $xml->{'author'}->{'name'} . '';

        break;

      case 'orkut':

        $params = array();
        $params['userId'] = "@me";
        $params['groupId'] = "@self";

        $p = array();
        $p['method'] = 'people.get';
        $p['id'] = 'myself';
        $p['params'] = $params;

        $params_string = json_encode($p);

        $client = $token->getHttpClient($this->config);
        $client->setUri('http://www.orkut.com/social/rpc');
        $client->setMethod(Zend_Http_Client::POST);
        $client->setRawData($params_string);
        $client->setHeaders('Content-type', 'application/json');

        $response = $client->request();
        $status = $response->getStatus();

        if ($status != 200) {
          return false;
        }

        $content = json_decode($response->getBody());

        $name = $content->data->name->familyName . ' ' . $content->data->name->givenName;
        $user_info['oauth_token'] = $token->getParam('oauth_token');
        $user_info['oauth_token_secret'] = $token->getParam('oauth_token_secret');
        $user_info['object_id'] = $content->data->id;
        $user_info['object_name'] = (trim($name) != "") ? $name : '_empty_';
        break;

      case 'yahoo':

        $client = $token->getHttpClient($this->config);
        $client->setUri('http://query.yahooapis.com/v1/yql');
        $client->setMethod(Zend_Http_Client::GET);
        $client->setParameterGet('q', 'select * from social.profile where guid = me');
        $client->setParameterGet('format', 'json');

        $response = $client->request();
        $status = $response->getStatus();

        if ($status != 200) {
          return false;
        }

        $content = $response->getBody();
        $content = Zend_Json::decode($content, Zend_Json::TYPE_ARRAY);
        $yahoo_account = isset($content['query']['results']['profile']) ? $content['query']['results']['profile'] : array();
        if (!$yahoo_account) {
          return false;
        }

        $emails = isset($yahoo_account['emails']) ? $yahoo_account['emails'] : array();
        $yahoo_email = false;
        foreach ($emails as $email) {
          if (isset($email['primary']) && $email['primary']) {
            $yahoo_email = $email['handle'];
            break;
          }
        }

        if (!$yahoo_email) {
          return false;
        }

        $user_info['oauth_token'] = $token->getParam('oauth_token');
        $user_info['oauth_token_secret'] = $token->getParam('oauth_token_secret');
        $user_info['object_id'] = $yahoo_email;
        $user_info['object_name'] = $yahoo_account['nickname'];

        break;

      case 'hotmail':

        $return_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => $this->provider), 'inviter_ru') . '/provider/hotmail';
        $privacy_url = $host_url . '/core/help/privacy';

        $wll = Engine_Api::_()->loadClass('Inviter_Plugin_WindowsLiveLogin');
        $params = array('appid' => $settings->getSetting('inviter.hotmail.consumer.key', false),
          'secret' => $settings->getSetting('inviter.hotmail.consumer.secret', false),
          'securityalgorithm' => 'wsignin1.0',
          'returnurl' => $return_url,
          'policyurl' => $privacy_url
        );
        $wll = Inviter_Plugin_WindowsLiveLogin::initMe($params);
        $wll->setDebug(false);

        $token = $this->_getParam('ConsentToken', null);

        $ct = $wll->processConsentToken($token);
        if ($ct && !$ct->isValid()) {
          $ct = null;
        }
        if ($ct) {
          $cid = $ct->getLocationID();
          $delegationToken = $ct->getDelegationToken();

          $httpHeaders = array("Authorization: DelegatedToken dt=\"{$delegationToken}\"");
          $options = array(
            CURLOPT_URL => "https://livecontacts.services.live.com/users/@L@" . $cid . "/rest/LiveContacts",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => true,
            CURLOPT_HTTPGET => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $httpHeaders
          );

          $ch = curl_init();
          curl_setopt_array($ch, $options);
          $response = curl_exec($ch);

          $xml_start = strpos($response, '<?xml');

          $xml = substr($response, $xml_start);

          $st = new SimpleXMLElement($xml);
          $user = $st->Owner;

          $token_access_params = array(
            'oauth_token' => $delegationToken,
            'oauth_token_secret' => $cid
          );

          $session->__set('access_token', Zend_Json::encode($token_access_params));
          $session->__unset('token_request');

          $user_info['oauth_token'] = $delegationToken;
          $user_info['oauth_token_secret'] = $cid;
          $user_info['object_id'] = $user->WindowsLiveID . '';
          $user_info['object_name'] = $user->Profiles->Personal->DisplayName . '';
        }

        break;

      case 'lastfm':
        $api_key = $settings->getSetting('inviter.lastfm.api.key');
        $secret = $settings->getSetting('inviter.lastfm.secret');

        $lastfm = Engine_Api::_()->loadClass('Inviter_Plugin_Lastfm');

        $params = array();
        $params['token'] = $token;
        $params['method'] = 'auth.getsession';
        $params['api_key'] = $api_key;
        $signature = $lastfm->sig($params, $secret);
        $params['api_sig'] = $signature;

        $result = $lastfm->make_request($params);
        $name = $result->session->name . '';
        $sk = $result->session->key . '';

        $token_access_params = array(
          'oauth_token' => $token,
          'oauth_token_secret' => $sk
        );

        $session->__set('access_token', Zend_Json::encode($token_access_params));
        $session->__unset('token_request');

        $params = array();
        $params['method'] = 'user.getinfo';
        $params['api_key'] = $api_key;
        $params['user'] = $name;
        $result = $lastfm->make_request($params);
        $id = $result->user->id . '';

        $user_info['oauth_token'] = $token;
        $user_info['oauth_token_secret'] = $sk;
        $user_info['object_id'] = $id;
        $user_info['object_name'] = $name;

        break;

//            case 'myspace':
//                $res = $this->_getMyspaceUserInfo($token);
//                $user_info = array_merge($res, $user_info);
//                break;

      case 'foursquare':
        $four_plugin = Engine_Api::_()->loadClass('Inviter_Plugin_Foursquare');
        $user = $four_plugin->getUser($token);
        $info = $four_plugin->getUserInfo($user);
        $info['oauth_token'] = $token;
        $user_info = array_merge($info, $user_info);
        break;

      case 'mailru':
        $mail_plugin = Engine_Api::_()->loadClass('Inviter_Plugin_MyMail');
        $mail_plugin->init();
        $user = $mail_plugin->getUser($token);
        $info = $mail_plugin->getUserInfo($user);
        $info['oauth_token'] = $token;
        $user_info = array_merge($info, $user_info);
        break;

      case 'aol':
        $redirect_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => 'aol', 'format' => null), 'default');
        $aol_plugin = Engine_Api::_()->loadClass('Inviter_Plugin_AOL');
        $aol_plugin->init();

        $info = $aol_plugin->getUser($token, null, $redirect_url);
        $info['oauth_token'] = $token;
        $user_info = array_merge($info, $user_info);
        break;

      default:
        break;
    }

    return $user_info;
  }

  private function _getFacebookRequest($tokenRow)
  {
    $new_token = $this->_getParam('new', false);
    $facebook = Inviter_Api_Provider::getFBInstance();

    $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    $next = array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => $this->provider);
    $cancel = array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => $this->provider, 'denied' => 1);

    if ($new_token) {
      $next['new'] = '0';
      $cancel['new'] = '0';

      $this->_redirect($facebook->getLogoutUrl(array(
        'display' => 'popup',
        'next' => $host_url . $this->view->url($next, 'default'),
        'cancel_url' => $host_url . $this->view->url($cancel, 'default')
      )), array('exit' => true));
    }

    try {
      $fb_user_id = Inviter_Api_Provider::getFBUserId();
    } catch (Exception $e) {
      $fb_user_id = 0;
    }

    $account_info = false;
    // Session based graph API call.
    if ($fb_user_id) {
      try {
        $access_token = $facebook->getAccessToken();

        $account_info = $facebook->api('/me');

      } catch (Exception $e) {
        $access_token = false;
      }
    }

    if ($account_info && $tokenRow && $account_info['id'] == $tokenRow->object_id) {
      return true;
    } elseif ($tokenRow) {
      $tokenRow->active = 0;
      $tokenRow->save();
    }

    if (isset($access_token) && $access_token) {
      $next['session'] = $access_token;
      $this->_redirect($host_url . $this->view->url($next, 'default'), array('exit' => true));
    } else {
      $this->_redirect($facebook->getLoginUrl(array(
        'display' => 'popup',
        'next' => $host_url . $this->view->url($next, 'default'),
        'cancel_url' => $host_url . $this->view->url($cancel, 'default')
      )), array('exit' => true));
    }

    return;
  }

  private function _getFacebookUserInfo($token)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $app_id = $settings->getSetting('inviter.facebook.consumer.key', false);
    $secret = $settings->getSetting('inviter.facebook.consumer.secret', false);
    $fbApi = Engine_Api::_()->loadClass('Inviter_Plugin_fbApi');
    $fbApi->init($app_id, $secret);
    $me = $fbApi->getMe($token);

    $user_info = array();
    $user_info['oauth_token'] = $token;
    $user_info['oauth_token_secret'] = $secret;
    $user_info['object_id'] = $me->id;
    $user_info['object_name'] = $me->name;

    return $user_info;
  }

  private function _getHotmailRequest($tokenRow, $new_token)
  {
    $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

    $return_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'access', 'provider' => $this->provider), 'inviter_ru') . '/provider/hotmail';
    $privacy_url = $host_url . '/core/help/privacy';

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $params = array('appid' => $settings->getSetting('inviter.hotmail.consumer.key', false),
      'secret' => $settings->getSetting('inviter.hotmail.consumer.secret', false),
      'securityalgorithm' => 'wsignin1.0',
      'returnurl' => $return_url,
      'policyurl' => $privacy_url
    );
    $wll = Engine_Api::_()->loadClass('Inviter_Plugin_WindowsLiveLogin');

    $wll = Inviter_Plugin_WindowsLiveLogin::initMe($params);
    $wll->setDebug(false);
    $consenturl = $wll->getConsentUrl('ContactsSync.FullSync');

    if ($tokenRow && $new_token) {
      $tokenRow->delete();
    }

    $this->_redirect($consenturl, array('exit' => true));
  }

  private function _getMyspaceUserInfo($token)
  {
    $user_info = array();

    $config = $this->providerApi->getMySpaceConfig(3, $token['oauth_token'], $token['oauth_token_secret']);
    $config['oauth_token'] = str_replace('%2', '%252', $token['oauth_token']);
    $result = $this->providerApi->myspace_request($config, 'rest');

    $displayName = $result->person->displayName;
    $familyname = $result->person->name->familyName;
    $givenname = $result->person->name->givenName;
    $name = 'Empty';
    if (trim($displayName) != '') {
      $name = $displayName;
    } else {
      if (trim($familyname) != '') {
        $name = $familyname;
      } else {
        if (trim($givenname) != '')
          $name = $givenname;
      }
    }

    $id = $result->person->id;

    $user_info['oauth_token'] = $token['oauth_token'];
    $user_info['oauth_token_secret'] = $token['oauth_token_secret'];
    $user_info['object_id'] = $id;
    $user_info['object_name'] = $name;
    return $user_info;
  }
}
