<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Photo.php 9632 2012-02-23 23:28:29Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Form_Signup_Photo extends Engine_Form
{
  public function init()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
  
    // Init form
    $this->setTitle('Add Your Photo');

    $this
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAttrib('id', 'SignupForm');

    $this->addElement('Image', 'current', array(
      'label' => 'Current Photo',
      'ignore' => true,
      'decorators' => array(array('ViewScript', array(
        'viewScript' => '_formSignupImage.tpl',
        'class'      => 'form element'
      )))
    ));
    Engine_Form::addDefaultDecorators($this->current);

    $this->addElement('File', 'Filedata', array(
      'label' => 'Choose New Photo',
      'destination' => APPLICATION_PATH.'/public/temporary/',
      'multiFile' => 1,
      'validators' => array(
        array('Count', false, 1),
        array('Extension', false, 'jpg,png,gif,jpeg'),
      ),
      'onchange'=>'javascript:uploadSignupPhoto();'
    ));
  
    $this->addElement('Hash', 'token');

    $this->addElement('Hidden', 'coordinates', array(
      'order' => 1
    ));
    $this->addElement('Hidden', 'uploadPhoto', array(
      'order' => 2
    ));
    $this->addElement('Hidden', 'nextStep', array(
      'order' => 3
    ));
    $this->addElement('Hidden', 'skip', array(
     'order' => 4
    ));
    
    // Element: done
    $this->addElement('Button', 'done', array(
      'label' => 'Save Photo',
      'type' => 'submit',
      'onclick' => 'javascript:finishForm();',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    // Element: skip  
    if( $settings->getSetting('user.signup.photo', 0) == 0 ) {
      $this->addElement('Cancel', 'skip-link', array(
        'label' => 'skip',
        'prependText' => ' or ',
        'link' => true,
        'href' => 'javascript:void(0);',
        'onclick' => 'skipForm(); return false;',
        'decorators' => array(
          'ViewHelper',
        ),
      ));
    }

    // DisplayGroup: buttons
    $this->addDisplayGroup(array('done', 'skip-link'), 'buttons', array(

    ));
  }
}