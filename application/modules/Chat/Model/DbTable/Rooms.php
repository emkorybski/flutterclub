<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Rooms.php 9521 2011-11-30 02:20:07Z shaun $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John
 */
class Chat_Model_DbTable_Rooms extends Engine_Db_Table
{
  protected $_rowClass = 'Chat_Model_Room';
  
  public function rebuildCounts()
  {
    $this->update(array(
      'user_count' => new Zend_Db_Expr('(SELECT COUNT(*) FROM ' . 
          'engine4_chat_roomusers WHERE ' . 
          'engine4_chat_roomusers.room_id = engine4_chat_rooms.room_id ' . 
          'LIMIT 1)'),
    ), NULL);
    
    return $this;
  }
}