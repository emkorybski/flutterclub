<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_Loader
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Loader.php 9339 2011-09-29 23:03:01Z john $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_Loader
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Engine_Loader
{
  /**
   * Singleton instance
   * 
   * @var Engine_Loader
   */
  static protected $_instance;

  /**
   * Class prefix to path mappings
   * 
   * @var array
   */
  protected $_prefixToPaths = array();

  /**
   * Array of loaded resources by class name
   * 
   * @var array
   */
  protected $_components = array();

  /**
   * Get current singleton instance
   * 
   * @return Engine_Loader
   */
  public static function getInstance()
  {
    if( null === self::$_instance ) {
      self::$_instance = new self();
    }

    return self::$_instance;
  }

  /**
   * Set current loader instance
   * 
   * @param Engine_Loader $loader
   */
  public static function setInstance(Engine_Loader $loader = null)
  {
    self::$_instance = $loader;
  }

  /**
   * Constructor
   */
  public function __construct()
  {
    spl_autoload_register(array(__CLASS__, 'autoload'));
  }

  /**
   * Registered in {@link Engine_Loader::__construct()} to spl_autoload_register
   * 
   * @param string $class
   * @return boolean
   */
  static public function autoload($class)
  {
    if( null !== self::$_instance ) {
      $self = self::$_instance;
    } else {
      $self = self::getInstance();
    }

    if( false === ($pos = strpos($class, '_')) ) {
      return false;
    }

    $prefix = substr($class, 0, $pos);

    if( !empty($self->_prefixToPaths[$prefix]) ) {
      $suffix = substr($class, $pos + 1);
      $path = $self->_prefixToPaths[$prefix] . DIRECTORY_SEPARATOR
        . str_replace('_', DIRECTORY_SEPARATOR, $suffix)
        . '.php';
    } else {
      $path = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    }
    
    $includeResult = include_once $path;
    
    return $includeResult;
  }

  /**
   * Registers a class prefix to path mapping
   * 
   * @param string $prefix
   * @param string $path
   * @return Engine_Loader
   */
  public function register($prefix, $path = null)
  {
    $this->_prefixToPaths[$prefix] = $path;
    return $this;
  }

  /**
   * Force load a class
   * 
   * @param string $class
   * @throws Engine_Loader_Exception If unable to load
   */
  public static function loadClass($class)
  {
    if( !class_exists($class, false) ) {
      if( !self::autoload($class) ) {
        throw new Engine_Loader_Exception(sprintf('Could not load class: %s', $class));
      }
    }
  }

  /**
   * Same as {@link Engine_Loader::loadClass()} except returns status
   * 
   * @param string $class
   * @return boolean
   */
  public static function conditionalLoadClass($class)
  {
    return (bool) self::autoload($class);
  }

  /**
   * Loads and instantiates a resource class
   * 
   * @param string $class
   * @return mixed
   */
  public function load($class)
  {
    if( isset($this->_components[$class]) ) {
      return $this->_components[$class];
    }

    if( !class_exists($class, false) ) {
      self::loadClass($class);
    }

    return $this->_components[$class] = new $class();
  }
}

