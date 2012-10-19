<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Provider.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Api_Provider extends Core_Api_Abstract
{
  /**
   * @var Inviter_Api_Facebook
   */
  public static $fb_instance;

  public static $fb_user_id;

  public static function getFBInstance()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');

    if (self::$fb_instance === null) {
      self::$fb_instance = new Inviter_Api_Facebook(array(
        'appId' => $settings->getSetting('inviter.facebook.consumer.key', false),
        'secret' => $settings->getSetting('inviter.facebook.consumer.secret', false),
        'cookie' => true,
        'baseDomain' => $_SERVER['HTTP_HOST'],
      ));
    }

    return self::$fb_instance;
  }

  public static function getFBUserId()
  {
    if (self::$fb_user_id !== null) {
      return self::$fb_user_id;
    }

    $facebook = self::getFBInstance();
    if (!$facebook) {
      self::$fb_user_id = 0;
    } else {
      try {
        self::$fb_user_id = $facebook->getUser();
      } catch (Exception $e) {
        self::$fb_user_id = 0;
      }
    }

    return self::$fb_user_id;
  }

  public function getProviderConfig($provider)
  {
    $config = array();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

    switch ($provider) {
      case 'twitter':
        $url_params = array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'provider' => 'twitter');
        $url = Zend_Controller_Front::getInstance()->getRouter()->assemble($url_params, 'default');

        $config = array(
          'callbackUrl' => $host_url . $url,
          'siteUrl' => 'http://twitter.com/oauth',
          'consumerKey' => $settings->getSetting('inviter.twitter.consumer.key', ''),
          'consumerSecret' => $settings->getSetting('inviter.twitter.consumer.secret', '')
        );

        break;

      case 'linkedin':
        $url_params = array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'provider' => 'linkedin');
        $url = Zend_Controller_Front::getInstance()->getRouter()->assemble($url_params, 'default');

        $config = array(
          'version' => '1.0',
          'localUrl' => $host_url . $url,
          'callbackUrl' => $host_url . $url,
          'requestTokenUrl' => 'https://api.linkedin.com/uas/oauth/requestToken',
          'userAuthorizationUrl' => 'https://api.linkedin.com/uas/oauth/authorize',
          'accessTokenUrl' => 'https://api.linkedin.com/uas/oauth/accessToken',
          'consumerKey' => $settings->getSetting('inviter.linkedin.consumer.key', ''),
          'consumerSecret' => $settings->getSetting('inviter.linkedin.consumer.secret', '')
        );

        break;

      case 'facebook':
        $url_params = array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'provider' => 'facebook');
        $url = Zend_Controller_Front::getInstance()->getRouter()->assemble($url_params, 'default');

        $config = array(
          'version' => '1.0',
          'localUrl' => $host_url . $url,
          'callbackUrl' => $host_url . $url,
          'consumerKey' => $settings->getSetting('inviter.facebook.consumer.key', ''),
          'consumerSecret' => $settings->getSetting('inviter.facebook.consumer.secret', '')
        );

        break;

      case 'gmail':
        $url_params = array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'provider' => 'gmail');
        $url = Zend_Controller_Front::getInstance()->getRouter()->assemble($url_params, 'default');

        $config = array(
          'version' => '1.0',
          'requestScheme' => Zend_Oauth::REQUEST_SCHEME_HEADER,
          'callbackUrl' => $host_url . $url,
          'requestTokenUrl' => 'https://www.google.com/accounts/OAuthGetRequestToken',
          'userAuthorizationUrl' => 'https://www.google.com/accounts/OAuthAuthorizeToken',
          'accessTokenUrl' => 'https://www.google.com/accounts/OAuthGetAccessToken',
          'consumerKey' => $settings->getSetting('inviter.gmail.consumer.key', ''),
          'consumerSecret' => $settings->getSetting('inviter.gmail.consumer.secret', '')
        );

        break;

      case 'yahoo':
        $url_params = array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'provider' => 'yahoo');
        $url = Zend_Controller_Front::getInstance()->getRouter()->assemble($url_params, 'default');

        $config = array(
          'siteUrl' => 'https://api.login.yahoo.com/oauth/v2/get_request_token',
          'requestTokenUrl' => 'https://api.login.yahoo.com/oauth/v2/get_request_token',
          'userAuthorizationUrl' => 'https://api.login.yahoo.com/oauth/v2/request_auth',
          'accessTokenUrl' => 'https://api.login.yahoo.com/oauth/v2/get_token',
          'consumerKey' => $settings->getSetting('inviter.yahoo.consumer.key', ''),
          'consumerSecret' => $settings->getSetting('inviter.yahoo.consumer.secret', ''),
          'signatureMethod' => 'PLAINTEXT',
          'oauth_signature' => $settings->getSetting('inviter.yahoo.consumer.secret', '') . '%26',
          'oauth_timestamp' => time(),
          'oauth_nonce' => md5(time()),
          'version' => '1.0',
          'callbackUrl' => $host_url . $url
        );

        break;

      case 'hotmail':
        $url_params = array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'provider' => 'hotmail');
        $url = Zend_Controller_Front::getInstance()->getRouter()->assemble($url_params, 'default');

        $config = array(
          'siteUrl' => 'https://oauth.live.com/authorize',
          'consumerKey' => $settings->getSetting('inviter.hotmail.consumer.key', ''),
          'consumerSecret' => $settings->getSetting('inviter.hotmail.consumer.secret', ''),
          'callbackUrl' => $host_url . $url,
        );
        break;

      case 'myspace':
        $url_params = array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'provider' => 'myspace');
        $url = $host_url . Zend_Controller_Front::getInstance()->getRouter()->assemble($url_params, 'default');
//          $url = 'http://project.hire-experts.kirill.com/inviter/oauth/access/provider/myspace';
        $nonce = md5(microtime() . mt_rand());
        $timestamp = time();
        $config = array(
          'oauth_version' => '1.0',
          'oauth_nonce' => $nonce,
          'oauth_timestamp' => $timestamp,
          'oauth_consumer_key' => $settings->getSetting('inviter.myspace.consumer.key', ''),
          'oauth_callback' => urlencode($url), //"http://project.hire-experts.kirill.com/inviter/oauth/access/provider/myspace",
          'oauth_secret_key' => $settings->getSetting('inviter.myspace.consumer.secret', ''),
          'oauth_signature_method' => 'HMAC-SHA1'
        );
        $config['oauth_signature'] = $this->build_myspace_signature($config, 'GET', 'http://api.myspace.com/request_token', $settings->getSetting('inviter.myspace.consumer.secret', '') . '&');
        break;

      case 'foursquare':
        $url_params = array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'provider' => 'foursquare');
        $url = Zend_Controller_Front::getInstance()->getRouter()->assemble($url_params, 'default');

        $config = array(
          'siteUrl' => 'https://api.login.yahoo.com/oauth/v2/get_request_token',
          'requestTokenUrl' => 'https://api.login.yahoo.com/oauth/v2/get_request_token',
          'userAuthorizationUrl' => 'https://api.login.yahoo.com/oauth/v2/request_auth',
          'accessTokenUrl' => 'https://api.login.yahoo.com/oauth/v2/get_token',
          'consumerKey' => $settings->getSetting('inviter.yahoo.consumer.key', ''),
          'consumerSecret' => $settings->getSetting('inviter.yahoo.consumer.secret', ''),
          'signatureMethod' => 'PLAINTEXT',
          'oauth_signature' => $settings->getSetting('inviter.yahoo.consumer.secret', '') . '%26',
          'oauth_timestamp' => time(),
          'oauth_nonce' => md5(time()),
          'version' => '1.0',
          'callbackUrl' => $host_url . $url
        );

        break;

      case 'orkut':
        $url_params = array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'provider' => 'orkut');
        $url = Zend_Controller_Front::getInstance()->getRouter()->assemble($url_params, 'default');
        $config = array(
          'version' => '1.0',
          'requestScheme' => Zend_Oauth::REQUEST_SCHEME_HEADER,
          'callbackUrl' => $host_url . $url,
          'requestTokenUrl' => 'https://www.google.com/accounts/OAuthGetRequestToken',
          'userAuthorizationUrl' => 'https://www.google.com/accounts/OAuthAuthorizeToken',
          'accessTokenUrl' => 'https://www.google.com/accounts/OAuthGetAccessToken',
          'consumerKey' => $settings->getSetting('inviter.gmail.consumer.key', ''),
          'consumerSecret' => $settings->getSetting('inviter.gmail.consumer.secret', '')
        );
        break;

      default:
        break;
    }

    return $config;
  }


  public function getMySpaceConfig($action = 1, $token = null, $token_secret = null, $user_id = null)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $consumer_key = $settings->getSetting('inviter.myspace.consumer.key', '');
    $consumer_secret = $settings->getSetting('inviter.myspace.consumer.secret', '');

    $nonce = md5(microtime() . mt_rand());
    $timestamp = time();

    $signature_secret = $consumer_secret . '&';
    $method = 'GET';
    $config = array(
      'oauth_version' => '1.0',
      'oauth_nonce' => $nonce,
      'oauth_timestamp' => $timestamp,
      'oauth_consumer_key' => $consumer_key,
      'oauth_signature_method' => 'HMAC-SHA1'
    );

    switch ($action) {
      case 1:
      default:
        $action_url = 'http://api.myspace.com/request_token';
        break;
      case 2:
        $action_url = 'http://api.myspace.com/access_token';
        $signature_secret .= $token_secret;
        $config['oauth_token'] = $token;
        break;
      case 3:
        $action_url = 'http://api.myspace.com/1.0/people/@me/@self';
        $signature_secret .= $token_secret;
        $config['oauth_token'] = $token;
        break;
      case 4:
        $action_url = 'http://api.myspace.com/1.0/people/' . $user_id . '/@friends';
        break;
      case 5:
//              $action_url = 'http://api.myspace.com/1.0/notifications/'.$user_id.'/@self';
//              $action_url = 'http://api.myspace.com/1.0/applications/207445/notifications/'.$user_id.'/@self?';
//              $action_url = 'http://api.myspace.com/1.0/notifications/@me/@self';
        $action_url = 'http://opensocial.myspace.com/roa/09/notifications/' . $user_id . '/@self';
//              $config['oauth_token'] = $token;
//              $signature_secret .= $token_secret;
        $method = 'POST';
        break;
    }

    $config['oauth_signature'] = $this->build_myspace_signature($config, $method, $action_url, $signature_secret);
    $config['url'] = $action_url;
    return $config;
  }

  public function build_myspace_signature($params, $method, $url, $secret)
  {

    ksort($params);

    $row = '';
    foreach ($params as $key => $value) {
      $row .= $key . '=' . $value . '&';
    }
    $row = substr($row, 0, count($row) - 2);

    $tmp_row = urlencode($url) . '&' . urlencode($row);
    $tmp_row = str_replace('%2B', '%2520', $tmp_row);
    $tmp_row = str_replace('%252', '%25252', $tmp_row);
    $row = $method . '&' . ($tmp_row);
    $signature = urlencode(base64_encode(hash_hmac("sha1", $row, $secret, true)));

    return $signature;
  }

  public function myspace_request($config, $mode = 'auth', $method = 'GET', $body = null)
  {
    $params = '';

    $url = $config['url'] . '?';
    unset($config['url']);
    foreach ($config as $key => $value) {
      $params .= $key . '=' . $value . '&';
    }

    $params = substr($params, 0, count($params) - 2);
    $url .= $params;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);

    if ($method == 'POST') {
      $headers = array('Content-Type' => 'application/json');
      $h = array();
      foreach ($headers as $k => $v) {
        $h[] = $k . ": " . $v;
      }

      curl_setopt($ch, CURLOPT_HEADER, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
      curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(implode("\r\n", $h)));

    } else {
      curl_setopt($ch, CURLOPT_HTTPGET, true);
    }

    $data = curl_exec($ch);

    switch ($mode) {
      case 'auth':
      default:
        $tmp = explode('&', $data);
        $token = array();
        foreach ($tmp as $str) {
          $str = explode('=', $str);
          $token[$str[0]] = $str[1];
        }
        return $token;
        break;
      case 'rest':
        $json = json_decode($data);
        return $json;
        break;
    }
  }

  public function getOrkutConfig($action = 1, $token = null, $token_secret = null, $user_id = null)
  {
    $config = array();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    $url_params = array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'provider' => 'orkut');
    $url = Zend_Controller_Front::getInstance()->getRouter()->assemble($url_params, 'default');

    $config['version'] = '1.0';
    $config['requestScheme'] = Zend_Oauth::REQUEST_SCHEME_HEADER;
    $config['consumerKey'] = $settings->getSetting('inviter.gmail.consumer.key', '');

    switch ($action) {
      case 1:
        $config = array_merge($config, array(
          'callbackUrl' => $host_url . $url,
          'requestTokenUrl' => 'https://www.google.com/accounts/OAuthGetRequestToken',
          'userAuthorizationUrl' => 'https://www.google.com/accounts/OAuthAuthorizeToken',
          'accessTokenUrl' => 'https://www.google.com/accounts/OAuthGetAccessToken',
          'consumerSecret' => $settings->getSetting('inviter.gmail.consumer.secret', '')
        ));
        break;
      case 2:
        $config = array_merge($config, array(
          'callbackUrl' => $host_url . $url,
          'requestTokenUrl' => 'https://www.google.com/accounts/OAuthGetRequestToken',
          'userAuthorizationUrl' => 'https://www.google.com/accounts/OAuthAuthorizeToken',
          'accessTokenUrl' => 'https://www.google.com/accounts/OAuthGetAccessToken',
          'consumerSecret' => $settings->getSetting('inviter.gmail.consumer.secret', '')
        ));
/*
 * http://www.orkut.com/social/pages/captcha?xid=1279397910268&
 * oauth_nonce=13bdb4e05b6d349256d618189977be03&
 * oauth_version=1.0&
 * oauth_timestamp=1279397909&
 * oauth_consumer_key=www.dxs.com.br&
 * oauth_token=1%2F7RDAd4VmDYt7zBxiQlCS4jfH6jexVyAJiarGTv_dEJc&
 * oauth_body_hash=XCGF2XpzvjJHiCCnQpdNu5y8294%3D&
 * oauth_signature_method=HMAC-SHA1&
 * oauth_signature=kMv3QFeYr4R8oJiybCoDGyn3x2M%3D
*/
        break;
      default:
        return false;
    }

    return $config;
  }

  public function build_orkut_signature($params, $url, $token_secret, $consumer_secret)
  {
    ksort($params);
    $row = '';
    foreach ($params as $key => $value) {
      $row .= $key . '=' . $value . '&';
    }
    $row = substr($row, 0, count($row) - 2);
    $row = 'GET&' . urlencode($url) . '&' . urlencode($row);
    $secret = urlencode($consumer_secret) . '&' . ($token_secret);
    $signature = base64_encode(hash_hmac("sha1", $row, $secret, true));

    return $signature;
  }

  public function checkProvider($provider)
  {
    $provider = strtolower($provider);
    $provider = strtolower(str_replace('.', '', $provider));
    $provider = strtolower(str_replace('!', '', $provider));
    if ($provider == 'live/hotmail' || $provider == 'msn') {
      $provider = 'hotmail';
    }
    return $provider;
  }

  public function checkIntegratedProvider($provider)
  {
    $provider = $this->checkProvider($provider);
    $settings = Engine_Api::_()->getApi('settings', 'core');

    switch ($provider) {
      case 'facebook':
        $facebook = self::getFBInstance();
        $status = $facebook->getAppId() ? true : false;
        break;

      case 'twitter':
        $status = $settings->getSetting('inviter.twitter.consumer.key', false) ? true : false;
        break;

      case 'linkedin':
        $status = $settings->getSetting('inviter.linkedin.consumer.key', false) ? true : false;
        break;

      case 'gmail':
        $status = $settings->getSetting('inviter.gmail.consumer.key', false) ? true : false;
        break;

      case 'yahoo':
        $status = $settings->getSetting('inviter.yahoo.consumer.key', false) ? true : false;
        break;

      case 'hotmail':
        $status = $settings->getSetting('inviter.hotmail.consumer.key', false) ? true : false;
        break;

      case 'lastfm':
        $status = $settings->getSetting('inviter.lastfm.api.key', false) ? true : false;
        break;

      case 'myspace':
        $status = $settings->getSetting('inviter.myspace.consumer.key', false) ? true : false;
        break;

      case 'foursquare':
        $status = $settings->getSetting('inviter.foursquare.consumer.key', false) ? true : false;
        break;

      case 'mailru':
        $status = $settings->getSetting('inviter.mailru.secret.key', false) ? true : false;
        break;

      case 'orkut':
        $status = $settings->getSetting('inviter.gmail.consumer.key', false) ? true : false;
        break;

      default:
        $status = false;
        break;
    }

    return $status;
  }

  public function getContacts($token, $provider)
  {
    if (!$token) {
      return false;
    }

    $friends_data = array('items' => array(), 'continue' => true, 'start' => 0);

    if (APPLICATION_ENV == 'production') {

      if ($provider == 'facebook') {
        $cache_id = 'facebook_friends' . $token->getParam('user_id');
      } elseif ($provider == 'gmail') {
        $cache_id = 'inviter_' . $provider . '_friends_' . str_replace('@gmail.com', '', $token->getParam('user_id'));
        $cache_id = str_replace('@', '_at_', $cache_id);
      } elseif ($provider == 'yahoo') {
        $cache_id = 'inviter_' . $provider . '_friends_' . str_replace('@yahoo.com', '', $token->getParam('user_id'));
        $cache_id = str_replace('@', '_at_', $cache_id);
      } elseif ($provider == 'hotmail') {
        $cache_id = 'inviter_' . $provider . '_friends_' . str_replace('@hotmail.com', '', $token->getParam('user_id'));
        $cache_id = str_replace('@', '_at_', $cache_id);
      } else {
        $cache_id = 'inviter_' . $provider . '_friends_' . $token->getParam('user_id');
      }

      $cache_id = str_replace('-', '_d_', $cache_id);
      $cache_id = str_replace('.', '_dt_', $cache_id);

      $cache = Engine_Cache::factory();
      $_friends_data = $cache->load($cache_id);

      if ($_friends_data && is_array($_friends_data)) {
        $friends_data = $_friends_data;

        if (isset($friends_data['continue']) && !$friends_data['continue']) {
          return $friends_data['items'];
        }
      }
    }

    $limit_start = (isset($friends_data['start']) && $friends_data['start']) ? $friends_data['start'] : 0;
    $limit_count = 100;

    // to fix GMail contacts
    $force_continue = false;

    switch ($provider) {

      case 'twitter':
        $contact_ids = $this->getTwitterContactIds($token, $limit_start, $limit_count);
        $contact_arr = $this->getTwitterContacts($token, $contact_ids);
        break;

      case 'linkedin':
        $contact_arr = $this->getLinkedInContacts($token, $limit_start, $limit_count);
        break;

      case 'facebook':
        $contact_arr = $this->getFacebookContacts($token, $limit_start, $limit_count);
        break;

      case 'gmail':
        $contact_list = $this->getGMailContacts($token, $limit_start, $limit_count);
        $contact_arr = ($contact_list) ? $contact_list['contacts'] : $contact_list;
        $force_continue = ($contact_list) ? ($contact_list['count'] == $limit_count) : false;
        break;

      case 'yahoo':
        $contact_arr = $this->getYahooContacts($token, $limit_start, $limit_count);
        break;

      case 'hotmail':
        $contact_arr = $this->getHotmailContacts($token, $limit_start, $limit_count);
        break;

      case 'lastfm':
        $contact_arr = $this->getLastfmContacts($token, $limit_start, $limit_count);
        break;

      case 'myspace':
        $contact_arr = $this->getMyspaceContacts($token, $limit_start, $limit_count);
        break;

      case 'foursquare':
        $contact_arr = $this->getFoursquareContacts($token, $limit_start, $limit_count);
        break;

      case 'mailru':
        $contact_arr = $this->getMailruContacts($token, $limit_start, $limit_count);
        break;

      case 'orkut':
        $contact_list = $this->getOrkutContacts($token, $limit_start, $limit_count);
        $contact_arr = ($contact_list) ? $contact_list['contacts'] : $contact_list;
        $force_continue = ($contact_list) ? ($contact_list['count'] == $limit_count) : false;
        break;

      default:
        $contact_arr = array();
        break;
    }

    if ($contact_arr === false) {
      return $contact_arr;
    }

    $contacts = ($friends_data['items']) ? $friends_data['items'] : array();
    foreach ($contact_arr as $contact_id => $contact_info) {
      if (!$contact_id || empty($contact_id) || trim($contact_id) == '')
        continue;
      $contacts[$contact_id] = $contact_info;
    }
    if (empty($contacts))
      return false;

    $friends_data = array(
      'items' => $contacts,
      'start' => $limit_start + count($contact_arr),
      'continue' => ($force_continue || count($contact_arr) == $limit_count)
    );

    if (APPLICATION_ENV == 'production') {
      $cache->save($friends_data, $cache_id);
      $cache->setLifetime(2 * 24 * 3600); // TODO SET RIGHT VALUE
    }

    return $contacts;
  }

  public function getTwitterContactIds($token, $start, $count)
  {
    $client = $token->getHttpClient($this->getProviderConfig('twitter'));
    $client->setUri('http://api.twitter.com/1/followers/ids.json');
    $client->setMethod(Zend_Http_Client::GET);
    $client->setParameterGet('user_id', $token->getParam('user_id'));

    /**
     * @var Zend_Http_Response
     */
    $response = $client->request();
    $status = $response->getStatus();

    if ($status != 200) {
      return false;
    }

    $content = Zend_Json::decode($response->getBody(), Zend_Json::TYPE_ARRAY);
    $follower_ids = (isset($content['ids'])) ? $content['ids'] : array();

    $contact_ids = array();
    foreach ($follower_ids as $index => $follower_id) {
      if ($index < $start) {
        continue;
      }

      if ($index >= ($start + $count)) {
        break;
      }

      $contact_ids[] = $follower_id;
    }

    return $contact_ids;
  }

  public function getTwitterContacts($token, $contact_ids)
  {
    $contacts = array();

    if (!$contact_ids) {
      return $contacts;
    }

    $contacts_str = implode(',', $contact_ids);

    $client = $token->getHttpClient($this->getProviderConfig('twitter'));
    $client->setUri('http://api.twitter.com/1/users/lookup.json');
    $client->setMethod(Zend_Http_Client::GET);
    $client->setParameterGet('user_id', $contacts_str);
    $response = $client->request();
    $status = $response->getStatus();

    if ($status != 200) {
      return false;
    }

    $contact_arr = Zend_Json::decode($response->getBody());
    foreach ($contact_arr as $contact_info) {
      $contacts[$contact_info['id']] = array(
        'id' => $contact_info['id'],
        'email' => $contact_info['screen_name'],
        'profile_image_url' => $contact_info['profile_image_url'],
        'name' => $contact_info['name']
      );
    }

    return $contacts;
  }

  public function getLinkedInContacts($token, $start = 0, $count = 2)
  {
    $client = $token->getHttpClient($this->getProviderConfig('linkedin'));
    $client->setUri('http://api.linkedin.com/v1/people/~/connections:(id,first-name,last-name,picture-url,public-profile-url)');
    $client->setMethod(Zend_Http_Client::GET);
    $client->setParameterGet('user_id', $token->getParam('user_id'));
    $client->setParameterGet('count', $count);
    $client->setParameterGet('start', $start);
    $response = $client->request();

    $status = $response->getStatus();

    if ($status != 200) {
      return false;
    }

    $content = $response->getBody();
    $xml = simplexml_load_string($content);

    $contacts = array();
    foreach ($xml->{'person'} as $person) {
      $start++;
      $contacts[$person->{'id'} . ''] = array(
        'id' => $person->{'id'} . '',
        'nid' => $start,
        'name' => $person->{'first-name'} . ' ' . $person->{'last-name'},
        'profile_image_url' => isset($person->{'picture-url'}) ? $person->{'picture-url'} . '' : '',
        'public_profile_url' => isset($person->{'public-profile-url'}) ? $person->{'public-profile-url'} . '' : ''
      );
    }

    return $contacts;
  }

  public function getHotmailContacts($token, $start = 0, $count = 2)
  {

    $delegationToken = $token->getParam('oauth_token');
    $cid = $token->getParam('oauth_token_secret');

    $httpHeaders = array("Authorization: DelegatedToken dt=\"{$delegationToken}\"");
    $options = array(
//            CURLOPT_URL => "http://apis.live.net/v5.0/me/contacts?limit=2&offset=3&access_token=".$token->getParam('oauth_token'),
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
    if (!$xml_start)
      return false;
    $xml = substr($response, $xml_start);
    $st = new SimpleXMLElement($xml);
    $xml_contacts = $st->Contacts->Contact;
    $contacts = array();

    foreach ($xml_contacts as $contact) {
      $start++;
      $email = $contact->Emails->Email->Address . '';
      if (!$email || trim($email) == '') {
        continue;
      }
      $first_name = $contact->Profiles->Personal->FirstName . '';
      $display_name = $contact->Profiles->Personal->DisplayName . '';
      $name = trim($first_name != '') ? $first_name : $display_name;
      $contacts[$email] = array(
        'id' => $email,
        'nid' => $start,
        'name' => $name,
      );
      if ($start >= $count)
        break;
    }
    return $contacts;
  }

  public function getLastfmContacts($token, $start = 0, $count = 2)
  {
    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    $api_key = $settings->getSetting('inviter.lastfm.api.key');
    $secret = $settings->getSetting('inviter.lastfm.secret');

    $lastfm = Engine_Api::_()->loadClass('Inviter_Plugin_Lastfm');
    $user = $token->getParam('screen_name');

    $params = array();
    $params['user'] = $user;
    $params['method'] = 'user.getfriends';
    $params['api_key'] = $api_key;

    $friends = $lastfm->make_request($params);
    $contacts = array();
    foreach ($friends->friends as $friend) {
      $start++;
      $name = $friend->user->name . '';
      $id = $friend->user->id . '';
      $contacts[$name] = array(
        'id' => $id,
        'nid' => $start,
        'name' => $name,
      );
    }
    return $contacts;
  }

  public function getMyspaceContacts($token, $start = 0, $count = 2)
  {
    $config = $this->getMySpaceConfig(4, $token->getParam('oauth_token'), $token->getParam('oauth_secret'), $token->getParam('user_id'));
    $result = $this->myspace_request($config, 'rest');

    $contacts = array();
    foreach ($result->entry as $friend) {
      $start++;
      $name = 'Empty';
      $id = explode('.', $friend->person->id);
      $displayName = $friend->person->displayName;
      $familyname = $friend->person->name->familyName;
      $givenname = $friend->person->name->givenName;
      if (trim($displayName) != '') {
        $name = $displayName;
      } else {
        if (trim($familyname) != '' || trim($givenname) != '') {
          $name = $familyname . ' ' . $givenname;
        }
      }
      $contacts[$name] = array(
        'id' => $id[3],
        'nid' => $start,
        'name' => $name
      );
    }
    return $contacts;
  }

  public function getFoursquareContacts($token, $start = 0, $count = 2)
  {
    $four_plugin = Engine_Api::_()->loadClass('Inviter_Plugin_Foursquare');
    $four_plugin->init();
    $friends = $four_plugin->getFriends($token->getParam('oauth_token'), $token->getParam('user_id'));
    $contacts = array();
    foreach ($friends as $friend) {
      $start++;
      $contacts[$friend['email']] = array(
        'id' => $friend['email'],
        'nid' => $start,
        'name' => $friend['name'],
      );
    }
    return $contacts;
  }

  public function getMailruContacts($token, $start = 0, $count = 2)
  {
    $mail_plugin = Engine_Api::_()->loadClass('Inviter_Plugin_MyMail');
    $mail_plugin->init();
    $friends = $mail_plugin->getFriends($token->getParam('oauth_token'));
    $contacts = array();
    foreach ($friends as $friend) {
      $start++;
      $contacts[$friend['email']] = array(
        'id' => $friend['email'],
        'nid' => $start,
        'name' => $friend['name'],
      );
    }
    return $contacts;
  }

  public function getFacebookContacts($token, $start = 0, $count = 2)
  {


    $fql = "SELECT uid, name, profile_url, pic_square, email FROM user "
      . "WHERE uid IN (SELECT uid2 FROM friend WHERE uid1={$token->getParam('user_id')}) "
      . "LIMIT $start, $count";
    //    $fql = "SELECT uid, name, pic_square FROM user "
    //      . "WHERE uid IN (SELECT uid2 FROM friend WHERE uid1={$token->getParam('user_id')}) "
    //      . "LIMIT $start, $count";
    $facebook = self::getFBInstance();

    try {
      $_contacts = $facebook->api(array('method' => 'fql.query', 'access_token' => $token->getParam('oauth_token'), 'query' => $fql));
    } catch (Exception $e) {
      return false;
    }

    $contacts = array();
    foreach ($_contacts as $friend_info) {
      $name = $friend_info['name'];
      $id = $friend_info['uid'];
      $start++;
      $contacts[$id] = array(
//        $contacts[$name] = array(
        'id' => $id,
        'nid' => $start,
        'name' => $name,
        'pic_square' => $friend_info['pic_square'],
        'profile_url' => $friend_info['profile_url']
      );
      //      $contacts[$friend_info['uid']] = $friend_info;
    }

    return $contacts;
  }

  public function getGMailContacts($token, $start = 0, $count = 2)
  {
    $start++;

    $client = $token->getHttpClient($this->getProviderConfig('gmail'));

    $client->setUri('https://www.google.com/m8/feeds/contacts/default/thin');
    $client->setMethod(Zend_Http_Client::GET);
    $client->setParameterGet('start-index', $start);
    $client->setParameterGet('max-results', $count);

    $response = $client->request();
    $status = $response->getStatus();

    if ($status != 200) {
      return false;
    }

    $content = $response->getBody();
    $xml = simplexml_load_string($content);

    $contacts = array();
    $contact_count = 0;
    foreach ($xml->{'entry'} as $person) {
      $contact_count++;
      $gd_email = is_array($person->xpath('gd:email')) ? array_shift($person->xpath('gd:email')) : false;
      if (!$gd_email) {
        continue;
      }

      $email = $gd_email->attributes()->{'address'} . '';

      if (!$email) {
        continue;
      }

      $name = (isset($person->{'title'}) && $person->{'title'} . '') ? $person->{'title'} . '' : $email;

      $start++;

      $contacts[$email] = array(
        'id' => $email,
        'nid' => $start,
        'name' => $name,
      );
    }

    return array('contacts' => $contacts, 'count' => $contact_count);
  }

  public function getYahooContacts($token, $start = 0, $count = 2)
  {
    $start++;

    $client = $token->getHttpClient($this->getProviderConfig('yahoo'));
    $client->setUri('http://query.yahooapis.com/v1/yql');
    $client->setMethod(Zend_Http_Client::GET);
    $client->setParameterGet('q', 'SELECT * from social.contacts WHERE guid=me');
    $client->setParameterGet('format', 'json');

    $response = $client->request();
    $status = $response->getStatus();

    if ($status != 200) {
      return false;
    }

    $content = $response->getBody();
    $content = Zend_Json::decode($content, Zend_Json::TYPE_ARRAY);

    $contact_list = isset($content['query']['results']['contact']) ? $content['query']['results']['contact'] : array();

    if (!$contact_list) {
      return false;
    }

    $contacts = array();
    foreach ($contact_list as $contact) {
      $start++;

      $email = '';
      $name = '';
      foreach ($contact['fields'] as $field) {
        if ($field['type'] == 'email' || $field['type'] == 'otherid') {
          $email = $field['value'];
        } elseif ($field['type'] == 'name') {
          $name = $field['value']['givenName'] . ' ' . $field['value']['middleName'] . ' ' . $field['value']['familyName'];
        }
      }

      $contacts[$email] = array(
        'id' => $email,
        'nid' => $start,
        'name' => $name,
      );
    }

    return $contacts;
  }

  public function getOrkutContacts($token, $start = 0, $count = 2)
  {
    $start++;

    $client = $token->getHttpClient($this->getProviderConfig('orkut'));
    $profileFields = array(
      'displayName',
      'currentLocation',
      'thumbnailUrl',
      'gender',
      'name',
      'email'
    );
    $params = array('method' => 'people.get',
      'params' => array('userId' => array('@me'),
        'groupId' => '@friends',
//     						            'fields' => $profileFields,
        'count' => 300)
    );
    $params_string = json_encode($params);
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
    if ($content->data->totalResults <= 0)
      return false;

    $contacts = array();
    $contact_count = 0;
    foreach ($content->data->list as $person) {
      $contact_count++;
      $start++;

      $id = $person->id;
      $name = $person->name->familyName . ' ' . $person->name->givenName;
      $contacts[$id] = array(
        'id' => $id,
        'nid' => $start,
        'name' => $name,
      );
    }

    return array('contacts' => $contacts, 'count' => $contact_count);
  }

  public function getNoneMemberContacts($token, $provider, $count = 9)
  {
    $contacts = $this->getContacts($token, $provider);

    if ($contacts === false) {
      return $contacts;
    } elseif (!$contacts) {
      return $contacts;
    }

    $contact_ids = array_keys($contacts);

    // exclude already invited users
    $invitesTbl = Engine_Api::_()->getDbTable('invites', 'inviter');

    $invitesSel = $invitesTbl->select()
      ->setIntegrityCheck(false)
      ->from($invitesTbl->info('name'), array('recipient'))
      ->where('provider = ?', $provider)
      ->where('new_user_id != ?', 0);

    $invited_users = $invitesTbl->getAdapter()->fetchCol($invitesSel);
    $contact_ids = array_diff($contact_ids, $invited_users);

    // exclude already joined users
    $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');
    $tokensSel = $tokensTbl->select()
      ->setIntegrityCheck(false)
      ->from($tokensTbl->info('name'), array('object_id'))
      ->where('provider = ?', $provider)
      ->where('user_id != ?', 0);

    $members = $tokensTbl->getAdapter()->fetchCol($tokensSel);
    $contact_ids = array_diff($contact_ids, $members);

    if ($provider == 'facebook') {
      // exclude already joined users
      $facebookTbl = Engine_Api::_()->getDbTable('facebook', 'user');
      $facebookSel = $facebookTbl->select()
        ->setIntegrityCheck(false)
        ->from($facebookTbl->info('name'), array('facebook_uid'));

      $facebook_members = $facebookTbl->getAdapter()->fetchCol($facebookSel);
      $contact_ids = array_diff($contact_ids, $facebook_members);
    }
    elseif ($provider == 'gmail' || $provider == 'yahoo' || $provider == 'hotmail' || $provider == 'foursquare' || $provider == 'mailru') {
      $usersTbl = Engine_Api::_()->getItemTable('user');

      $already_members = array();
      $contact_emails = array();
      foreach ($contact_ids as $index => $contact_id) {
        $e_index = intval($index / 500);

        if (!isset($contact_emails[$e_index])) {
          $contact_emails[$e_index] = array();
        }

        $contact_emails[$e_index][] = $contact_id;
      }

      foreach ($contact_emails as $emails) {

        if (count($emails) == 0) {
          continue;
        }

        $usersSel = $usersTbl->select()
          ->from(array('user' => $usersTbl->info('name')), array('user.email'))
          ->where('user.email IN (?)', $emails);

        $member_list = $usersTbl->getAdapter()->fetchCol($usersSel);

        if (!$member_list) {
          continue;
        }

        $already_members = ($already_members) ? $already_members : array();
        $already_members = array_merge($already_members, $member_list);
      }

      $contact_ids = array_diff($contact_ids, $already_members);
    }

    if (!$contact_ids) {
      return array();
    }

    shuffle($contact_ids);

    $none_members = array();
    foreach ($contact_ids as $contact_uid) {
      $none_members[] = $contacts[$contact_uid];

      if (count($none_members) == $count) {
        break;
      }
    }

    return $none_members;
  }

  public function getNoneFriendContacts($token, $provider, $count = 9)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $contacts = $this->getContacts($token, $provider);

    if ($contacts === false) {
      return $contacts;
    } elseif (!$contacts) {
      return $contacts;
    }

    $contact_ids = array_keys($contacts);

    $max_contact_count = 1000;
    $contact_groups = array();

    foreach ($contact_ids as $index => $contact_id) {
      $current_group = intval($index / $max_contact_count);
      $contact_groups[$current_group][] = $contact_id;
    }

    $usersTbl = Engine_Api::_()->getItemTable('user');
    $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');
    $invitesTbl = Engine_Api::_()->getDbTable('invites', 'inviter');
    $membershipTbl = Engine_Api::_()->getDbtable('membership', 'user');
    $facebookTbl = Engine_Api::_()->getDbTable('facebook', 'user');

    $byFacebook = array();
    $byTokens = array();
    $byInvites = array();
    foreach ($contact_groups as $contact_group) {

      if ($provider == 'facebook') {
        //find by facebook integration
        $select = $facebookTbl->select()
          ->from($facebookTbl->info('name'), array('user_id', 'facebook_uid'))
          ->where('facebook_uid IN (?)', $contact_group);

        $rows = $facebookTbl->getAdapter()->fetchPairs($select);
        $byFacebook = $byFacebook + $rows;
      }

      //find by tokens
      $select = $tokensTbl->select()
        ->from($tokensTbl->info('name'), array('user_id', 'object_id'))
        ->where('object_id IN (?)', $contact_group);

      $rows = $tokensTbl->getAdapter()->fetchPairs($select);
      $byTokens = $byTokens + $rows;

      //find by invites
      $select = $invitesTbl->select()
        ->from($invitesTbl->info('name'), array('new_user_id', 'recipient'))
        ->where('provider = "' . $provider . '" AND recipient IN (?)', $contact_group);

      $rows = $invitesTbl->getAdapter()->fetchPairs($select);
      $byInvites = $byInvites + $rows;

    }

    $facebook_members = $byFacebook + $byTokens + $byInvites;

    $fb_members = array();
    if (count($facebook_members) != 0) {
      $fb_member_ids = array_keys($facebook_members);
      $select = $usersTbl->select()
        ->setIntegrityCheck(false)
        ->from(array('user' => $usersTbl->info('name')), array('user.user_id'))
        ->joinLeft(array('friend' => $membershipTbl->info('name')), 'user.user_id = friend.user_id AND friend.resource_id = ' . $viewer_id, array())
        ->where('user.user_id IN (?)', $fb_member_ids)
        ->where('ISNULL(friend.resource_id)')
        ->where('user.user_id != ?', $viewer_id);
      $se_members = $usersTbl->getAdapter()->fetchCol($select);

      foreach ($se_members as $user_id) {
        $fb_members[$user_id] = $facebook_members[$user_id];
      }
    }

    if ($provider == 'gmail' || $provider == 'yahoo' || $provider == 'hotmail' || $provider == 'foursquare' || $provider == 'mailru') {
      $contact_emails = array();
      foreach ($contact_ids as $index => $contact_id) {
        $e_index = intval($index / 100); // @todo set right value

        if (!isset($contact_emails[$e_index])) {
          $contact_emails[$e_index] = array();
        }

        $contact_emails[$e_index][] = $contact_id;
      }

      foreach ($contact_emails as $emails) {

        if (count($emails) == 0) {
          continue;
        }

        $usersSel = $usersTbl->select()
          ->from(array('user' => $usersTbl->info('name')), array('user.user_id', 'user.email'))
          ->where('user.email IN (?)', $emails);

        $already_members = $usersTbl->getAdapter()->fetchPairs($usersSel);

        if (!$already_members) {
          continue;
        }

        $already_members = ($already_members) ? $already_members : array();
        foreach ($already_members as $se_user_id => $email) {
          $fb_members[$se_user_id] = $email;
        }
      }
    }

    $fb_none_friends = array();
    $se_none_friend_ids = array();
    foreach ($fb_members as $se_user_id => $fb_member_id) {
      if (!in_array($fb_member_id, $contact_ids)) {
        continue;
      }

      if (count($se_none_friend_ids) >= $count) {
        break;
      }

      $fb_none_friends[$se_user_id] = new Inviter_Model_FacebookFriend($contacts[$fb_member_id], $provider);
      $se_none_friend_ids[] = $se_user_id;
    }

    if (count($se_none_friend_ids) == 0) {
      return array();
    }

    shuffle($se_none_friend_ids);

    return array('fb_users' => $fb_none_friends, 'se_users' => Engine_Api::_()->getItemMulti('user', $se_none_friend_ids));
  }

  public function getAlreadyFriendContacts($token, $provider, $count = 9)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $contacts = $this->getContacts($token, $provider);

    if ($contacts === false) {
      return $contacts;
    } elseif (!$contacts) {
      return $contacts;
    }

    $contact_ids = array_keys($contacts);
    $max_contact_count = 1000;
    $contact_groups = array();

    foreach ($contact_ids as $index => $contact_id) {
      $current_group = intval($index / $max_contact_count);
      $contact_groups[$current_group][] = $contact_id;
    }

    $usersTbl = Engine_Api::_()->getItemTable('user');
    $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');
    $invitesTbl = Engine_Api::_()->getDbTable('invites', 'inviter');
    $membershipTbl = Engine_Api::_()->getDbtable('membership', 'user');
    $facebookTbl = Engine_Api::_()->getDbTable('facebook', 'user');

    $byFacebook = array();
    $byTokens = array();
    $byInvites = array();
    foreach ($contact_groups as $contact_group) {

      if ($provider == 'facebook') {
        //find by facebook integration
        $select = $facebookTbl->select()
          ->from($facebookTbl->info('name'), array('user_id', 'facebook_uid'))
          ->where('facebook_uid IN (?)', $contact_group);

        $rows = $facebookTbl->getAdapter()->fetchPairs($select);
        $byFacebook = $byFacebook + $rows;
      }

      //find by tokens
      $select = $tokensTbl->select()
        ->from($tokensTbl->info('name'), array('user_id', 'object_id'))
        ->where('object_id IN (?)', $contact_group);

      $rows = $tokensTbl->getAdapter()->fetchPairs($select);
      $byTokens = $byTokens + $rows;

      //find by invites
      $select = $invitesTbl->select()
        ->from($invitesTbl->info('name'), array('new_user_id', 'recipient'))
        ->where('provider = "' . $provider . '" AND recipient IN (?)', $contact_group);

      $rows = $invitesTbl->getAdapter()->fetchPairs($select);
      $byInvites = $byInvites + $rows;

    }

    $facebook_members = $byFacebook + $byTokens + $byInvites;

    if (count($facebook_members) == 0) {
      return array();
    }

    $fb_member_ids = array_keys($facebook_members);
    $select = $usersTbl->select()
      ->setIntegrityCheck(false)
      ->from(array('user' => $usersTbl->info('name')), array('user.user_id'))
      ->joinLeft(array('friend' => $membershipTbl->info('name')), 'user.user_id = friend.user_id AND friend.resource_id = ' . $viewer_id, array())
      ->where('user.user_id IN (?)', $fb_member_ids)
      ->where('friend.active = ?', 1);

    $se_members = $usersTbl->getAdapter()->fetchCol($select);

    $fb_members = array();

    foreach ($se_members as $user_id) {
      $fb_members[$user_id] = $facebook_members[$user_id];
    }

    $fb_already_friends = array();
    $se_already_friend_ids = array();
    foreach ($fb_members as $se_user_id => $fb_member_id) {
      if (!in_array($fb_member_id, $contact_ids)) {
        continue;
      }

      if (count($se_already_friend_ids) >= $count) {
        break;
      }

      $fb_already_friends[$se_user_id] = new Inviter_Model_FacebookFriend($contacts[$fb_member_id], $provider);
      $se_already_friend_ids[] = $se_user_id;
    }

    if (count($se_already_friend_ids) == 0) {
      return array();
    }
    shuffle($se_already_friend_ids);
    return array('fb_users' => $fb_already_friends, 'se_users' => Engine_Api::_()->getItemMulti('user', $se_already_friend_ids));
  }

  public function sendInvites($token, $provider, $contact_ids, $page_id = null, $captcha_value = null, $captcha_token = null)
  {
    if (!$token) {
      return 'Invalid token';
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $invitesTbl = Engine_Api::_()->getDbTable('invites', 'inviter');
    $contacts = $this->getContacts($token, $provider);

    if ($provider == 'linkedin' || $provider == 'gmail' || $provider == 'yahoo' || $provider == 'hotmail' || $provider == 'last_fm' || $provider == 'foursquare'
      || $provider == 'mailru'
    ) {
      $contact_temp = array();
      foreach ($contacts as $uid => $info) {
        if (in_array($info['nid'], $contact_ids)) {
          $contact_temp[] = $uid;
        }
      }

      $contact_ids = $contact_temp;
    }
    $twitter_step = false;
    switch ($provider) {
      case 'facebook':
        $result = $this->sendFacebookInvites($token, $contact_ids, $page_id);
        break;

      case 'linkedin':
        $result = $this->sendLinkedInInvites($token, $contact_ids, $page_id);
        break;

      case 'twitter':
        $result = $this->sendTwitterInvites($token, $contact_ids, $page_id);
        if (isset($result['continue']) && $result['continue']) {
          $twitter_step = true;
          $result = $result['result'];
        }
        break;

      case 'gmail':
        $result = $this->sendMailServiceInvites($token, $contact_ids, 'gmail', $page_id);
        break;

      case 'orkut':
        $result = $this->sendOrkutInvites2($token, $contact_ids, $page_id, $captcha_value, $captcha_token);
        if (isset($result['captcha_token'])) {
          return $result;
        }
        break;

      case 'yahoo':
        $result = $this->sendMailServiceInvites($token, $contact_ids, 'yahoo', $page_id);
        break;

      case 'hotmail':
        $result = $this->sendMailServiceInvites($token, $contact_ids, 'hotmail', $page_id);
        break;

      case 'lastfm':
        $result = $this->sendLastfmInvites($token, $contact_ids, $page_id);
        break;

      case 'myspace':
        $result = $this->sendMyspaceInvites($token, $contact_ids, $page_id);
        break;

      case 'foursquare':
        $result = $this->sendMailServiceInvites($token, $contact_ids, 'foursquare', $page_id);
        break;

      case 'mailru':
        $result = $this->sendMailruInvites($token, $contact_ids, $page_id);
        break;

      default:
        $result = false;
        break;
    }

    if ($result === false) {
      return 'Service is not available now';
    }

    if ($provider == 'gmail') {
      return true;
    }

    $sent_date = new Zend_Db_Expr('NOW()');

    foreach ($contact_ids as $contact_id) {


      if (!isset($contacts[$contact_id]) || !isset($result[$contact_id])) {
        continue;
      }

      $contact_info = $contacts[$contact_id];
      $name = $token->getParam('user_id');
      //        if($provider=='linkedin') {
      //            $name = $token->getParam('screen_name');
      //        }


      $invitesTbl->insertInvitation(array(
        'user_id' => (int)$viewer->getIdentity(),
        'sender' => $name,
        'recipient' => $contact_id,
        'code' => $result[$contact_id],
        'message' => '',
        'sent_date' => $sent_date,
        'provider' => $provider,
        'recipient_name' => $contact_info['name']
      ));

      if (trim($result[$contact_id]) != '') {
        $viewer->invites_used++;
        $viewer->save();
      }
    }
    if ($twitter_step)
      return array('twitter_step' => true);
    return true;
  }

  public function sendFacebookInvites($token, $contact_ids, $page_id)
  {
    $access_token = $token->getParam('oauth_token');
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $app_id = $settings->getSetting('inviter.facebook.consumer.key', false);
    $secret = $settings->getSetting('inviter.facebook.consumer.secret', false);
    $fbApi = Engine_Api::_()->loadClass('Inviter_Plugin_fbApi');
    $fbApi->init($app_id, $secret);
    $me_id = $fbApi->getMe($access_token, true);

    $result_apprequest = $fbApi->apprequest($access_token, $contact_ids[0]);
  }

  public function sendLinkedInInvites($token, $contact_ids, $page_id = null)
  {
    if (!$token && !$contact_ids) {
      return false;
    }

    $client = $token->getHttpClient($this->getProviderConfig('linkedin'));

    $client->setUri('http://api.linkedin.com/v1/people/~/mailbox');
    $client->setMethod(Zend_Http_Client::POST);

    $session = new Zend_Session_Namespace('inviter');
    $message = $session->__get('message');

    $translate = Zend_Registry::get('Zend_Translate');

    $subject = $translate->_('INVITER_You have received an invitation to join our social network.');
    $body = $translate->_('INVITER_You have been invited by %1$s to join our social network. To join, please follow the link below: %2$s %3$s');

    $sent = 0;
    $invite_codes = array();
    foreach ($contact_ids as $contact_id) {
      $code = substr(md5(rand(0, 999) . $contact_id), 10, 7);

      if (is_null($page_id))
        $url = $this->getInvitationUrl($code, null);
      else
        $url = $this->getInvitationUrl($code, null, $page_id);

      $recipient = '    <recipient>
      <person path="/people/id=' . $contact_id . '" />
    </recipient>';

      $body = vsprintf($body, array($token->getParam('screen_name'), $url, $message));

      $xml = <<<XML_MESSAGE
<?xml version="1.0" encoding="UTF-8"?>
<mailbox-item>
  <recipients>
    {$recipient}
  </recipients>
  <subject>{$subject}</subject>
  <body>{$body}</body>
</mailbox-item>
XML_MESSAGE;

      $client->setRawData($xml, 'text/xml');
      $client->setHeaders('Content-Type', 'text/xml');
      $response = $client->request();

      if ($response->getStatus() == 201) {
        $invite_codes[$contact_id] = $code;
        $sent++;
      }
    }

    return $invite_codes;
  }

  public function sendLastfmInvites($token, $contact_ids, $page_id = null)
  {
    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    $sk = $token->getParam('oauth_token_secret');

    $api_key = $settings->getSetting('inviter.lastfm.api.key');

    $secret = $settings->getSetting('inviter.lastfm.secret');


    $session = new Zend_Session_Namespace('inviter');
    $message = $session->__get('message');
    $invitesTbl = Engine_Api::_()->getDbtable('invites', 'inviter');

    $sent = 0;

    $viewer = Engine_Api::_()->user()->getViewer();

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    foreach ($contact_ids as $friend) {

      do {
        $invite_code = substr(md5(rand(0, 999) . $friend), 10, 7);
        $code_check = $invitesTbl->select()->where('code = ?', $invite_code);
      } while (null !== $invitesTbl->fetchRow($code_check));

      if (is_null($page_id))
        $inviteUrl = $this->getInvitationUrl($invite_code, null);
      else
        $inviteUrl = $this->getInvitationUrl($invite_code, null, $page_id);

      $message .= ' ' . $inviteUrl;

      try {
        $invitation = array(
          'user_id' => $viewer->getIdentity(),
          'sender' => trim($token->getParam('user_id')),
          'recipient' => trim($friend),
          'recipient_name' => trim($friend),
          'provider' => strtolower('Last.fm'),
          'code' => trim($invite_code),
          'sent_date' => new Zend_Db_Expr('NOW()'),
          'message' => trim($message),
        );

        if (!$invitesTbl->updateInvitation($invitation)) {
          $invitesTbl->insertInvitation($invitation);
        }
        if (trim($invite_code) != '') {
          $viewer->invites_used++;
          $viewer->save();
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
      }

      $method = 'user.shout';
      $params = array();
      $params['message'] = $message;
      $params['user'] = $friend;
      $params['method'] = $method;
      $params['api_key'] = $api_key;
      $params['sk'] = $sk;

      $lastfm = Engine_Api::_()->loadClass('Inviter_Plugin_Lastfm');
      $api_sig = $lastfm->sig($params, $secret);
      $params['api_sig'] = $api_sig;

      $result = $lastfm->make_request($params);
      $error = $result->error . '';
      if (trim($error) != '')
        $sent++;
    }
    return $sent;
  }

  public function sendOrkutInvites2($token, $contact_ids, $page_id = null,
                                    $captcha_value = null, $captcha_token = null)
  {
    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    $session = new Zend_Session_Namespace('inviter');
    $message = $session->__get('message');
    $invitesTbl = Engine_Api::_()->getDbtable('invites', 'inviter');

    $sent = 0;

    $viewer = Engine_Api::_()->user()->getViewer();

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $messages = array();
    if ($captcha_token && $captcha_value) {
      $params = array();
      $params['userId'] = array("@me");
      $params['groupId'] = "@self";
      $params['captchaAnswer'] = $captcha_value;
      $params['captchaToken'] = $captcha_token;

      $captcha_object = array();
      $captcha_object['method'] = 'captcha.answer';
      $captcha_object['params'] = $params;

      $messages[] = $captcha_object;
    }
    $invites = array();
    foreach ($contact_ids as $friend) {
      $name = $this->getOrkutFriendInfo($token, $friend);
      do {
        $invite_code = substr(md5(rand(0, 999) . $friend), 10, 7);
        $code_check = $invitesTbl->select()->where('code = ?', $invite_code);
      } while (null !== $invitesTbl->fetchRow($code_check));

      if (is_null($page_id))
        $inviteUrl = $this->getInvitationUrl($invite_code, null);
      else
        $inviteUrl = $this->getInvitationUrl($invite_code, null, $page_id);

      $invites[] = array(
        'user_id' => $viewer->getIdentity(),
        'sender' => trim($token->getParam('screen_name')),
        'recipient' => trim($friend),
        'recipient_name' => $name,
        'provider' => 'orkut',
        'code' => trim($invite_code),
        'sent_date' => new Zend_Db_Expr('NOW()'),
        'message' => trim($message),
      );
      $message_object = array('method' => 'messages.create',
        'params' => array('userId' => array($friend),
          'groupId' => '@friends',
          'message' => array('recipients' => array(1),
            'body' => $message . ' ' . $inviteUrl,
            'title' => 'sent at ' . strftime('%X')
          ),
          'messageType' => 'public_message')
      );
      $messages[] = $message_object;
    }

    $params_string = json_encode($messages);

    $client = $token->getHttpClient($this->getProviderConfig('orkut'));
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
    $error = false;
    $error_array = array();
    if ($captcha_token && $captcha_value) {
      $error = isset($content[0]->error) || isset($content[1]->error);
      $error_array = array('captcha_token' => $content[1]->error->data->captchaToken, 'captcha_url' => $content[1]->error->data->captchaUrl);
    } else {
      $error = isset($content[0]->error);
      $error_array = array('captcha_token' => $content[0]->error->data->captchaToken, 'captcha_url' => $content[0]->error->data->captchaUrl);
    }

    if ($error) {
      return $error_array;
    }
    foreach ($invites as $invitation) {
      try {
        if (!$invitesTbl->updateInvitation($invitation)) {
          $invitesTbl->insertInvitation($invitation);
          $sent++;
        }
        if (trim($invite_code) != '') {
          $viewer->invites_used++;
          $viewer->save();
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
      }

    }

    return $sent;
  }

  public function getOrkutCaptcha($token, $url = null)
  {
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $params = array(
      'oauth_nonce' => md5(microtime() . mt_rand()),
      'oauth_version' => '1.0',
      'oauth_timestamp' => time(),
      'oauth_consumer_key' => $settings->getSetting('inviter.gmail.consumer.key', false),
      'oauth_token' => urlencode($token->getParam('oauth_token')),
      'oauth_signature_method' => 'HMAC-SHA1'
    );

    $signature = $this->build_orkut_signature($params,
      'http://www.orkut.com/social/pages/captcha',
      $token->getParam('oauth_token_secret'),
      $settings->getSetting('inviter.gmail.consumer.secret', false));

    $params['oauth_signature'] = urlencode($signature);

    $c_url = 'http://www.orkut.com/social/pages/captcha?';
    //        $c_url = 'http://www.orkut.com' . $url . '&';

    foreach ($params as $k => $v) {
      $c_url .= $k . '=' . ($v) . '&';
    }
    $c_url = substr($c_url, 0, count($c_url) - 2);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $c_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'osapi 1.0');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, Array('Content-type: image/jpeg '));
    $response = curl_exec($ch);

    $result = false;
    $uid = substr(md5(rand(1111, 9999)), 0, 15);
    $img_path = "/temporary/package/archives/" . $uid . ".jpg";
    if ($response) {
      $img_name = getcwd() . $img_path;
      $fp = fopen($img_name, "w");
      $result = fwrite($fp, $response);
      fclose($fp);
    }
    if ($result)
      return $img_path;
    return false;
  }

  public function checkOrkutCaptcha($captcha_value, $captcha_token, $token)
  {
    if (!$captcha_value || !$captcha_token || !$token) {
      return false;
    }
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $params = array();
    $params['userId'] = array("@me");
    $params['groupId'] = "@self";
    $params['captchaAnswer'] = $captcha_value;

    $params['captchaToken'] = $captcha_token;

    $p = array();
    $p['method'] = 'captcha.answer';
    $p['params'] = $params;

    $params_string = json_encode($p);
    $header_auth = "Authorization:OAuth";
    $oauth_params = array(
      'oauth_nonce' => md5(microtime() . mt_rand()),
      'oauth_version' => '1.0',
      'oauth_timestamp' => time(),
      'oauth_consumer_key' => $settings->getSetting('inviter.gmail.consumer.key', false),
      'oauth_token' => urlencode($token->getParam('oauth_token')),
      'oauth_signature_method' => 'HMAC-SHA1'
    );
    $bodyHash = base64_encode(sha1($params_string, true));
    $oauth_params['oauth_body_hash'] = $bodyHash;
    foreach ($oauth_params as $k => $v) {
      $header_auth .= ' ' . $k .= '="' . $v . '",';
    }
    $header_auth = substr($header_auth, 0, count($header_auth) - 2);
    $header_content_type = 'Content-Type: application/json';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://www.orkut.com/social/rpc');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'osapi 1.0');
    //		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array($header_auth, $header_content_type));


    $data = @curl_exec($ch);
    //        print_die($data);
    //          $client = $token->getHttpClient($this->getProviderConfig('orkut'));
    //          $client->setUri('http://www.orkut.com/social/rpc');
    //          $client->setMethod(Zend_Http_Client::POST);
    ////          $client->setRawData($params_string);
    //          $client->setParameterPost($p);
    //          $client->setHeaders('Content-Type: application/json');
    //print_die($client);
    //          $response = $client->request();
    //          $status = $response->getStatus();
    //print_die($response);
    //          if ($status != 200) {
    //            return false;
    //          }
    //            return true;
  }

  private function getOrkutFriendInfo($token, $user_id)
  {
    if (!$token)
      return false;

    $params = array();
    $params['userId'] = $user_id;
    $p = array();
    $p['method'] = 'people.get';
    $p['id'] = $user_id;
    $p['params'] = $params;

    $params_string = json_encode($p);

    $client = $token->getHttpClient($this->getProviderConfig('orkut'));
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


    if (isset($content->data->name)) {
      return $content->data->name->givenName . ' ' . $content->data->name->familyName;
    } else {
      return 'Error';
    }
  }

  public function sendMyspaceInvites($token, $contact_ids, $page_id = null)
  {
    $session = new Zend_Session_Namespace('inviter');
    $message = $session->__get('message');
    $invitesTbl = Engine_Api::_()->getDbtable('invites', 'inviter');

    $sent = 0;

    $viewer = Engine_Api::_()->user()->getViewer();

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    foreach ($contact_ids as $contact_id) {

      do {
        $invite_code = substr(md5(rand(0, 999) . $contact_id), 10, 7);
        $code_check = $invitesTbl->select()->where('code = ?', $invite_code);
      } while (null !== $invitesTbl->fetchRow($code_check));

      if (is_null($page_id))
        $inviteUrl = $this->getInvitationUrl($invite_code, null);
      else
        $inviteUrl = $this->getInvitationUrl($invite_code, null, $page_id);

      $message .= ' ' . $inviteUrl;
      try {
        $invitation = array(
          'user_id' => $viewer->getIdentity(),
          'sender' => trim($token->getParam('user_id')),
          'recipient' => trim($contact_id),
          'recipient_name' => trim($contact_id),
          'provider' => strtolower('MySpace'),
          'code' => trim($invite_code),
          'sent_date' => new Zend_Db_Expr('NOW()'),
          'message' => trim($message),
        );

        if (!$invitesTbl->updateInvitation($invitation)) {
          $invitesTbl->insertInvitation($invitation);
        }

        if (trim($invite_code) != '') {
          $viewer->invites_used++;
          $viewer->save();
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
      }
      // Код отправки
      $templateParameters = array('content' => 'Notification from Kirill', 'button0_surface' => 'canvas', 'button0_label' => 'Go To App', 'button1_surface' => 'appProfile', 'button1_label' => 'Go To App Profile');
      $sb = '[';
      $n = count($templateParameters);
      $i = 0;
      foreach ($templateParameters as $key => $val) {
        $sb .= '{"key":"' . $key . '","value":"' . $val . '"}';
        $i++;
        if ($i == $n)
          $sb .= ']';
        else
          $sb .= ',';
      }
      $my_id = $token->getParam('user_id');
      $mediaItem = 'http://api.myspace.com/v1/users/' . $my_id;
      $body = '{"recipientIds":["' . $contact_id . '"], "templateParameters":' . $sb . ', "mediaItems":[{"msMediaItemUri":"' . $mediaItem . '"}]}';
      $config = $this->getMySpaceConfig(5, $token->getParam('oauth_token'), $token->getParam('oauth_token_secret'), $my_id);
      $result = $this->myspace_request($config, 'rest', 'POST', $body);
    }
    return $sent;
  }

  public function sendMailruInvites($token, $contact_ids, $page_id = null)
  {
    $session = new Zend_Session_Namespace('inviter');
    $message = $session->__get('message');
    $invitesTbl = Engine_Api::_()->getDbtable('invites', 'inviter');

    $sent = 0;

    $viewer = Engine_Api::_()->user()->getViewer();

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    foreach ($contact_ids as $contact_id) {

      do {
        $invite_code = substr(md5(rand(0, 999) . $contact_id), 10, 7);
        $code_check = $invitesTbl->select()->where('code = ?', $invite_code);
      } while (null !== $invitesTbl->fetchRow($code_check));

      if (is_null($page_id))
        $inviteUrl = $this->getInvitationUrl($invite_code, null);
      else
        $inviteUrl = $this->getInvitationUrl($invite_code, null, $page_id);

      $message .= ' ' . $inviteUrl;
      try {
        $invitation = array(
          'user_id' => $viewer->getIdentity(),
          'sender' => trim($token->getParam('user_id')),
          'recipient' => trim($contact_id),
          'recipient_name' => trim($contact_id),
          'provider' => strtolower('MySpace'),
          'code' => trim($invite_code),
          'sent_date' => new Zend_Db_Expr('NOW()'),
          'message' => trim($message),
        );

        if (!$invitesTbl->updateInvitation($invitation)) {
          $invitesTbl->insertInvitation($invitation);
        }

        if (trim($invite_code) != '') {
          $viewer->invites_used++;
          $viewer->save();
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
      }
      $mail_plugin = Engine_Api::_()->loadClass('Inviter_Plugin_MyMail');
      $mail_plugin->init();

      $result = $mail_plugin->sendMessage($contact_id, $token->getParam('oauth_token'), $message);
      if ($result)
        $sent++;
    }
    return $sent;
  }

  public function sendTwitterInvites($token, $contact_ids, $page_id = null)
  {
    if (!$token && !$contact_ids) {
      return false;
    }

    $session = new Zend_Session_Namespace('inviter');
    $message = $session->__get('message');

    $sended_ids = $session->__get('sended_ids');
    if (!$sended_ids) {
      $sended_ids = array();
    }

    $sent = 0;
    $invite_codes = array();
    foreach ($contact_ids as $contact_id) {
      if (in_array($contact_id, $sended_ids)) {
        continue;
      }
      if ($sent >= 100) {
        $session->__set('sended_ids', $sended_ids);
        return array('result' => $invite_codes, 'continue' => true);
      }
      $sended_ids[] = $contact_id;

      $code = substr(md5(rand(0, 999) . $contact_id), 10, 7);
      if (is_null($page_id))
        $url = $this->getInvitationUrl($code, null);
      else
        $url = $this->getInvitationUrl($code, null, $page_id);
      $body = $message . ' ' . $url;

      $client = $token->getHttpClient($this->getProviderConfig('twitter'));

      $client->setUri('http://api.twitter.com/1/direct_messages/new.json');
      $client->setMethod(Zend_Http_Client::POST);
      $client->setParameterPost('user_id', $contact_id);

      $client->setParameterPost('text', $body);

      $response = $client->request();

      if ($response->getStatus() == 200) {
        $invite_codes[$contact_id] = $code;
        $sent++;
      }
    }

    return $invite_codes;
  }

  public function sendMailServiceInvites($token, $contact_ids, $provider = 'gmail', $page_id = null)
  {
    if (!$token && !$contact_ids) {
      return false;
    }

    $session = new Zend_Session_Namespace('inviter');
    $message = $session->__get('message');
    $invitesTbl = Engine_Api::_()->getDbtable('invites', 'inviter');

    $sent = 0;
    $sent_date = new Zend_Db_Expr('NOW()');

    $viewer = Engine_Api::_()->user()->getViewer();
    $contact_list = $this->getContacts($token, $provider);

    foreach ($contact_ids as $contact_id)
    {
      if (!isset($contact_list[$contact_id])) {
        continue;
      }

      do {
        $invite_code = substr(md5(rand(0, 999) . $contact_id), 10, 7);
        $code_check = $invitesTbl->select()->where('code = ?', $invite_code);
      } while (null !== $invitesTbl->fetchRow($code_check));

      $recipient = $contact_id;
      $recipient_name = $contact_list[$contact_id]['name'];

      if (is_null($page_id))
        $inviteUrl = $this->getInvitationUrl($invite_code, null);
      else
        $inviteUrl = $this->getInvitationUrl($invite_code, null, $page_id);

      $inviteUrl = '<a href="' . $inviteUrl . '">' . $inviteUrl . '</a>';

      $message = str_replace('[invite_url]', $inviteUrl, $message);

      // insert the invite into the database
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try
      {
        if ($provider == 'hotmail' || $provider == 'foursquare' || $provider == 'yahoo')
          $sender = trim($token->getParam('user_id'));
        else
          $sender = trim($token->getParam('object_id'));

        $invitation = array(
          'user_id' => $viewer->getIdentity(),
          'sender' => $sender,
          'recipient' => trim($recipient),
          'recipient_name' => trim($recipient_name),
          'provider' => strtolower($provider),
          'code' => trim($invite_code),
          'sent_date' => $sent_date,
          'message' => trim($message),
        );

        if (!$invitesTbl->updateInvitation($invitation)) {
          $invitesTbl->insertInvitation($invitation);
        }
        if (trim($invite_code) != '') {
          $viewer->invites_used++;
          $viewer->save();
        }
        $from_sender = ($viewer && $viewer->getIdentity() != 0)
          ? $viewer->getTitle()
          : $session->sender;
        if (!is_null($page_id)) {
          $page = Engine_Api::_()->getItem('page', $page_id);
        }
        $mail_settings = (!is_null($page_id)) ?
          array(
            'from' => $from_sender,
            'from_email' => $token->getParam('object_id'),
            'to' => $recipient,
            'message' => $message,
            'link' => $inviteUrl,
            'page_title' => $page->getTitle(),
          )
          : array(
            'from' => $from_sender,
            'from_email' => $token->getParam('object_id'),
            'to' => $recipient,
            'message' => $message,
            'code' => $invite_code,
            'link' => $inviteUrl,
          );
        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $mail_settings['queque'] = $settings->getSetting('inviter.queque', true);
        // send email

        Engine_Api::_()->getApi('mail', 'core')->sendSystem($recipient, 'inviter', $mail_settings);

        // mail sent, so commit
        $sent++;
        $db->commit();
      } catch (Zend_Mail_Transport_Exception $e) {
        $db->rollBack();
      }
    }

    Engine_Api::_()->getDbtable('statistics', 'inviter')->increment('inviter.sents', $sent);

    return $sent;
  }

  public function getInvitationUrl($code, $email = null, $page_id = null)
  {
    $router = Zend_Controller_Front::getInstance()->getRouter();
    $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    //print_die($page_id);
    if ($page_id) {
      $page = Engine_Api::_()->getItem('page', $page_id);
      if (!is_null($page)) {
        $invite_url = $host_url . $page->getHref();
        return $invite_url;
      }
    }
    $params = array('module' => 'inviter', 'controller' => 'signup', 'code' => $code);
    if ($email) {
      $params['email'] = $email;
    }
    $invite_url = $host_url . $router->assemble($params, 'default', true);

    return $invite_url;
  }

  public function checkTwitterMessageLength($message, $page_id = null)
  {
    if (!$message) {
      return true;
    }

    $code = substr(md5(rand(0, 999)), 10, 7);

    if (!is_null($page_id)) {
      $url = $this->getInvitationUrl($code, null, $page_id);
    } else {
      $url = $this->getInvitationUrl($code, null);
    }


    return (iconv_strlen($message . ' ' . $url, 'UTF-8') <= 140);
  }

  public function findContacts($provider)
  {

    $session = new Zend_Session_Namespace('inviter');

    if (!$session->__isset('account_info') || !$session->__get('account_info')) {
      return false;
    }

    $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');

    $access_token_params = Zend_Json::decode($session->__get('account_info'), Zend_Json::TYPE_ARRAY);
    $token = $tokensTbl->getUserTokenByArray($access_token_params);

    if (!$token) {
      return false;
    }
    try {
      $contacts = $this->getNoneFriendContacts($token, $provider, 1000);
    } catch (Exception $e) {
      $contacts = false;
    }

    if ($contacts && isset($contacts['se_users']) && count($contacts['se_users']) > 0) {
      $session->__set('members', 1);
    }

    try {
      $contacts = $this->getNoneMemberContacts($token, $provider, 1000);
    } catch (Exception $e) {
      $contacts = false;
    }

    if ($contacts && count($contacts) > 0) {
      $session->__set('contacts', 1);
    }
  }
}
