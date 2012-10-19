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
 

 
class Article_AdminFieldsController extends Fields_Controller_AdminAbstract
{
  protected $_fieldType = 'article';

  protected $_requireProfileType = false;
  
  public function init()
  {
    if (!Engine_Api::_()->article()->checkLicense()) {
      return $this->_redirectCustom(array('route'=>'admin_default', 'module'=>'article', 'controller'=>'settings', 'notice' => 'license'));
    }   

    parent::init();
  } 
  
  public function indexAction()
  {
    // Make navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('article_admin_main', array(), 'article_admin_main_fields');

    parent::indexAction();
  }

  public function fieldCreateAction()
  {
    parent::fieldCreateAction();


    $form = $this->view->form;

    if($form){
      $form->setTitle('Add Article Question');

      $form->removeElement('display');
      $form->removeElement('search');
     
      // Display
      $form->addElement('Select', 'display', array(
        'label' => 'Show on Article Profiles?',
        'multiOptions' => array(
          1 => 'Show on Article Profiles',
          0 => 'Hide on Article Profiles'
        )
      ));


      $form->addElement('Select', 'search', array(
        'label' => 'Show on the search options?',
        'multiOptions' => array(
          0 => 'Hide on the search options',
          1 => 'Show on the search options'
        ),
        'value' => 1
      ));
    }
  }

  public function fieldEditAction()
  {
    parent::fieldEditAction();


    $form = $this->view->form;

    if($form){
      $form->setTitle('Edit Article Question');

      $form->removeElement('display');
      $form->removeElement('search');

      // Display
      $form->addElement('Select', 'display', array(
        'label' => 'Show on Article Profiles?',
        'multiOptions' => array(
          1 => 'Show on Article Profiles',
          0 => 'Hide on Article Profiles'
        ),
        'value' => $this->view->display
      ));
      
      
      $form->addElement('Select', 'search', array(
        'label' => 'Show on the search options?',
        'multiOptions' => array(
          0 => 'Hide on the search options',
          1 => 'Show on the search options'
        ),
        'value' => $this->view->search
      ));
    }
  }

}