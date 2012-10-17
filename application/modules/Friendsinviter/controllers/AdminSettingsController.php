<?php

class Friendsinviter_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('friendsinviter_admin_main', array(), 'friendsinviter_admin_main_settings');
    
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->view->form = $form = new Friendsinviter_Form_AdminSettings();
    
    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
    {
      $form->saveAdminSettings();
    }
  }


}