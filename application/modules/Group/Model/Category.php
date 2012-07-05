<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Category.php 9382 2011-10-14 00:41:45Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Group_Model_Category extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;
  
  public function getTable()
  {
    if( null === $this->_table ) {
      $this->_table = Engine_Api::_()->getDbtable('categories', 'group');
    }

    return $this->_table;
  }

  public function getUsedCount()
  {
    $eventTable = Engine_Api::_()->getItemTable('group');
    return $eventTable->select()
        ->from($eventTable, new Zend_Db_Expr('COUNT(group_id)'))
        ->where('category_id = ?', $this->category_id)
        ->query()
        ->fetchColumn();
  }

  public function isOwner($owner)
  {
    return false;
  }

  public function getOwner()
  {
    return $this;
  }
}
