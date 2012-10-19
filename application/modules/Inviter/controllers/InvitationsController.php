<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: InvitationsController.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_InvitationsController extends Core_Controller_Action_Standard
{

  public function init()
  {
    $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    if (!$this->_helper->api()->user()->getViewer()->getIdentity()) {
      $this->_redirect($host_url . $this->view->url(array(), 'default'));
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $auth_table = Engine_Api::_()->getDbTable('permissions', 'authorization');
    if (!$auth_table->isAllowed('inviter', $viewer, 'use')) {
      $this->_redirectCustom(array('route' => 'default'));
    }
  }

  public function indexAction()
  {
    $session = new Zend_Session_Namespace('inviter');

    if ($session->__isset('invites_del_msg')) {
      $this->view->has_msg = true;
      $this->view->msg = $session->__get('invites_del_msg');
      $this->view->msg_type = $session->__get('invites_del_msg_type');
      $session->__unset('invites_del_msg');
      $session->__unset('invites_del_msg_type');
    }

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('inviter_profile', array(), 'inviter_profile_invitation');

    $this->view->filter_form = $filter_form = new Inviter_Form_InvitesFilter();
    $table = $this->_helper->api()->getDbtable('invites', 'inviter');

    $user = Engine_Api::_()->user()->getViewer();

    $params['select'] = $table->select()->where('user_id = ? && new_user_id = 0 && sender !="" && recipient!="" ', $user->user_id)->order('sent_date Desc');
    $params['page'] = $this->_getParam('page', 1);
    $params['limit'] = 3;

    $params = array(
      'user_id' => $user->getIdentity(),
      'page' => $this->_getParam('page', 1),
      'ipp' => 10
    );

    if ($this->getRequest()->isPost() && $filter_form->isValid($this->_getAllParams())) {
      $params = array_merge($params, $this->_getAllParams());
    }

    // Make paginator
    $this->view->invites_paginator = $invites_paginator = Engine_Api::_()->getDbTable('invites', 'inviter')->getInvitesPaginator($params);
    $this->view->providers = Engine_Api::_()->inviter()->getIntegratedProviders();
    $this->view->fb_settings = Engine_Api::_()->inviter()->getFacebookSettings($this->view);
  }

  public function deleteAction()
  {
    if (!$this->_helper->api()->user()->getViewer()->getIdentity()) {
      $this->_redirect('inviter/index/send');
    }

    $id = $this->_getParam('id', null);
    $this->view->invitation = $invitation = Engine_Api::_()->inviter()->getInvitation($id);
    $this->view->form = $form = new Inviter_Form_Delete();

    if ($this->getRequest()->isPost()) {
      $invitation->delete();

      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'format' => 'smoothbox',
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('INVITER_Invitation deleted.'))
      ));
    }
  }

  public function deleteSelectedAction()
  {
    $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    if (!$this->_helper->api()->user()->getViewer()->getIdentity()) {
      $this->_redirect($host_url . $this->view->url(array(), 'inviter_invitations'));
    }
    $del_ids = array();

    foreach ($this->_getAllParams() as $key => $value) {
      if (strstr($key, 'invite')) {
        $tmp = explode('_', $key);
        if ($tmp[1] != 'all')
          $del_ids[] = $tmp[1];
      }
    }
    $session = new Zend_Session_Namespace('inviter');
    if (!empty($del_ids)) {
      $res = Engine_Api::_()->getDbTable('invites', 'inviter')->deleteInvitations($del_ids);
      if ($res) {
        $session->__set('invites_del_msg', $this->view->translate('INVITER_Invites deleted'));
        $session->__set('invites_del_msg_type', 'message');
      } else {
        $session->__set('invites_del_msg', $this->view->translate('INVITER_Invites not deleted'));
        $session->__set('invites_del_msg_type', 'error');
      }
    } else {
      $session->__set('invites_del_msg', $this->view->translate('INVITER_Invites not selected'));
      $session->__set('invites_del_msg_type', 'notice');
    }
    $this->_redirect($host_url . $this->view->url(array(), 'inviter_invitations'));

    //
    //        if ($this->getRequest()->isPost()) {
    //            $session = new Zend_Session_Namespace('inviter');
    //            if ($session->__isset('del_ids')) {
    //                $del_ids = $session->__get('del_ids', false);
    //                $session->__unset('del_ids');
    //
    //                $res = Engine_Api::_()->getDbTable('invites', 'inviter')->deleteInvitations($del_ids);

    //
    //
    //            } else {
    //                foreach ($this->_getAllParams() as $key => $value) {
    //                    if (strstr($key, 'invite')) {
    //                        $tmp = explode('_', $key);
    //                        if ($tmp[1] != 'all')
    //                            $del_ids[] = $tmp[1];
    //                    }
    //                }
    //                if (!$del_ids) {
    //                    $this->_redirect($host_url . $this->view->url(array(), 'inviter_invitations'));
    //                }
    //                $session->__set('del_ids', $del_ids);
    //            }
    //
    //        }
    //        $this->view->form = $form = new Inviter_Form_DeleteSelected();
  }

  public function sendnewAction()
  {
    $viewer = $this->_helper->api()->user()->getViewer();

    if (!$viewer->getIdentity()) {
      $this->_redirect('inviter/index/send');
    }

    $id = $this->_getParam('id', null);
    $this->view->form = $form = new Inviter_Form_Sendnew(array('params' => $id));

    $inv = $form->_inv->toArray();
    $providerApi = Engine_Api::_()->getApi('provider', 'inviter');
    $conf = $providerApi->getProviderConfig($form->_inv->provider);

    $this->view->invitation = $invitation = array(
      'sender' => $form->_inv->sender,
      'recipient' => $form->_inv->recipient,
      'recipient_name' => $form->_inv->recipient_name,
      'provider' => $form->_inv->provider);

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
      $this->_success = $form->sendNewInvite();
    }

    if ($this->getRequest()->isPost() && !$form->isErrors()) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'format' => 'smoothbox',
        'messages' => array($this->_success)
      ));
    }
  }

  public function resendAction()
  {
    $invite_id = $this->_getParam('invite_id', false);
    $viewer = $this->_helper->api()->user()->getViewer();
    $inviterApi = Engine_Api::_()->getApi('core', 'inviter');

    $this->view->invitation = $invitation = $inviterApi->getInvitation($invite_id);
    $this->view->current_page = $current_page = $this->_getParam('page', '');

    if (!$viewer->getIdentity() || !$invite_id || !$invitation) {
      $this->_redirect('inviter/index/send');
    }

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('inviter_profile', array(), 'inviter_profile_invitation');

    $skip = $this->_getParam('skip', false);
    $invite_code = $this->_getParam('code', '');
    $recipient_id = $this->_getParam('recipient_id', false);

    if ($skip) {

      if ($recipient_id) {
        $invitation->setFromArray(array('code' => $invite_code, 'sent_date' => new Zend_Db_Expr('NOW()')));
        $invitation->save();
      }

      $this->_redirect($this->view->url(array('page' => $current_page), 'inviter_invitations', true));
    }

    $invite_code = substr(md5(rand(0, 999) . $viewer->getIdentity()), 10, 7);

    $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    $this->view->action_url = $host_url . $this->view->url(array(
      'module' => 'inviter',
      'controller' => 'invitations',
      'action' => 'resend',
      'invite_id' => $invite_id,
      'page' => $current_page,
      'skip' => 1,
      'code' => $invite_code
    ), 'default', true);

    $this->view->invite_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'signup', 'code' => $invite_code), 'default', true);


    if ($invitation->provider == 'facebook') {
      $facebook = Inviter_Api_Provider::getFBInstance();
      $fb_user_id = Inviter_Api_Provider::getFBUserId();

      $this->view->appId = $facebook->getAppId();
      $this->view->init_fb_app = $inviterApi->checkInitFbApp();

      if (!$this->view->appId && !$fb_user_id) {
        return;
      }

      if ($fb_user_id && $fb_user_id != $invitation->sender) {
        $this->view->fb_logout = true;
      }
    }
  }
}