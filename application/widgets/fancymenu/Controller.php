<?php
/**
 * YouSocialEngine
 *
 * @category    Application_Widget
 * @package     Fancymenu
 * @copyright   Copyright (c) 2011, Shane Barcinas
 * @license     http://yousocialengine.com/view-content/2/License-Terms.html
 * @version     $Id: Controller.php 2011-28-09 21:30 shane $
 * @author      Shane Barcinas
 */

class Widget_FancymenuController extends Engine_Content_Widget_Abstract
{
  
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('core_main');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
    if(!$require_check && !$viewer->getIdentity()) {
      $navigation->removePage($navigation->findOneBy('route', 'user_general'));
    }
    
    // Get menu name
    $this->view->menuname = $menuname = $this->_getParam('menuname');
    // Get menu count
    $this->view->menucount = $menucount = $this->_getParam('menucount');
    // Get menu effect
    $this->view->menueffect = $menueffect = $this->_getParam('menueffect');
    // Get menu physics
    $this->view->menuphysics = $menuphysics = $this->_getParam('menuphysics');
    // Get menu duration
    $this->view->fxduration = $fxduration = $this->_getParam('fxduration');
    // Get menu theme
    $this->view->menutheme = $menutheme = $this->_getParam('menutheme');
  }
  
}