<?php

class Friendsinviter_AdminQuickstatsController extends Core_Controller_Action_Admin
{


  public function indexAction()
  {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('friendsinviter_admin_main', array(), 'friendsinviter_admin_main_quickstats');

    $table  = Engine_Api::_()->getDbtable('statistics', 'core');

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($table->info('name'), array('SUM(value) as total'))
      ->where("type = ?",'friendsinviter.invites');
      
    $this->view->total_invites = $total_invites = (int)$table->fetchRow($select)->total;
    


    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($table->info('name'), array('SUM(value) as total'))
      ->where("type = ?",'friendsinviter.converted_invites');

    $this->view->total_converted_invites = $total_converted_invites = (int)$table->fetchRow($select)->total;


    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($table->info('name'), array('SUM(value) as total'))
      ->where("type = ?",'friendsinviter.imported_contacts');

    $this->view->total_contacts_imported = $total_contacts_imported = (int)$table->fetchRow($select)->total;


    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($table->info('name'), array('SUM(value) as total'))
      ->where("type = ?",'friendsinviter.invited_contacts');

    $this->view->total_contacts_invited = $total_contacts_invited = (int)$table->fetchRow($select)->total;


    $this->view->contacts_invited_vs_signups = $contacts_invited_vs_signups = $total_invites ? $total_converted_invites / $total_invites * 100 : 0;
  
    $this->view->contacts_imported_vs_invited = $contacts_imported_vs_invited = $total_contacts_imported ? $total_contacts_invited / $total_contacts_imported * 100 : 0;

      
  }
  
}