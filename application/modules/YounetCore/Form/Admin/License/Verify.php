<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class YounetCore_Form_Admin_License_Verify extends Engine_Form {

  public function init() {
  
    $this->setTitle('Verify your package')
          ->setDescription('Package description')
          ->setAttrib('id','id_form_younetcore_admin_license_verify');
    
    $this->addElement('text','license',array(
        'label'=>'License Key',
        'required'=>true,
        'validators'=>array(),
    ));
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Verify',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'prependText' => ' or ',
       'link'=>true,
      'href'=>'',        
      'decorators' => array(
        'ViewHelper'
      )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');

  }
  

}
