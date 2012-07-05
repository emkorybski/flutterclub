<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Groups.php 9086 2011-07-21 21:25:21Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Group_Model_DbTable_Groups extends Engine_Db_Table
{
  protected $_rowClass = 'Group_Model_Group';
  
  public function getGroupPaginator($params = array())
  {
    return Zend_Paginator::factory($this->getGroupSelect($params));
  }
  
  public function getGroupSelect($params = array())
  {
    $table = Engine_Api::_()->getItemTable('group');
    $select = $table->select();
    
    // Search
    if( isset($params['search']) ) {
      $select->where('search = ?', (bool) $params['search']);
    }
    
    // User-based
    if( !empty($params['owner']) && $params['owner'] instanceof Core_Model_Item_Abstract ) {
      $select->where('user_id = ?', $params['owner']->getIdentity());
    } else if( !empty($params['user_id']) ) {
      $select->where('user_id = ?', $params['user_id']);
    } else if( !empty($params['users']) && is_array($params['users']) ) {
      foreach( $params['users'] as &$id ) if( !is_numeric($id) ) $id = 0;
      $params['users'] = array_filter($params['users']);
      $select->where('user_id IN(\''.join("', '", $params['users']).'\')');
    }
    
    // Category
    if( !empty($params['category_id']) ) {
      $select->where('category_id = ?', $params['category_id']);
    }
    
    // Order
    if( !empty($params['order']) ) {
      $select->order($params['order']);
    } else {
      $select->order('creation_date DESC');
    }
    
    return $select;
  }
}