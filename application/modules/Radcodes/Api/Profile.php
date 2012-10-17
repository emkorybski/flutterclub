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
 
class Radcodes_Api_Profile extends Core_Api_Abstract
{

  /**
   * lower-case of module name (ex: business)
   */
  protected $_spec;
  
  public function getSpec()
  {
    if (!$this->_spec) {
      $this->_spec = strtolower($this->getModuleName());
    }
    return $this->_spec;
  }
  
  /**
   * Get the profile_type field def
   * @return Fields_Model_Meta
   */
  public function getFieldMeta()
  {
    $topStructure = Engine_Api::_()->fields()->getFieldStructureTop($this->getSpec());
    if( count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type' ) {
      return $topStructure[0]->getChild();
    }
    throw new Core_Model_Exception("Could not find ".$this->getSpec()." 'profile_type' field meta.");
  }
  // getFieldMeta
  
  
  /**
   * Get all profile types in array assoc mode
   * @return array
   */
  public function getTypesAssoc()
  {
    static $types = null;
    if ($types === null)
    {
      $optionsData = Engine_Api::_()->getApi('core', 'fields')->getFieldsOptions($this->getSpec());
      
      $field = $this->getFieldMeta();
      
      $types = array();
      
      foreach ($field->getOptions() as $option) {
        $types[$option->option_id] = $option->label;
      }
      //print_r($types);
    }
    return $types;
  }
  // getTypesAssoc
  
  
  public function isValidTypeId($type_id)
  {
    $types = $this->getTypesAssoc();
    return array_key_exists($type_id, $types);
  }
  
  public function getLabel($type_id)
  {
    if (!$this->isValidTypeId($type_id)) {
      return "Deleted Profile Type";
    }
    
    $types = $this->getTypesAssoc();
    return $types[$type_id];
  }
  // getLabel
  
  
  /**
   * get default profile type id
   * @return int
   */
  public function getDefaultTypeId()
  {
    $types = $this->getTypesAssoc();
    $type_id = Engine_Api::_()->getApi('settings', 'core')->getSetting($this->getSpec() . '.profile.default', 1);
    
    if (!$this->isValidTypeId($type_id)) {
      $types = $this->getTypesAssoc();
      reset($types);
      $type_id = key($types);
    }
    
    return $type_id;
  }
  // getDefaultTypeId
  

  public function isMultiTypes()
  {
    return (count($this->getTypesAssoc()) > 1);
  }
  
  
  public function getCategoryMapping($params = array())
  {
    $mapping = array();
    
    $categories = Engine_Api::_()->getDbtable('categories', $this->getSpec())->getCategories($params);
    foreach ($categories as $category) {
      $mapping[$category->getIdentity()] = $category->getProfileTypeId();
    }
    
    return $mapping;
  }
}

