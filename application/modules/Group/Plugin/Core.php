<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Core.php 8940 2011-05-14 01:19:58Z jung $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Group_Plugin_Core
{
  public function onStatistics($event)
  {
    $table = Engine_Api::_()->getItemTable('group');
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count');
    $event->addResponse($select->query()->fetchColumn(0), 'group');
  }

  public function onUserDeleteBefore($group)
  {
    $payload = $group->getPayload();
    if( $payload instanceof User_Model_User ) {
      // Delete posts
      $postTable = Engine_Api::_()->getDbtable('posts', 'group');
      $postSelect = $postTable->select()->where('user_id = ?', $payload->getIdentity());
      foreach( $postTable->fetchAll($postSelect) as $post ) {
        //$post->delete();
      }

      // Delete topics
      $topicTable = Engine_Api::_()->getDbtable('topics', 'group');
      $topicSelect = $topicTable->select()->where('user_id = ?', $payload->getIdentity());
      foreach( $topicTable->fetchAll($topicSelect) as $topic ) {
        //$topic->delete();
      }

      // Delete photos
      $photoTable = Engine_Api::_()->getDbtable('photos', 'group');
      $photoSelect = $photoTable->select()->where('user_id = ?', $payload->getIdentity());
      foreach( $photoTable->fetchAll($photoSelect) as $photo ) {
        $photo->delete();
      }

      // Delete officers
      $listItemTable = Engine_Api::_()->getDbtable('ListItems', 'group');
      $listItemSelect = $listItemTable->select()->where('child_id = ?', $payload->getIdentity());
      foreach( $listItemTable->fetchAll($listItemSelect) as $listitem ) {
        $list = Engine_Api::_()->getItem('group_list', $listitem->list_id);
        if( !$list ) {
          $listitem->delete();
          continue;
        }
        if( $list->has($payload) ) {
          $list->remove($payload);
        }
      }

      // Delete memberships
      $membershipApi = Engine_Api::_()->getDbtable('membership', 'group');
      foreach( $membershipApi->getMembershipsOf($payload) as $group ) {
        $membershipApi->removeMember($group, $payload);
      }

      // Delete groups
      $groupTable = Engine_Api::_()->getDbtable('groups', 'group');
      $groupSelect = $groupTable->select()->where('user_id = ?', $payload->getIdentity());
      foreach( $groupTable->fetchAll($groupSelect) as $group ) {
        $group->delete();
      }
    }
  }

  public function addActivity($event)
  {
    $payload = $event->getPayload();
    $subject = $payload['subject'];
    $object = $payload['object'];

    // Only for object=event
    if( $object instanceof Group_Model_Group &&
        Engine_Api::_()->authorization()->context->isAllowed($object, 'member', 'view') ) {
      $event->addResponse(array(
        'type' => 'group',
        'identity' => $object->getIdentity()
      ));
    }

  }

  public function getActivity($event)
  {
    // Detect viewer and subject
    $payload = $event->getPayload();
    $user = null;
    $subject = null;
    if( $payload instanceof User_Model_User ) {
      $user = $payload;
    } else if( is_array($payload) ) {
      if( isset($payload['for']) && $payload['for'] instanceof User_Model_User ) {
        $user = $payload['for'];
      }
      if( isset($payload['about']) && $payload['about'] instanceof Core_Model_Item_Abstract ) {
        $subject = $payload['about'];
      }
    }
    if( null === $user ) {
      $viewer = Engine_Api::_()->user()->getViewer();
      if( $viewer->getIdentity() ) {
        $user = $viewer;
      }
    }
    if( null === $subject && Engine_Api::_()->core()->hasSubject() ) {
      $subject = Engine_Api::_()->core()->getSubject();
    }

    // Get feed settings
    $content = Engine_Api::_()->getApi('settings', 'core')
      ->getSetting('activity.content', 'everyone');
    
    // Get event memberships
    if( $user ) {
      $data = Engine_Api::_()->getDbtable('membership', 'group')->getMembershipsOfIds($user);
      if( !empty($data) && is_array($data) ) {
        $event->addResponse(array(
          'type' => 'group',
          'data' => $data,
        ));
      }
    }
  }
}