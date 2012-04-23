<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Twitter.php 9382 2011-10-14 00:41:45Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Form_Admin_Twitter extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Twitter Integration')
      ->setDescription('USER_ADMIN_SETTINGS_TWITTER_DESCRIPTION')
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod("POST");
      ;

    $description = $this->getTranslator()->translate('USER_ADMIN_SETTINGS_TWITTER_DESCRIPTION');
    $description = vsprintf($description, array(
      'https://dev.twitter.com/apps/new',
      'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'module' => 'user',
          'controller' => 'auth',
          'action' => 'twitter'
        ), 'default', true),
    ));
    $this->setDescription($description);

    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
    
    $this->addElement('Text', 'key', array(
      'label' => 'Twitter App Consumer Key',
      'description' => '',
      'filters' => array(
        'StringTrim',
      ),
    ));
    
    $this->addElement('Text', 'secret', array(
      'label' => 'Twitter App Consumer Secret',
      'description' => '',
      'filters' => array(
        'StringTrim',
      ),
    ));

    $this->addElement('Radio', 'enable', array(
      'label' => 'Integrate Features',
      'description' => 'What features would you like to integrate?',
      'multiOptions' => array(
        'none'  => 'None',
        'login' => 'Login only',
        'publish' => 'Publish to Twitter',
      ),
      'value' => 'none'
    ));


    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));

  }
}
