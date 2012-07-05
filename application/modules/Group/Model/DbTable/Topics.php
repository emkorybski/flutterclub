<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Topics.php 9509 2011-11-22 22:09:13Z shaun $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Group_Model_DbTable_Topics extends Engine_Db_Table
{
  protected $_rowClass = 'Group_Model_Topic';
  
  public function getChildrenSelectOfGroup($group, $params)
  {
    $select = $this->select()->where('group_id = ?', $group->group_id);
    return $select;
  }
}