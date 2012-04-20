<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: BlockController.php 9604 2012-01-17 21:48:02Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_BlockController extends Core_Controller_Action_User
{
  public function init()
  {
    $this->_helper->requireUser();
  }
  
  public function addAction()
  {
    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);
    if( !$user_id ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // Make form
    $this->view->form = $form = new User_Form_Block_Add();

    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('block', 'user')->getAdapter();
    $db->beginTransaction();

    try {
      $viewer = Engine_Api::_()->user()->getViewer();
      $user = Engine_Api::_()->getItem('user', $user_id);
      
      $viewer->addBlock($user);
      if( $user->membership()->isMember($viewer, null) ) {
        $user->membership()->removeMember($viewer);
      }
      
      try {
        // Set the requests as handled
        $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
          ->getNotificationBySubjectAndType($viewer, $user, 'friend_request');
        if( $notification ) {
          $notification->mitigated = true;
          $notification->read = 1;
          $notification->save();
        }
        $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
            ->getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
        if( $notification ) {
          $notification->mitigated = true;
          $notification->read = 1;
          $notification->save();
        }
      } catch( Exception $e ) {}

      $db->commit();

      $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Member blocked');
      
      return $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Member blocked'))
      ));
    } catch( Exception $e ) {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }

  public function removeAction()
  {
    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);
    if( !$user_id ) {
      $this->view->status = false;
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // Make form
    $this->view->form = $form = new User_Form_Block_Remove();

    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->view->status = false;
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('block', 'user')->getAdapter();
    $db->beginTransaction();

    try {
      $viewer = Engine_Api::_()->user()->getViewer();
      $user = Engine_Api::_()->getItem('user', $user_id);

      $viewer->removeBlock($user);

      $db->commit();

      $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Member unblocked');

      return $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Member unblocked'))
      ));
    } catch( Exception $e ) {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }
  
  public function successAction()
  {
    // This is a smoothbox
    $this->_helper->layout->setLayout('default-simple');
    $this->view->messages = $this->_getParam('messages', array());
  }
}