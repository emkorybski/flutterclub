<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

class Radcodes_Form_Element_DoubleSelect extends Zend_Form_Element_Xhtml
{
  
  public $helper = 'formDoubleSelect';

  /**
   * Flag: autoregister inArray validator?
   * @var bool
   */
  protected $_registerInArrayValidator = true;  
  
  public $options = array(
    'parentOptions' => array(),
    'multiChildOptions' => array(),
    'defaultChildMessage' => '',        
  );
  
  public function setDefaultParentMessage($message)
  {
    $this->options['defaultParentMessage'] = $message;
    return $this;
  }

  public function setDefaultChildMessage($message)
  {
    $this->options['defaultChildMessage'] = $message;
    return $this;
  }
  
  /**
   * Set all options at once (overwrites)
   *
   * @param  array $doubleOptions
   * @return Radcodes_Form_Element_DoubleSelect
   */  
  public function setDoubleOptions(array $options)
  {
    if (isset($options[0])) {
      $this->setParentOptions($options[0]);
      unset($options[0]);
    }
    $this->setMultiChildOptions($options);
  }
  
  public function getParentOptions()
  {
    return $this->options['parentOptions'];
  }
  
  public function clearParentOptions()
  {
    $this->options['parentOptions'] = array();
    return $this;
  }
  
  public function setParentOptions(array $options)
  {
    $this->clearParentOptions();
    return $this->addParentOptions($options);
  }
  
  public function addParentOptions(array $options)
  {
    foreach ($options as $option => $value)
    {
      $this->addParentOption($option, $value);
    }
    return $this;
  }
  
  public function addParentOption($option, $value = '')
  {
    $option = (string) $option;
    $this->options['parentOptions'][$option] = $value;
    return $this;
  }
  
  public function getMultiChildOptions()
  {
    return $this->options['multiChildOptions']; 
  }
  
  public function clearMultiChildOptions()
  {
    $this->options['multiChildOptions'] = array();
    return $this;
  }
  
  public function setMultiChildOptions(array $multiChildOptions)
  {
    $this->clearMultiChildOptions();
    foreach ($multiChildOptions as $parent => $options)
    {
      $this->setChildOptions($parent, $options);
    }
    return $this;
  }
  
  public function clearChildOptions($parent)
  {
    $parent = (string) $parent;
    $this->options['multiChildOptions'][$parent] = array();
    return $this;
  }
  
  public function setChildOptions($parent, $options)
  {
    $this->clearChildOptions($parent);
    foreach ($options as $option => $value)
    {
      $this->addChildOption($parent, $option, $value);
    }
    return $this;
  }
  
  public function addChildOption($parent, $option, $value = '')
  {
    $parent = (string) $parent;
    $option = (string) $option;
    $this->options['multiChildOptions'][$parent][$option] = $value;
    return $this;
  }
  
  
  /**
   * Set flag indicating whether or not to auto-register inArray validator
   *
   * @param  bool $flag
   * @return Zend_Form_Element_Multi
   */
  public function setRegisterInArrayValidator($flag)
  {
    $this->_registerInArrayValidator = (bool) $flag;
    return $this;
  }

  /**
   * Get status of auto-register inArray validator flag
   *
   * @return bool
   */
  public function registerInArrayValidator()
  {
    return $this->_registerInArrayValidator;
  }

  /**
   * Is the value provided valid?
   *
   * Autoregisters InArray validator if necessary.
   *
   * @param  string $value
   * @param  mixed $context
   * @return bool
   */
  public function isValid($value, $context = null)
  {
    if ($this->registerInArrayValidator()) {
      if (!$this->getValidator('InArray')) {
        $parentOptions = $this->getParentOptions();
        $multiChildOptions = $this->getMultiChildOptions();
        $options    = array();

        foreach ($parentOptions as $opt_value => $opt_label) {
          $options[] = $opt_value;
        }
        
        foreach ($multiChildOptions as $parent => $childOptions) {
          foreach ($childOptions as $opt_value => $opt_label) {
            $options[] = $opt_value;
          }
        }

        $this->addValidator(
          'InArray',
          true,
          array($options)
        );
      }
    }
    return parent::isValid($value, $context);
  }
  
  
  public function loadDefaultDecorators()
  {
    if( $this->loadDefaultDecoratorsIsDisabled() )
    {
      return;
    }

    $decorators = $this->getDecorators();
    if( empty($decorators) )
    {
      $this->addDecorator('ViewHelper');
      Engine_Form::addDefaultDecorators($this);
    }
  }
}

