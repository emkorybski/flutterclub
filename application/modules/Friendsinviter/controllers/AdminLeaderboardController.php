<?php

class Friendsinviter_AdminLeaderboardController extends Core_Controller_Action_Admin
{


  public function indexAction()
  {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('friendsinviter_admin_main', array(), 'friendsinviter_admin_main_leaderboard');

    $table  = Engine_Api::_()->getDbtable('invites', 'invite');
    $iName  = $table->info('name');
    $uName  = Engine_Api::_()->getDbtable('users', 'invite')->info('name');
      

    $table = Engine_Api::_()->getItemTable('user');
    $userTableName = $table->info('name');

    $rTable = $this->_helper->api()->getDbtable('stats', 'friendsinviter');
    $rName = $rTable->info('name');
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($rName)
      ->joinLeft($userTableName, "`{$rName}`.`user_id` = `{$userTableName}`.`user_id`", null )
      ->where("`{$userTableName}`.user_id != 0")
      ->order(array('invites_sent DESC','invites_converted DESC'))
      ->limit(100);
      
    $this->view->leaderboard = $table->fetchAll($select);

    // Clear order 
    $select->reset( Zend_Db_Select::ORDER );

    $select
      ->order(array('invites_converted DESC','invites_sent DESC'))
      ->limit(100);
      
    $this->view->leaderboard2 = $table->fetchAll($select);

      
  }


  public function clearAction()
  {

    Engine_Api::_()->getDbtable('stats', 'friendsinviter')->delete();
    
    return $this->_helper->redirector->gotoRoute( array(
                                                        'module'      => 'friendsinviter',
                                                        'controller'  => 'leaderboard',
                                                        'action'      => 'index'
                                                        )
                                                 );
    
  }
  
  
}