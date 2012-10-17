<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Invite.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Form_Admin_Signup_Invite extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttrib('enctype', 'multipart/form-data');

    $step_table = Engine_Api::_()->getDbtable('signup', 'user');
    $step_row = $step_table->fetchRow($step_table->select()->where('class = ?', 'Inviter_Plugin_Signup_Invite'));
    $count = $step_row->order + 1;
    $title = $this->getView()->translate('INVITER_Step %d: Invite Your Friends', $count);
    $this->setTitle($title)->setDisableTranslator(true);


    $enable = new Engine_Form_Element_Radio('enable');
    $enable->setLabel("INVITER_Invite Friends");
    $enable->setDescription("INVITER_FORM_ADMIN_SIGNUP_INVITE_DESCRIPTION");
    $enable->addMultiOptions(
      array(
        1 => "INVITER_Yes, include the 'Invite Friends' step during signup.",
        0 => "INVITER_No, do not include this step."
    ));
    $enable->setValue($step_row->enable);

    $this->addElements(array($enable));


    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}