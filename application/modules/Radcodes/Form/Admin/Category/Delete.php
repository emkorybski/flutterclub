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
 
 
 
class Radcodes_Form_Admin_Category_Delete extends Radcodes_Form_Admin_Category_Abstract
{

  
  public function init()
  {
    $this->setTitle('Delete Category?')
      ->setDescription('Are you sure that you want to delete this category? It will not be recoverable after being deleted.')
      ->setAttrib('class', 'global_form_popup radcodes_category_form_popup')
      ;
    
    /*
    $this->addElement('Select', 'category_id', array(
      'label' => 'Category',
      'allowEmpty' => false,
      'required' => true,        
      'multiOptions' => array(),
      'validators' => array(
        new Engine_Validate_Callback(array($this, 'validateCategory')),
      ),         
    ));  
    */
      
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Delete',
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

  public function validateCategory($value)
  {
    $validator = $this->category_id->getValidator('Engine_Validate_Callback');
    
    $category = $this->getCategoryTable()->getCategory($value);
    
    if ($category->getUsedCount() > 0)
    {
      $validator->setMessage('This category has entries associated with it, move these entries to a different category first.');
      return false;
    }
    else if ($category->hasChildrenCategory())
    {
      $validator->setMessage('This category has sub-categories, delete them first.');
      return false;
    }
    
    return true;
  }  
  
}