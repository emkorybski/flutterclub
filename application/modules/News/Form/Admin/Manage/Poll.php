<?php

class News_Form_Admin_Manage_Poll extends Engine_Form
{

  public function init()
  {
       $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));
	    //init category name
        $question = Engine_Api::_()->news()->getQuestion(1);
        $optionA = Engine_Api::_()->news()->getOptionPoll(1,1);
        $optionB = Engine_Api::_()->news()->getOptionPoll(1,2);
        $this->addElement('Textarea', 'question', array(
            'label' => 'Question',
            'required' => true,
            'value' => $question,
            'style' => 'width:500px;',
        ));
        $this->addElement('Textarea', 'optionA', array(
            'label' => 'Option A',
            'required' => true,
            'value' => $optionA,
            'style' => 'width:500px;',
        ));
        $this->addElement('Textarea', 'optionB', array(
            'label' => 'Option B',
            'required' => true,
            'value' => $optionB,
            'style' => 'width:500px;',
        ));
	    $this->addElement('Checkbox', 'is_active', array(
          'label' => "Active Poll?",
          'value' => 1,
          'checked' => true,
          ));
	    // Buttons
	    $this->addElement('Button', 'submit', array(
	      'label' => 'Save ',
	      'type' => 'submit',
	      'ignore' => true,
          'style'=>'border:none;margin-left:265px;margin-top:10px',
	      'decorators' => array('ViewHelper')
	    ));

  }

}