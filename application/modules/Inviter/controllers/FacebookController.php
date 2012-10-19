<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IntroduceController.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_FacebookController extends Core_Controller_Action_Standard
{
    public function init()
    {
        $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $this->_redirect($host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'index', 'action' => 'index'), 'default'));
    }

    public function indexAction()
    {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('inviter_profile', array(), 'inviter_facebook');
        $new_token = $this->_getParam('new_param', false);
        $this->view->code = $code = $this->_getParam('code', false);
        $this->view->state = $state = $this->_getParam('state', false);
        $this->view->logout = $logout = $this->_getParam('logout', false);
        if ($logout)
            return;
        $viewer = $this->_helper->api()->user()->getViewer();
        $session = new Zend_Session_Namespace('inviter');
        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $this->view->url = $url = $this->view->url(array('module' => 'inviter', 'controller' => 'ajax', 'action' => 'request'), 'default');
        //get user token
        $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');
        $tokenRow = $tokensTbl->findUserToken($viewer->getIdentity(), 'facebook');

        if ($tokenRow && !$new_token && !$state) {
            $this->view->tokenRow = $tokenRow;
            $this->view->askForm = $this->_generateForm($tokenRow);
            return;
        }


        $viewer = Engine_Api::_()->user()->getViewer();
        $inviterApi = Engine_Api::_()->getApi('core', 'inviter');

        $this->view->app_id = $app_id = $settings->getSetting('inviter.facebook.consumer.key', false);

        if (!$viewer->getIdentity() || !$app_id) {
            $this->_redirect('inviter');
        }


        $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');
        $tokenRow = $tokensTbl->getUserToken($viewer->getIdentity(), 'facebook');

        $fbApi = Engine_Api::_()->loadClass('Inviter_Plugin_fbApi');
        $app_id = $settings->getSetting('inviter.facebook.consumer.key', false);
        $secret = $settings->getSetting('inviter.facebook.consumer.secret', false);
        $fbApi->init($app_id, $secret);

        if ($code) {
            $url = $this->view->url(array('module' => 'inviter', 'controller' => 'facebook', 'action' => 'response', 'state' => true, 'code' => null), 'default');
            $redirect_url = $host_url . $url;
            $token = $fbApi->getAccessToken($redirect_url, $code);

            if ($token) {
                $account_info = $this->_getFBAccountInfo($token);
                if (!$account_info) {
                    $this->_redirect($this->view->url(array('module' => 'inviter', 'controller' => 'facebook', 'action' => 'index'), 'default'));
                }

                $tokensTbl->delete(array("user_id = {$account_info['user_id']}", "object_id = '{$account_info['object_id']}'", "provider = '{$account_info['provider']}'"));
                $tokensSel = $tokensTbl->select()
                    ->where('user_id = ?', $account_info['user_id'])
                    ->where('provider = ?', 'facebook')
                    ->where('active = ?', 1);

                $otherTokens = $tokensTbl->fetchAll($tokensSel);
                foreach ($otherTokens as $otherToken) {
                    $otherToken->active = 0;
                    $otherToken->save();
                }

                $tokenRow = $tokensTbl->createRow();
                $tokenRow->setFromArray($account_info);

                $tokenRow->save();
            } else {
                $this->_redirect($this->view->url(array('module' => 'inviter', 'controller' => 'facebook', 'action' => 'index', 'code' => null), 'default'));
            }
        }

        if ($state) {

            $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
            $providerApi = Engine_Api::_()->getApi('provider', 'inviter');
            $this->view->app_id = $settings->getSetting('inviter.facebook.consumer.key', false);

            $redirect_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'index', 'action' => 'facebookaftersend'), 'default');

            $this->view->redirect_url = $redirect_url;
            $this->view->invite_code = $invite_code = substr(md5(rand(0, 999) . time() . 'facebook'), 10, 7);
            $this->view->invitation_url = $providerApi->getInvitationUrl($invite_code, null, null);
            $this->view->host = $_SERVER['HTTP_HOST'];
            //          $this->view->host = 'kontroler.kg';

            $contact_list = $providerApi->getNoneMemberContacts($tokenRow, 'facebook', 5000);

            if ($contact_list === false) {
                $this->_redirectCustom(array('route' => 'inviter_general'));
            }
            $contacts = array();

            $fb_user_id = $fbApi->getMe($tokenRow->getParam('oauth_token'), true);

            if ($fb_user_id) {
                $exclude_ids = $inviterApi->getAlreadyMemberFbFriends($fb_user_id, $tokenRow->getParam('oauth_token'));
            }
            foreach ($contact_list as $contact_info) {

                if (!in_array($contact_info['id'], $exclude_ids)) {
                    $contact_info['email'] = $contact_info['id'];
                    $contacts[$contact_info['id']] = $contact_info;
                }
            }
            if (count($contacts) == 0) {
                $this->_redirectCustom(array('route' => 'inviter_general'));
            }

            $this->view->contacts = $contacts;
            return;
        }

    }

    public function responseAction()
    {
        $this->view->state = $state = $this->_getParam('state', false);
        $this->view->code = $state = $this->_getParam('code', false);
    }

    public function userContentAction()
    {
        $user_id = array_pop(explode('_', $this->_getParam('id', '')));

        if (!$user_id) {
            $this->view->error = 1;
            $this->view->message = 'Error happened.';
            return;
        }

        $inviterApi = Engine_Api::_()->getApi('core', 'inviter');
        $this->view->user = $user = Engine_Api::_()->getItem('user', $user_id);

        $this->view->mutual_friend_count = $inviterApi->getMutualFriendCount($user->user_id);
        $this->view->mutual_like_count = $inviterApi->getMutualLikeCount($user->user_id);

        if ($this->view->mutual_like_count) {
            $params = array('poster_type' => $user->getType(), 'poster_id' => $user->getIdentity());

            $select = Engine_Api::_()->like()->getLikesSelect($params);
            $select->where('like1.resource_type IN ("page", "user")');

            $this->view->likedMembersAndPages = Engine_Api::_()->like()->getTable()->fetchAll($select)->count();
        }

        if ($this->view->mutual_friend_count) {
            $this->view->paginator = $paginator = $inviterApi->getMutualFriends(array('user_id' => $user_id));
            $paginator->setItemCountPerPage(4);
        }

        $this->view->html = $this->view->render('_user_content.tpl');
    }

    public function joinAction()
    {
        $session = new Zend_Session_Namespace('inviter');

        $formSkipped = $this->_getParam('skip_inviter', false);
        $formSubmitted = $this->_getParam('submitFacebook', false);
        $contactIds = $this->_getParam('ids', array());
        $session->__set('inviterStep', 'sendInvitation');

        $inviterApi = Engine_Api::_()->getApi('core', 'inviter');
        /**
         * @var $providerApi Inviter_Api_Provider
         */
        $providerApi = Engine_Api::_()->getApi('provider', 'inviter');

        $facebook = Inviter_Api_Provider::getFBInstance();
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->appId = $facebook->getAppId();
        $this->view->init_fb_app = $inviterApi->checkInitFbApp();

        $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $this->view->invite_code = $invite_code = substr(md5(rand(0, 999) . $viewer->getIdentity()), 10, 7);
        $this->view->action_url = $host_url . $this->view->url(array('skip_inviter' => 1), 'inviter_facebook_signup');
        $this->view->invite_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'signup', 'code' => $invite_code), 'default', true);
        $this->view->exclude_ids = '';

        if ($formSkipped && $formSubmitted) {
            $this->view->submitForm = true;
            $this->view->skipForm = true;
            $this->view->form = $form = new Inviter_Form_Signup_Contacts();
            $form->setAction($this->view->url(array(), 'user_signup'));

            $this->view->contactIds = implode(',', $contactIds);
            $session->__set('custom_submit_fb', true);
            $session->__set('invite_code', $invite_code);

            return;

        } elseif ($formSkipped) {
            $this->view->skipForm = true;
            $this->view->form = $form = new Inviter_Form_Signup_Contacts();
            $form->setAction($this->view->url(array(), 'user_signup'));

            return;
        }

        $this->view->show_login_btn = true;

        if ($this->view->appId) {
            try {
                $fbUserInfo = $facebook->api('/me');
                if ($fbUserInfo) {
                    $this->view->show_login_btn = false;
                }
            }
            catch (Exception $e) {
                return;
            }
        }

        try {
            $fb_user_id = Inviter_Api_Provider::getFBUserId();

            if ($fb_user_id) {
                $exclude_ids = $inviterApi->getAlreadyMemberFbFriends($fb_user_id);
                $this->view->exclude_ids = ($exclude_ids) ? implode(',', $exclude_ids) : '';
            }
        } catch (Exception $e) {
            $this->view->exclude_ids = '';
        }
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

        $form->addElement('Button', 'ok', array(
            'onclick' => 'facebook_inviter.get_contacts("' . $this->view->url(array('module' => 'inviter', 'controller' => 'facebook', 'action' => 'index'), 'default') . '", 0);',
            'label' => 'INVITER_Continue'
        ));

        $params['action'] = 'request';
        $params['new'] = 1;
        $form->addElement('Cancel', 'cancel', array(
            'label' => 'INVTTER_Use another account',
            'link' => true,
            'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
            'href' => 'javascript:void(0);',
            'onclick' => 'facebook_inviter.request(1, "' . $this->view->url(array('module' => 'inviter', 'controller' => 'ajax', 'action' => 'request'), 'default') . '");',
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $form->addDisplayGroup(array('submit', 'cancel'), 'buttons');

        return $form;
    }

    private function _getFBAccountInfo($token)
    {
        $session = new Zend_Session_Namespace('inviter');
        $viewer = $this->_helper->api()->user()->getViewer();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $user_info = array(
            'user_id' => $viewer->getIdentity(),
            'provider' => 'facebook',
            'creation_date' => new Zend_Db_Expr('NOW()'),
            'active' => 1,
        );

        $res = $this->_getFacebookUserInfo($token);
        $user_info = array_merge($res, $user_info);

        return $user_info;
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
}