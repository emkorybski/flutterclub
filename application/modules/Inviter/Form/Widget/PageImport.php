<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Import.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Form_Widget_PageImport extends Engine_Form
{
  protected $_errors;
  protected $_success;

  public function init()
  {
    $this->setDescription('PAGE_INVITER_FROM_WIDGET_IMPORT_DESCRIPTION')
       ->clearDecorators()
       ->clearAttribs()
       ->setAction('page-inviter')
       ->setAttrib( 'id', 'invite_friends' )
//       ->setAttrib('onsubmit', 'inviter.page_inviter_submit(); return false;')
    ;

    $translate = $this->getTranslator();

    $this->addElement('hidden', 'page_id');

    $this->addElement('text', 'email_box', array(
      'label' => 'Email',
      'required' => true,
      'autocomplete'=>'on',
      'trim' => true,
      'order' => 1,
      'value' => $translate->_('email...'),
      'decorators' => array(
        'ViewHelper'
      ),
      'id' => 'inviter_email_box',
      'class' => 'inviter_fields',
      'style' => 'color: #999999;margin-bottom: 5px; width: 180px',
    ));

    $this->addElement('password', 'password_box', array(
      'label' => 'Password',
      'type' => 'password',
      'required' => true,
      'trim' => true,
      'autocomplete'=>'off',
      'order' => 2,
      'value' => $translate->_('password...'),
      'decorators' => array(
        'ViewHelper'
      ),
      'id' => 'inviter_password_box',
      'class' => 'inviter_fields',
      'style' => 'color: #999999;width: 180px',
    ));

    $path = Engine_Api::_()->getModuleBootstrap('inviter')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $path . '/Form/Decorator/', 'Decorator');

    $this->addElement('text', 'provider_box', array(
      'label' => 'Provider',
      'required' => true,
      'order' => 3,
      'trim' => true,
      'autocomplete' =>'off',
      'value' => $translate->_('provider...'),
      'onkeyup'=>'provider.provider_suggest($(this))',
      'onblur'=>'provider.provider_blur($(this))',
      'decorators'=>array(
       'ViewHelper'
      ),
      'id' => 'provider_box',
      'class' => 'inviter_fields',
      'style' => 'width:110px; float:left;color: #999999;',
    ));
    $this->provider_box->addDecorator('ProviderSuggest', array('widget'=>true));

    $this->addElement('button', 'submit', array(
      'label' => 'PAGE_INVITER_Import Contacts',
      'type' =>'submit',
      'class' => 'page-inviter-submit',
      'onclick' =>'return provider.submit_form(this); ',
      'ignore' => true,
      'order' => 4,
      'decorators'=>array(
       'ViewHelper'
      ),
      'style' => 'margin-top: 5px',
    ));

    $this->addDisplayGroupPrefixPath('Engine_Form_Decorator_', $path . '/Form/Decorator/', 'Decorator');
    $this->addDisplayGroup(array_keys($this->getElements()), 'from_elements');

    $this->from_elements->addDecorator('DefaultProviders', array('default_providers'=>9, 'widget'=>true));
  }
}