<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    News
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Level.php 6858 2010-07-27 01:16:32Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    News
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class News_Form_Admin_Manage_Createcategory extends Engine_Form
{
    
  public function init()
  {
       $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));
	    //init category name
	    $this->addElement('Text', 'category_name', array(
	    	'label' => 'Category Name',
	    	'required' => true,
	    	'style' => 'width:370px;',
	    ));
        
	    $this->addElement('Textarea', 'category_description', array(
	    	'label' => 'Category Description',
	    	'style' => 'width:370px;',
	    ));
	    $this->addElement('Checkbox', 'is_active', array(
          'label' => "Active Category?",
          'value' => 1,
          'checked' => true,
          ));
	    // Buttons
	    $this->addElement('Button', 'submit', array(
	      'label' => 'Save Category',
	      'type' => 'submit',
	      'ignore' => true,
          'style'=>'border:none;margin-left:265px;margin-top:10px',
	      'decorators' => array('ViewHelper')
	    ));
  	
  }
  
}