<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Article
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
class Article_AdminWidgetController extends Core_Controller_Action_Admin
{
  public function init()
  {
    if (!Engine_Api::_()->article()->checkLicense()) {
      return $this->_redirectCustom(array('route'=>'admin_default', 'module'=>'article', 'controller'=>'settings', 'notice' => 'license'));
    }   

    parent::init();
  }   
  
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('article_admin_main', array(), 'article_admin_main_widget');

    $this->view->form = $form = new Article_Form_Admin_Widget();

    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
    {
      $values = $form->getValues();

      foreach ($values as $key => $value){
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }

      $savedChangesNotice = Zend_Registry::get('Zend_Translate')->_("Your changes were saved.");
      $form->addNotice($savedChangesNotice);
    }
  }


}