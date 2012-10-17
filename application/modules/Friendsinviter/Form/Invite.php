<?php

class Friendsinviter_Form_Invite extends Engine_Form
{
  public $invalid_emails  = array();
  public $already_members = array();
  public $emails_sent     = 0;

  public function init()
  {
    // Init settings object
    $settings  = Engine_Api::_()->getApi('settings', 'core');
    $translate = Zend_Registry::get('Zend_Translate');
    
    // Init form
    $this->setTitle('Invite Your Friends')
         ->setDescription('_INVITE_FORM_DESCRIPTION');
    $this->setLegend('');
    

    if (Engine_Api::_()->getApi('settings', 'core')->core_spam_invite) {
      $this->addElement('captcha', 'captcha', array(
        'description' => '_CAPTCHA_DESCRIPTION',
        'captcha' => 'image',
        'required' => true,
        'captchaOptions' => array(
          //'height'  => 30,
          'wordLen' => 6,
          'fontSize' => '30',
          'timeout' => 300,
          'imgDir' => APPLICATION_PATH . '/public/temporary/',
          'imgUrl' => $this->getView()->baseUrl().'/public/temporary',
          'font' => APPLICATION_PATH . '/application/modules/Core/externals/fonts/arial.ttf'
        )));
    }

  }


}
