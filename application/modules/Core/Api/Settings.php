<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Settings.php 9646 2012-03-14 19:19:07Z shaun $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Api_Settings extends Core_Api_Abstract
{
  protected $_table;

  public function  __construct()
  {
    $this->_table = Engine_Api::_()->getDbtable('settings', 'core');
  }

  public function __get($key)
  {
    return $this->_table->getSetting($key);
  }

  public function __set($key, $value)
  {
    return $this->_table->setSetting($key, $value);
  }

  public function __isset($key)
  {
    return $this->_table->hasSetting($key);
  }

  public function __unset($key)
  {
    return $this->_table->removeSetting($key);
  }

  public function __call($method, array $arguments = array())
  {
    $r = new ReflectionMethod($this->_table, $method);
    return $r->invokeArgs($this->_table, $arguments);
  }
}