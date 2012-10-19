<?php
class Friendsinviter_Plugin_Signup
{
  public function onUserCreateAfter($payload)
  {
    
    $viewer    = $payload->getPayload();
    
    $session = new Zend_Session_Namespace('Friendsinviter');
    if(isset($session->social) && $session->social) {
      $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
      $row = $inviteTable->fetchRow(array('code = ?' => $session->code));
      if($row) {
        $row->new_user_id = 0;
        $row->save();
      }
    }
    
    $referrer = Zend_Controller_Request_Http::getCookie('signup_referer');

    // Clear signup cookie
    setcookie("signup_referer", "", 0, "/");
    
    if(empty($referrer)) {
      return;
    }

    $user = Engine_Api::_()->user()->getUser($referrer);
    
    if(!$user->getIdentity()) {
      return;
    }
    

    $api = Engine_Api::_()->getApi('core', 'friendsinviter');

    // USER STATS
    $table = Engine_Api::_()->getDbTable('stats', 'friendsinviter');
    $db = $table->getAdapter();
    $tableName = $table->info("name");

    $sql = "INSERT INTO $tableName (user_id, invites_converted)
              VALUES ( ?, 1 )
              ON DUPLICATE KEY UPDATE
              invites_converted = invites_converted + 1";
    
    $values = array('user_id' => $user->getIdentity(),
                    );
    
    $db->query($sql, array_values($values));      

    // GLOBAL STATS
    $api->fi_update_stats("converted_invites", 1);


    // USER REFERER
    Engine_Api::_()->getDbtable('users', 'user')->update(array(
      'user_referer' => $user->getIdentity(),
    ), array(
      "user_id = {$viewer->getIdentity()}"
    ));

    $table = Engine_Api::_()->getDbTable('users', 'user');


    
    // skip if "no friends"
    $friend_settings = Engine_Api::_()->getApi('settings', 'core')->user_friends;
    if($friend_settings['eligible'] != 0) {

      try {

    $activity = Engine_Api::_()->getDbtable('notifications', 'activity');

    $viewer->membership()->addMember($user);
    $viewer->membership()->setUserApproved($user);
    $activity->addNotification($viewer, $user, $viewer, 'friend_request');
    
      } catch(Exception $ex) {
        
      }
      
  }
  
}
  
}
?>
