<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Write.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Form_Widget_PageWrite extends Engine_Form
{
  public function init()
  {
    // Init settings object
    $settings = Engine_Api::_()->getApi('settings', 'core');

    // Init form
    $this->clearDecorators()
         ->clearAttribs()
         ->setDescription('PAGE_INVITER_FORM_WRITE_DESCRIPTION')
         ->setAttrib( 'id', 'invite_friends');

      $this->addElement('hidden', 'page_id');

    // Init recipients
    $this->addElement('Textarea', 'recipients', array(
      'label' => 'Recipients',
      'required' => true,
      'allowEmpty' => false,
      'class' => 'writer_textarea',
      'decorators'=>array(
          'ViewHelper',
           'Description',
          'Label',
          array('HtmlTag2', array('tag' => 'div', 'class'=>'widget-writer-textarea-conteiner')),
      ),
      'style'=>'width: 220px'
    ));

    // Init custom message
    if( $settings->getSetting('invite.allowCustomMessage', 1) > 0 ) {
      $this->addElement('Textarea', 'message', array(
        'label' => 'Message',
        'required' => false,
        'allowEmpty' => true,
        'value' => $this->getTranslator()->_('PAGE_INVITER_You are being invited to join our social network.'),
        'filters' => array(
          new Engine_Filter_Censor(),
        ),
        'decorators'=>array(
            'ViewHelper',
            'Label',
            array('HtmlTag2', array('tag' => 'div', 'class'=>'widget-writer-textarea-conteiner')),
        ),
        'class' => 'writer_textarea',
        'style'=>'width: 220px',
      ));
    }

    $path = Engine_Api::_()->getModuleBootstrap('inviter')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $path . '/Form/Decorator/', 'Decorator');
    $this->addElement('button', 'submit_contacts', array(
      'label' => 'PAGE_INVITER_Send Invitations',
      'onClick'=>'sendInvitations()',
      'decorators'=>array(
       'ViewHelper',
        'SubmitContacts',
      ),
      'style' => 'margin-top: 10px;float:left'
    ));
  }
}
