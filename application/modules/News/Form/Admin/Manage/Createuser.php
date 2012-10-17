<?php
class News_Form_Admin_Manage_Createuser extends Engine_Form
{
    
  public function init()
  {
       $this->loadDefaultDecorators();
       //$this->setDescription('Users in this list have the right to manage news');
        $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));
	    //init category name
	    $this->addElement('Text', 'username', array(
	    	'label' => 'Username',
	    	'required' => true,
	    	'style' => 'width:370px;',
	    ));
        
	    // Buttons
	    $this->addElement('Button', 'submit', array(
	      'label' => 'Add User',
	      'type' => 'submit',
	      'ignore' => true,
          'style'=>'border:none;margin-left:265px;margin-top:10px',
	      'decorators' => array('ViewHelper')
	    ));
  	
  }
  
}