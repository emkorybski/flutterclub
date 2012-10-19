<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Modules.php 2010-07-02 19:52 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_Model_DbTable_Modules extends Engine_Db_Table
{
  protected $_rowClass = "Hecore_Model_Module";
  protected $_primary = "name";

  public function findByName($name)
  {
    if (!$name) {
      return false;
    }

    $select = $this->select();
    $select->where("name = ?", $name);

    return $this->fetchRow($select);
  }

  public function isModuleEnabled($name)
  {
    $isModuleEnabled = false;

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($name)) {
      switch ($name) {
        case 'page': $name = 'pages'; break;
        case 'like': $name = 'likes'; break;
      }
      if ($this->findByName($name)) $isModuleEnabled = true;
    }

    return $isModuleEnabled;
  }

  public function getAllModules()
  {
    $select = $this->select();
    return $this->fetchAll($select);
  }
}