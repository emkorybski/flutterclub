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
 
 
 
class Radcodes_Form_Admin_Category_Move extends Radcodes_Form_Admin_Category_Abstract
{

  public function init()
  {
    $this
      ->setMethod('post')
      ->setTitle('Move Category')
      ->setDescription('Use this form to transfer entries from one category to another.')
      ->setAttrib('class', 'global_form_popup radcodes_category_form_popup')
      ;
      
    // prepare categories
   
    $multiOptions = array("" => "") + $this->getCategoryTable()->getMultiOptionsAssoc();

    $this->addElement('Select', 'from_category_id', array(
      'label' => 'From Category',
      'multiOptions' => $multiOptions,
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
      ),
      'filters' => array(
       'Int'
      ),    
    ));
    
    $this->addElement('Select', 'to_category_id', array(
      'label' => 'To Category',
      'multiOptions' => $multiOptions,
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
      ),
      'filters' => array(
       'Int'
      ),    
    ));
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Move Entries',
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