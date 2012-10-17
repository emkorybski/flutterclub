<?php

class Friendsinviter_AdminHelpController extends Core_Controller_Action_Admin
{


  public function indexAction()
  {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('friendsinviter_admin_main', array(), 'friendsinviter_admin_main_help');

    $table  = Engine_Api::_()->getDbtable('teasersettings', 'friendsinviter');

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($table->info('name'), array('COUNT(*) as total'))
      ->where("enabled = 0");
      
    $this->view->hidden_count = (int)$table->fetchRow($select)->total;


    $table  = Engine_Api::_()->getDbtable('users', 'friendsinviter');

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($table->info('name'), array('COUNT(*) as total'))
      ->where("enabled = 1")
      ->where("verified = 1");
      
    $this->view->total_users = (int)$table->fetchRow($select)->total;

  }


  public function clearAction()
  {
    
    Engine_Api::_()->getDbtable('teasersettings', 'friendsinviter')->showForAll();
    
    return $this->_helper->redirector->gotoRoute( array(
                                                        'module'      => 'friendsinviter',
                                                        'controller'  => 'help',
                                                        'action'      => 'index'
                                                        )
                                                 );
    
  }
  
}