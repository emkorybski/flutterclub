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
class News_Form_Admin_Manage_Setting extends Engine_Form
{
  public function init()
  {
	  	$this	      
	      ->setDescription("This form setting timeframes to get data. All the variables, with the exception of the command itself, are numerical constants. In addition to an asterisk (*), which is a wildcard that allows any value.");
	
	    $this->loadDefaultDecorators();
	    $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));
	    
	    //init category name
	    $this->addElement('Text', 'minutes', array(
	    	'label' => 'Minutes',
	    	'Description' => 'Value is 00 - 59. Exact minute the cron executes',
	    	'required' => true,
	    	'style' => 'width:300px;',
	    ));
	    
	    
	    $this->addElement('Text', 'hour', array(
	    	'label' => 'Hour',
	    	'Description' => 'Value is 00 - 23. Hour of the day the cron executes. 0 means midnight',
	    	'required' => true,
	    	'style' => 'width:300px;',
	    ));
	    
	    $this->addElement('Text', 'month', array(
	    	'label' => 'Month',
	    	'Description' => 'Value is 01 - 12. Month of the year the cron executes',
	    	'required' => true,
	    	'style' => 'width:300px;',
	    ));
	    
	    $this->addElement('Text', 'day', array(
	    	'label' => 'Day',
	    	'Description' => 'Value is 01 - 31. Day of the month the cron executes',
	    	'required' => true,
	    	'style' => 'width:300px;',
	    ));
	    
	    $this->addElement('Text', 'weekday', array(
	    	'label' => 'Week day',
	    	'Description' => 'Value is 00 - 06. Day of the week the cron executes. Sunday=0,Monday=1',
	    	'required' => true,
	    	'style' => 'width:300px;',
	    ));
	        
	
	    // Buttons
	    $this->addElement('Button', 'submit', array(
	      'label' => 'Save',
	      'type' => 'submit',
	      'ignore' => true,
	      'decorators' => array('ViewHelper')
	    ));
	    
	    // hidden
	    $this->addElement('Hidden', 'id', array());
	    
	   /*
	    $this->addDisplayGroup(array('submit', 'getdata'), 'buttons');
	    $button_group = $this->getDisplayGroup('buttons');
	    $button_group->addDecorator('DivDivDivWrapper');
	    */
  	
  }
}