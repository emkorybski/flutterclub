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
 
 
 
class Radcodes_Form_Admin_Category_Create extends Radcodes_Form_Admin_Category_Abstract
{

  
  public function init()
  {
    $this->setTitle('Add New Category')
      ->setDescription('Please fill out the form below to create a new category.')
      ->setAttrib('class', 'global_form_popup radcodes_category_form_popup')
      ;
    
    /*
    if ($this->getEnableProfileType()) {
      $this->addElement('Select', 'profile_type_id', array(
        'label' => 'Profile Type',
        'multiOptions' => array("" => "") + $this->getModuleApi()->profile()->getTypesAssoc()
      ));
    }
    */
        
    $this->addElement('Select', 'parent_id', array(
      'label' => 'Parent Category',
      'multiOptions' => array("0" => "__ ROOT __"),
    ));  
      
    $this->addElement('Text', 'category_name', array(
      'label' => 'Category Name',
      'allowEmpty' => false,
      'required' => true,
      'attribs' => array(
        'class' => 'text'
      ),
    ));
    
    $this->addElement('Textarea', 'description', array(
      'label' => 'Description',
    ));

    
    $this->addElement('File', 'photo', array(
      'label' => 'Photo'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif');


    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Add Category',
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