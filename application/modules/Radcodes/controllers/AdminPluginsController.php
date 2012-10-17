<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
class Radcodes_AdminPluginsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('radcodes_admin_main', array(), 'radcodes_admin_main_plugins');

    $system_modules = Engine_Api::_()->getDbtable('modules', 'core')->getModulesAssoc();
      
  	$radcodes_modules = Engine_Api::_()->radcodes()->getRest('store')->getModules();
  	
  	//$xx = $radcodes_modules['resume'];
  	//unset($radcodes_modules['resume']);
  	//$radcodes_modules['stupid'] = $xx;
  	
  	foreach ($radcodes_modules as $type => $radcodes_module) {
  	  
  	  $v4_type = $radcodes_module['v4_type'];
  	  
  	  $radcodes_module['latest_version'] = $this->properVersion($radcodes_module['version']);
  	  
  	  
  	  if (!empty($system_modules[$v4_type])) {
  	    $radcodes_module['system_module'] = $system_modules[$v4_type];
  	    
  	    $radcodes_module['installed_version'] = $system_modules[$v4_type]['version'];
  	    $radcodes_module['installed'] = true;
  	    
  	    $radcodes_module['upgradable'] = version_compare($radcodes_module['installed_version'], $radcodes_module['latest_version'], '<');
  	  }
  	  else {
  	    $radcodes_module['installed'] = false;
  	  }

  	  $radcodes_modules[$type] = $radcodes_module;
  	}
  	
  	$this->view->modules = $radcodes_modules;
  }
  
  private function properVersion($version)
  {
    $v = (int) str_replace('.', '', $version);
    
    if (strlen($v) == 1) {
      $v = $v * 100;
    }
    elseif (strlen($v) == 2) {
      $v = $v * 10;
    }

    $major = substr($v, 0, -2);
    $minor = substr($v, -2, 1);
    $release = substr($v, -1);
    
    return "$major.$minor.$release";
  }
  
}