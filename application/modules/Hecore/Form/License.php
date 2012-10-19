<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: License.php 2010-07-02 19:52 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_Form_License extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Please type license key.')
      ->setDescription('Please update your license key <a href="http://www.hire-experts.com" target="_blank">here</a>')
      ->setMethod('post');

    $this->addElement('Text', 'license', array(
      'label' => 'License Key',
      'allowEmpty' => false,
      'required' => true,
      'style' => 'width: 300px;',
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 128)),
      ),
      'filters' => array(
        'StripTags',
      ),
    ));

    $this->addElement('Hidden', 'name', array(
      'allowEmpty' => false,
      'required' => true,
      'order' => 900
    ));
    
    $this->addElement('Hidden', 'version', array(
      'allowEmpty' => false,
      'required' => true,
      'order' => 901
    ));

    $this->addElement('Hidden', 'target_version', array(
      'order' => 902
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Register Product',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'style' => 'margin-top:12px'
    ));

    $this->addElement('Cancel', 'cancel', array(
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'style' => 'margin-top:18px'
    ));
  }
}