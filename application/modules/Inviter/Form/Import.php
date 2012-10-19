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

class Inviter_Form_Import extends Engine_Form
{
  public function init()
  {
    $this->setDescription('INVITER_FROM_IMPORT_DESCRIPTION')
       ->clearDecorators()
       ->clearAttribs()
       ->setAttrib( 'id', 'invite_friends');

    $this->addElement('text', 'email_box', array(
      'label' => 'Email',
      'required' => true,
      'autocomplete'=>'on',
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),),
      'trim' => true,
      'order' => 2,
      'style' => 'margin-top: 8px; width: 180px;'
    ));
    
    $this->addElement('password', 'password_box', array(
      'label' => 'Password',
      'type' => 'password',
      'required' => true,
      'trim' => true,
      'autocomplete'=>'off',
      'order' => 3,
      'style' => 'margin-top: 8px; width: 180px;'
    ));
    
    $path = Engine_Api::_()->getModuleBootstrap('inviter')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $path . '/Form/Decorator/', 'Decorator');
    
    $this->addElement('text', 'provider_box', array(
      'label' => 'Provider',
      'required' => true,
      'style' => 'width:120px; float:left;',
      'order' => 1,
      'trim' => true,
      'autocomplete' =>'off',
      'onkeyup'=>'provider.provider_suggest($(this))',
      'onblur'=>'provider.provider_blur($(this))',
      'decorators'=>array(
       'ViewHelper'
      )
    ));
    $this->provider_box->addDecorator('ProviderSuggest');
    $this->addDefaultDecorators($this->provider_box);

    $this->addElement('button', 'submit', array(
      'label' => 'INVITER_Import Contacts',
      'type' =>'submit',
      'onclick' =>'return provider.submit_form(this)',
      'ignore' => true,
      'order' => 4,
      'style' =>'margin-top: 5px;'
    ));

    $this->addDisplayGroupPrefixPath('Engine_Form_Decorator_', $path . '/Form/Decorator/', 'Decorator');
    $this->addDisplayGroup(array_keys($this->getElements()), 'from_elements');
    
    $this->from_elements->addDecorator('DefaultProviders', array('default_providers'=>12));
  }

    /**
     * Validate the form
     *
     * @param  array $data
     * @return boolean
     */
  public function isValid($data)
  {
    if (!is_array($data)) {
      // require_once 'Zend/Form/Exception.php';
      throw new Zend_Form_Exception(__CLASS__ . '::' . __METHOD__ . ' expects an array');
    }

    $translator = $this->getTranslator();
    $valid      = true;

    if ($this->isArray()) {
      $data = $this->_dissolveArrayValue($data, $this->getElementsBelongTo());
    }

    $providerApi = Engine_Api::_()->getApi('provider', 'inviter');

    $integrated_provider = (isset($data['provider_box']) && $providerApi->checkIntegratedProvider( $data['provider_box'])) ? $data['provider_box'] : null;

    foreach ($this->getElements() as $key => $element) {
      $element->setTranslator($translator);

      if ($integrated_provider && in_array($key, array('email_box', 'password_box'))) {
        continue;
      }

      if (!isset($data[$key])) {
        $valid = $element->isValid(null, $data) && $valid;
      } else {
        $valid = $element->isValid($data[$key], $data) && $valid;
      }
    }
    
    $this->_errorsExist = !$valid;

    // If manually flagged as an error, return invalid status
    if ($this->_errorsForced) {
      return false;
    }

    return $valid;
  }
}