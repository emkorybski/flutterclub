<?php
class News_Form_Admin_Manage_Create extends Engine_Form
{
    
  public function init()
  {
	  	$this     
	      ->setDescription("This form is used for configuration settings to get data from remote servers");
	
	    $this->loadDefaultDecorators();
	    $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));
	    
	    //init category name
	    $this->addElement('Text', 'category_name', array(
	    	'label' => 'Feed Name',
	    	'required' => true,
	    	'style' => 'width:300px;',
	    ));
        $this->addElement('Textarea', 'url_resource', array(
            'label' => 'Feed URL',
            'required' => true,
            'style' => 'width:300px; ',
        ));
	    $this->addElement('Text', 'category_logo', array(
            'label' => 'Logo of RSS Provider',
            'required' => false,
            'style' => 'width:300px;',
        ));
        
         $this->addElement('File', 'logo', array(
          'label' => 'Or Upload Logo From Your Computer(gif,png)',
        ));
         $cats = Engine_Api::_()->news()->getAllCategoryparents(array());
        $catPerms    = array();
        $catPerms[0] = "Others";
        foreach( $cats as $cat ){
            $catPerms[$cat['category_id']] = $cat['category_name'];
        }
        $this->addElement('Select', 'category_parent_id', array(
        'label'        => 'Category',
        'style' => 'width:310px;',
        'multiOptions' => $catPerms
        ));
        $this->addElement('Checkbox', 'is_active', array(
      'label' => "Active RSS?",
      'value' => 1,
      'checked' => true,
      ));
         $this->addElement('Checkbox', 'mini_logo', array(
          'label' => "Display Mini Logo?",
          'value' => 1,
          'checked' => true,
        )); 
        $this->addElement('Checkbox', 'display_logo', array(
          'label' => "Display logo?",
          'value' => 1,
          'checked' => true,
        ));
	    
	   
	    // Buttons
	    $this->addElement('Button', 'submit', array(
	      'label' => 'Save',
	      'type' => 'submit',
	      'ignore' => true,
          'style'=>'border:none;margin-top:10px;margin-bottom:10px',
	      'decorators' => array('ViewHelper')
	    ));
	    /*
	    $this->addElement('Button', 'getdata', array(
	      'label' => 'Save && Get Data',
	      'type' => 'submit',
	      'ignore' => true,
	      'decorators' => array('ViewHelper')	      
	    ));
	    */
	   // $this->addDisplayGroup(array('submit', 'getdata'), 'buttons');
	   // $button_group = $this->getDisplayGroup('buttons');
	    //$button_group->addDecorator('DivDivDivWrapper');
  	
  }
  
}
