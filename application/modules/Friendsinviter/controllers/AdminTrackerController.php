<?php

class Friendsinviter_AdminTrackerController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('friendsinviter_admin_main', array(), 'friendsinviter_admin_main_tracker');
    
    $this->view->formFilter = $formFilter = new Friendsinviter_Form_Admin_Tracker_Filter();
    $page = $this->_getParam('page',1);

    $table = Engine_Api::_()->getItemTable('user');
    $userTableName = $table->info('name');

    $rTable = $this->_helper->api()->getDbtable('stats', 'friendsinviter');
    $rName = $rTable->info('name');
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($userTableName)
      ->joinLeft($rName, "`{$userTableName}`.`user_id` = `{$rName}`.`user_id`", array('invites_sent','invites_converted','invites_sent_counter','invites_sent_last') )
      ->joinLeft( array('U2' => $userTableName ), "`{$userTableName}`.`user_referer` = `U2`.`user_id`", array('displayname as referer_displayname','username as referer_username'));

    // Process form
    $values = array();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }

    foreach( $values as $key => $value ) {
      if( null === $value ) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order' => 'user_id',
      'order_direction' => 'DESC',
    ), $values);
    
    $this->view->assign($values);

    // Set up select info
    $select->order(( !empty($values['order']) ? $values['order'] : 'user_id' ) . ' ' . ( !empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    if( !empty($values['username']) )
    {
      $select->where("`{$userTableName}`.username LIKE ?", '%' . $values['username'] . '%');
    }

    if( !empty($values['email']) )
    {
      $select->where("`{$userTableName}`.email LIKE ?", '%' . $values['email'] . '%');
    }

    if( !empty($values['referer_username']) )
    {
      $select->where("`U2`.username LIKE ?", '%' . $values['referer_username'] . '%');
    }

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setCurrentPageNumber( $page );


    $this->view->superAdminCount = count(Engine_Api::_()->user()->getSuperAdmins());
    $this->view->hideEmails = _ENGINE_ADMIN_NEUTER;
    
  }


}