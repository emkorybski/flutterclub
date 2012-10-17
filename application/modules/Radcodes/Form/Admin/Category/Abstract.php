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
 
 
 
abstract class Radcodes_Form_Admin_Category_Abstract extends Engine_Form
{
  protected $_categoryTable;
  protected $_enableProfileType = false;
  protected $_moduleApi = null;
  
  /**
   * @return Radcodes_Model_DbTable_Categories
   */
  public function getCategoryTable()
  {
    return $this->_categoryTable;
  }

  public function setCategoryTable($table)
  {
    $this->_categoryTable = $table;
    return $this;
  }
  

  protected function setEnableProfileType($val)
  {
    $this->_enableProfileType = $val;
    return $this;
  }
  
  protected function getEnableProfileType()
  {
    return $this->_enableProfileType;
  }
  
  
  protected function setModuleApi($val)
  {
    $this->_moduleApi = $val;
    return $this;
  }
  
  protected function getModuleApi()
  {
    return $this->_moduleApi;
  }  
  
}