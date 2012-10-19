<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ReferralsController.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_ReferralsController extends Core_Controller_Action_Standard
{

  public function indexAction()
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

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('inviter_profile', array(), 'inviter_profile_referral');
    $this->view->filter_form = $filter_form = new Inviter_Form_ReferralsFilter();

    $params = array(
      'user_id' => $viewer->getIdentity(),
      'ipp' => 10,
      'page' => $this->_getParam('page', 1)
    );

    if ($this->getRequest()->isPost() && $filter_form->isValid($this->_getAllParams())) {
      $params = array_merge($params, $this->_getAllParams());
    }

    $this->view->referrals_paginator = $referrals_paginator = Engine_Api::_()->getDbTable('invites', 'inviter')->getReferralsPaginator($params);

    $inviterTable = $this->_helper->api()->getDbtable('invites', 'inviter');

    $inviterSelect = $inviterTable->select('new_user_id')->where('user_id = ? && new_user_id != 0', $viewer->getIdentity());
    $user_ids = array();
    foreach ($inviterTable->fetchAll($inviterSelect) as $inviter)
    {
      $user_ids[] = $inviter->new_user_id;
    }
    if (count($user_ids) == 0)
      $user_ids = 0;
    else
      $user_ids = implode(', ', $user_ids);

    // Suggest integration
    $coreModulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
    $coreModuleSelect = $coreModulesTbl->select()
      ->where('name = ?', 'suggest')
      ->where('enabled = ?', 1);

    $this->view->suggest_enabled = false;
    if ($coreModulesTbl->fetchRow($coreModuleSelect)) {
      $this->view->suggest_enabled = true;
    }
  }

  public function referralAction()
  {
    $code = $this->_getParam('code');
    if (!$code) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    $invites_tbl = Engine_Api::_()->getDbTable('invites', 'inviter');
    $codes_tbl = Engine_Api::_()->getDbTable('codes', 'inviter');
    $sender_id = $codes_tbl->getUserId($code);
    if (!$sender_id) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $invitation_id = false;
    try {
      $invitation = array(
        'user_id' => $sender_id,
        'code' => trim($code),
        'provider' => 'link',
        'referred_date' => new Zend_Db_Expr('NOW()')
      );

      $invitation_id = $invites_tbl->insertReferralInvitation($invitation);
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
    }
    if (!$invitation_id) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    return $this->_helper->redirector->gotoRoute(array('module' => 'inviter', 'controller' => 'signup', 'code' => $code, 'sender' => $invitation_id), 'default', true);
  }
}