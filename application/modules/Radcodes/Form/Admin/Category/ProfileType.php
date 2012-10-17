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
 
 
 
class Radcodes_Form_Admin_Category_ProfileType extends Radcodes_Form_Admin_Category_Abstract
{

  
  public function init()
  {
    $this->setTitle('Category Profile Type')
      ->setDescription('Please select profile type for this category. If you change its profile type, entries belong to this category may have to update their profiles again.')
      ->setAttrib('class', 'global_form_popup radcodes_category_form_popup')
      ;
    

    if ($this->getEnableProfileType()) {
      $this->addElement('Select', 'profile_type_id', array(
        'label' => 'Profile Type',
        'multiOptions' => $this->getModuleApi()->profile()->getTypesAssoc(),
        'allowEmpty' => false,
        'required' => true,
      ));
    }

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}