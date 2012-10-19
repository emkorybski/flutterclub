<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IntroduceEdit.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Form_IntroduceEdit extends Engine_Form
{
  public function init()
  {
    $this->setDescription('INVITER_FORM_EDIT_INTRODUCE_DESCRIPTION');

    $this->addElement('Textarea', 'body', array(
      'label' => 'INVITER_About me',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 500)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
      ),
    ));

    $this->addElement('Checkbox', 'publish', array(
      'label' => 'INVITER_Show your introduction',
    ));

    $this->addElement('Button', 'submit', array(
      'type' => 'submit',
      'label' => 'Save'
    ));
  }
}