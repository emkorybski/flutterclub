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
 
class Radcodes_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');
    
    
    $this->addElement('Radio', 'radcodes_mapcache', array(
      'label' => 'Google Map - Caching',
      'description' => "Would you like to enable caching for geocoding result?",
      'multiOptions' => array(
				1 => "Yes",
        0 => "No",
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('radcodes.mapcache', 1),
    ));
        
    $this->addElement('Radio', 'radcodes_mapdebug', array(
      'label' => 'Google Map - Debug',
      'description' => "Would you like to output debug data for geocoding result?",
      'multiOptions' => array(
				1 => "Yes",
        0 => "No",
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('radcodes.mapdebug', 0),
    ));
    
    
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}