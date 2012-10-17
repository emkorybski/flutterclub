<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Popups.php 2010-07-02 19:54 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Controller_Action_Helper_Invite extends Zend_Controller_Action_Helper_Abstract
{
	public function preDispatch()
	{
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();

    if ($module != 'user' || $controller != 'signup' || $action != 'index') {
      return;
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');
    // check settings
    if ($settings->getSetting('user.signup.inviteonly') == 0) {
      return;
    }

    $session = new Zend_Session_Namespace('inviter');

    $invite_code = ($session->__isset('invite_code')) ? $session->__get('invite_code') : false;
    $invite_email = ($session->__isset('invite_email')) ? $session->__get('invite_email') : false;
    $tmp_invite_row = ($session->__isset('tmp_invite_row')) ? $session->__get('tmp_invite_row') : false;

    $code = isset($_REQUEST['code']) ? $_REQUEST['code'] : false;
    $email = isset($_REQUEST['email']) ? $_REQUEST['email'] : false;

    $inviterTbl = Engine_Api::_()->getDbTable('invites', 'inviter');
    $coreTbl = Engine_Api::_()->getDbtable('invites', 'invite');

    if ($invite_code && !$code && !$email && $tmp_invite_row) {
      $coreSel = $coreTbl->select()
        ->orWhere("code = ?", $invite_code)
        ->orWhere("recipient = ?", $invite_email);

      $coreInvites = $coreTbl->fetchAll($coreSel);

      foreach ($coreInvites as $coreInvite) {
        $coreInvite->delete();
      }

      return;
    }

    if (!$invite_code || !$code || !$email || $invite_code != $code) {
      return;
    }

    $inviterSel = $inviterTbl->select()
      ->where('code = ?', $invite_code)
      ->where('new_user_id = ?', 0);

    $invites = $inviterTbl->fetchAll($inviterSel);
    if ($invites->count() == 0) {
      return;
    }

    $invite = $invites->getRow(0);

    if (!$invite) {
      return;
    }

    $coreSel = $coreTbl->select()
      ->where('code = ?', $invite_code)
      ->where('new_user_id = ?', 0);

    if ($coreTbl->fetchRow($coreSel)) {
      return;
    }

    $inviteCoreItem = $coreTbl->createRow();
    $inviteCoreItem->setFromArray(array(
      'user_id' => $invite->user_id,
      'recipient' => $email,
      'code' => $invite_code,
      'timestamp' => $invite->sent_date,
    ));

    $inviteCoreItem->save();

    $session->__set('tmp_invite_row', true);

    if (!$invite_email) {
      $session->__set('invite_email', $email);
    }
	}
}