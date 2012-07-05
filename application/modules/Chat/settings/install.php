<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: install.php 9301 2011-09-21 21:34:34Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Chat_Installer extends Engine_Package_Installer_Module
{
  public function onInstall()
  {
    $this->_addMainPage();
    
    parent::onInstall();
  }
  
  protected function _addMainPage()
  {
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);
    
    // Check if it's already been placed
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'chat_index_index')
      ->limit(1)
      ->query()
      ->fetchColumn(0);
      ;

    if( !$page_id ) {
      $db->insert('engine4_core_pages', array(
        'name' => 'chat_index_index',
        'displayname' => 'Chat Main Page',
        'title' => 'Chat',
        'description' => 'This is the chat room.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $container_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 3,
        'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');
      
      // middle column content
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.content',
        'parent_content_id' => $middle_id,
        'order' => 1,
        'params' => '',
      ));
    }
  }
}
?>