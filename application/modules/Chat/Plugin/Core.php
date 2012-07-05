<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Core.php 9451 2011-10-28 22:50:01Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John
 */
class Chat_Plugin_Core
{
  public function onRenderLayoutDefault($event)
  {
    // Arg should be an instance of Zend_View
    $view = $event->getPayload();
    $viewer = Engine_Api::_()->user()->getViewer();
    
    if( $view instanceof Zend_View && $viewer->getIdentity() ) {

      // Check if enabled
      $view->canChat = $canChat = Engine_Api::_()->authorization()->isAllowed('chat', $viewer, 'chat');
      $view->canIM = $canIM = Engine_Api::_()->authorization()->isAllowed('chat', $viewer, 'im');
      if( !$canIM ) return;

      // Check if friends-only or all members
      $memberIm = Engine_Api::_()->getApi('settings', 'core')->getSetting('chat.im.privacy', 'friends');
      $memberIm = 'everyone' === $memberIm
                ? 'true'
                : 'false';
      
      $identity = sprintf('%d', $viewer->getIdentity());
      $delay = Engine_Api::_()->getApi('settings', 'core')->getSetting('chat.general.delay', '5000');
      
      $canIM = ($canIM ? 'true' : 'false');
      $canChat = ($canChat ? 'true' : 'false');

      $script = <<<EOF
  var chatHandler;
  en4.core.runonce.add(function() {
    try {
      chatHandler = new ChatHandler({
        'baseUrl' : en4.core.baseUrl,
        'basePath' : en4.core.basePath,
        'identity' : {$identity},
        'enableIM' : {$canIM},
        'enableChat' : false,
        'imOptions' : { 'memberIm' : {$memberIm} },
        'delay' : {$delay}
      });

      chatHandler.start();
      window._chatHandler = chatHandler;
    } catch( e ) {
      //if( \$type(console) ) console.log(e);
    }
  });
EOF;
      
      $view->headScript()
        ->prependFile($view->layout()->staticBaseUrl . 'externals/soundmanager/script/soundmanager2'
           . (APPLICATION_ENV == 'production' ? '-nodebug-jsmin' : '' ) . '.js')
        ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Chat/externals/scripts/core.js')
        ->appendFile($view->layout()->staticBaseUrl . 'externals/mdetect/mdetect'
            . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
        ->appendScript($script);

      $view->headTranslate(array(
        'The chat room has been disabled by the site admin.', 'Browse Chatrooms',
        'You are sending messages too quickly - please wait a few seconds and try again.',
        '%1$s has joined the room.', '%1$s has left the room.', 'Settings',
        'Friends Online', 'None of your friends are online.', 
        'Members Online', 'No members are online.', 'Go Offline',
        'Open Chat', 'General Chat', 'Introduce Yourself', '%1$s person',
        'You',
      ));
    }
  }

  public function onRenderLayoutAdminDefault($event)
  {
    //return $this->onRenderLayoutDefault($event);
  }
  
  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( !$payload instanceof User_Model_User ) {
      return;
    }
    
    $db = Engine_Db_Table::getDefaultAdapter();
    
    try {
      // Delete bans
      $db->delete('engine4_chat_bans', array(
        'user_id = ?' => $payload->getIdentity(),
      ));

      // Delete events
      $db->delete('engine4_chat_events', array(
        'user_id = ?' => $payload->getIdentity(),
      ));

      // Delete messages
      $db->delete('engine4_chat_messages', array(
        'user_id = ?' => $payload->getIdentity(),
      ));

      // Delete room users
      $db->delete('engine4_chat_roomusers', array(
        'user_id = ?' => $payload->getIdentity(),
      ));

      // Delete online users
      $db->delete('engine4_chat_users', array(
        'user_id = ?' => $payload->getIdentity(),
      ));

      // Delete whispers
      $db->delete('engine4_chat_whispers', array(
        'recipient_id = ?' => $payload->getIdentity(),
      ));
      $db->delete('engine4_chat_whispers', array(
        'sender_id = ?' => $payload->getIdentity(),
      ));
      
      // Rebuild room counts
      $roomTable = Engine_Api::_()->getDbtable('rooms', 'chat');
      $roomTable->rebuildCounts();
      
    } catch( Exception $e ) {}
  }
}