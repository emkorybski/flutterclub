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
 
 
 
class Radcodes_Form_Admin_Category_Photo extends Radcodes_Form_Admin_Category_Abstract
{

  
  public function init()
  {
    //$this->setTitle('Delete Photo?')
     // ->setDescription('You can remove photo associated with this category by clicking on "Delete Photo" below.')
      $this->setAttrib('class', 'global_form_popup radcodes_category_form_popup')
      ;
      
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Delete Photo',
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