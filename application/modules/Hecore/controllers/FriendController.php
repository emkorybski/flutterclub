<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: FriendController.php 2010-07-02 19:52 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_FriendController extends Core_Controller_Action_Standard
{
  public function changeIppAction()
  {
    $value = (int)$this->_getParam('value');
    $setting = 'hecore.friend.ipp';
    
    if (!$value) {
      $this->view->html = 'Wrong parameters pass through request.';
      return ;
    }

    $table = Engine_Api::_()->getDbTable('user_settings', 'hecore');
    $db = $table->getAdapter();
    $viewer = Engine_Api::_()->user()->getViewer();
    $api = Engine_Api::_()->hecore();

    $select = $table->select();

    $select
      ->where('setting = ?', $setting)
      ->where('user_id = ?', $viewer->getIdentity());

    $row = $table->fetchRow($select);

    if (!$row) {
      $row = $table->createRow();
    }

    $db->beginTransaction();
    
    try {
      $row->value = $value;
      $row->setting = $setting;
      $row->user_id = $viewer->getIdentity();
      $row->save();
      
      $db->commit();
    }
    catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->friends = $friends = $api->getFriends(array('sort_list' => $table->getSetting('hecore.friend.list', $viewer->getIdentity())), $viewer);
    $friends->setItemCountPerPage($value);
    
    $this->view->html = $this->view->render('_friends_list.tpl');
  }

  public function changePrivacyAction()
  {
    $value = $this->_getParam('value');
    $setting = 'hecore.friend.privacy';

    if (!$value) {
      $this->view->html = 'Wrong parameters pass through request.';
      return;
    }

    $table = Engine_Api::_()->getDbTable('user_settings', 'hecore');
    $db = $table->getAdapter();
    $viewer = Engine_Api::_()->user()->getViewer();

    $select = $table->select();

    $select
      ->where('setting = ?', $setting)
      ->where('user_id = ?', $viewer->getIdentity());

    $row = $table->fetchRow($select);

    if (!$row) {
      $row = $table->createRow();
    }

    $db->beginTransaction();

    try {
      $row->value = $value;
      $row->setting = $setting;
      $row->user_id = $viewer->getIdentity();
      $row->save();

      $db->commit();
    }
    catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function saveFriendsAction()
  {
    $value = array_unique($this->_getParam('value'));
    $setting = 'hecore.friend.list';

    if (!$value) {
      $this->view->html = 'Wrong parameters pass through request.';
      return ;
    }

    $table = Engine_Api::_()->getDbTable('user_settings', 'hecore');
    $db = $table->getAdapter();
    $viewer = Engine_Api::_()->user()->getViewer();
    $api = Engine_Api::_()->hecore();

    $select = $table->select();

    $select
      ->where('setting = ?', $setting)
      ->where('user_id = ?', $viewer->getIdentity());

    $row = $table->fetchRow($select);

    if (!$row) {
      $row = $table->createRow();
    }

    $db->beginTransaction();

    try {
      $row->value = implode(',', $value);
      $row->setting = $setting;
      $row->user_id = $viewer->getIdentity();
      $row->save();

      $db->commit();
    }
    catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->friends = $friends = $api->getFriends(array('sort_list' => $table->getSetting('hecore.friend.list', $viewer->getIdentity())), $viewer);
    $friends->setItemCountPerPage($table->getSetting('hecore.friend.ipp', $viewer->getIdentity()));

    $this->view->html = $this->view->render('_friends_list.tpl');
  }
}