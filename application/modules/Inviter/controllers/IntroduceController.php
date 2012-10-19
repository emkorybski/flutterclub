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

class Inviter_IntroduceController extends Core_Controller_Action_Standard
{
  public function init()
  {
    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext->addActionContext('ajax-request', 'json')
      ->initContext();

    if(!$this->_helper->requireAuth()->setAuthParams('inviter', null, 'use')->isValid()) // @todo add new setting?
    {
      return;
    }
  }
  
  public function editAction()
  {
    if (!Engine_Api::_()->core()->hasSubject()) {
      // Can specifiy custom id
      $id = $this->_getParam('id', null);
      $subject = null;

      if (null === $id) {

        $subject = $this->_helper->api()->user()->getViewer();
        $this->_helper->api()->core()->setSubject($subject);

      } else {

        $subject = $this->_helper->api()->user()->getUser($id);
        $this->_helper->api()->core()->setSubject($subject);

      }
    }

    if (!empty($id)) {
      $params = array('params' => array('id' => $id));
    } else {
      $params = array();
    }

    // Set up navigation
    $this->view->navigation = $navigation = $this->_helper->api()
      ->getApi('menus', 'core')
      ->getNavigation('user_edit', array('params'=>array('id'=>$id)), 'user_profile_introduce');

    $introductionTbl = Engine_Api::_()->getDbTable('introductions', 'inviter');
    $userIntroduce = $introductionTbl->getUserIntroduction($subject->getIdentity());

    $this->view->form = $form = new Inviter_Form_IntroduceEdit();

    if (!$this->getRequest()->isPost()) {

      $form->populate($userIntroduce->toArray());

    } else {

      if (!$form->isValid($this->getRequest()->getPost())) {
        return;
      }

      $db = $introductionTbl->getAdapter();
      $db->beginTransaction();

      try {
        $userIntroduce->body =  $form->getElement('body')->getValue();
        $userIntroduce->publish = $form->getElement('publish')->getValue();
        $userIntroduce->save();

        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

    }
  }

  public function ajaxRequestAction()
  {
    $task = $this->_getParam('task', false);

    $viewer = Engine_Api::_()->user()->getViewer();

    // get user introduction
    $introductionTbl = Engine_Api::_()->getDbTable('introductions', 'inviter');
    $userIntroduce = $introductionTbl->getUserIntroduction($viewer->getIdentity());

    $this->view->result = 0;

    if ($task == 'save') {

      $body = $this->_getParam('body', '');

      if ($viewer->getIdentity() == 0 || !$body) {
        return;
      }
      
      $userIntroduce->setFromArray(array('body' => $body, 'publish' => 1));
      $userIntroduce->save();
      
      $this->view->result = 1;

    }
    elseif ($task == 'hide') {

      $userIntroduce->displayed_date = new Zend_Db_Expr('NOW()');
      $userIntroduce->save();

      $this->view->result = 1;
      
    }
    elseif ($task == 'hide_member') {

      $user_id = $this->_getParam('user_id');

      $exclude_ids = array();


      if ($userIntroduce->exclude_ids) {
        $exclude_ids = explode(',', $userIntroduce->exclude_ids);
      }

      $exclude_ids[] = $user_id;
      $userIntroduce->exclude_ids = implode(',', $exclude_ids);
      $userIntroduce->save();

      $memberIntroduce = $introductionTbl->getRandomIntroduction($viewer->getIdentity(), $exclude_ids);

      if ($memberIntroduce === null) {
        return;
      }

      $this->view->memberIntroduce = $memberIntroduce;
      $this->view->memberItem = Engine_Api::_()->getItem('user', $memberIntroduce->user_id);

      $inviterApi = Engine_Api::_()->getApi('core', 'inviter');

      $this->view->mutual_friend_count = $inviterApi->getMutualFriendCount($memberIntroduce->user_id);
      $this->view->mutual_like_count = $inviterApi->getMutualLikeCount($memberIntroduce->user_id);

    if ($this->view->mutual_like_count) {
      $params = array('poster_type' => $this->view->memberItem->getType(), 'poster_id' => $this->view->memberItem->getIdentity());

      $select = Engine_Api::_()->like()->getLikesSelect($params);
      $select->where('like1.resource_type IN ("page", "user")');

      $this->view->likedMembersAndPages = Engine_Api::_()->like()->getTable()->fetchAll($select)->count();
    }      

      $this->view->result = 1;
      $this->view->html = $this->view->render('_member_introduce.tpl');

      unset($this->view->memberIntroduce);
      unset($this->view->memberItem);
    }
    elseif ($task == 'hide_more_friends') {
      $userIntroduce->more_friends_date = new Zend_Db_Expr('NOW()');
      $userIntroduce->save();

      $this->view->result = 1;
    }
    elseif ($task == 'add_friends') {
      $user_ids = $this->_getParam('users');

      if (!$user_ids || count($user_ids) == 0) {
        return;
      }

      $user_ids = (is_array($user_ids)) ? $user_ids : explode(',', $user_ids);
      $translate = Zend_Registry::get('Zend_Translate');
      $session = new Zend_Session_Namespace('inviter');

      $session->__set('user_ids', $user_ids);
      $error = Engine_Api::_()->getApi('openinviter', 'inviter')->sendRequests(false);

      if (!$error) {
        $message = $translate->_('INVITER_Your friend request has been sent.');
      } else {
        $message = $translate->_('INVITER_Friend request was not sent to some of selected members.');
      }

      $this->view->result = 1;
      $this->view->message = $message;
    }
  }
}
