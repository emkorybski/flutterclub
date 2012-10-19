<?php

class AutoFriender_Plugin_Signup
{
  public function onUserCreateAfter($event)
  {
    if ( !Engine_Api::_()->getApi('settings', 'core')->getSetting('auto.friender.enable', 0) ) {
      return;
    }
    
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {
      $payloadUser = Engine_Api::_()->user()->getUser($payload->user_id);
      $friendUserIds = Engine_Api::_()->getApi('settings', 'core')->getSetting('auto.friender.friend_id', '');
      $friendUserIds = explode(',', $friendUserIds);
      if ( is_array($friendUserIds) && count($friendUserIds) ) {
        foreach ( $friendUserIds as $friendUserId ) {
          $user = Engine_Api::_()->user()->getUser($friendUserId);
          if ( $user->getIdentity() ) {
            $admin = Engine_Api::_()->user()->getUser($user->getIdentity());
            $table = Engine_Api::_()->getDbTable('membership', 'user');
            try {
              $arr1 = array(
                      'resource_id' => $admin->getIdentity(),
                      'user_id' => $payload->getIdentity(),
                      'active' => 1,
                      'resource_approved' => 1,
                      'user_approved' => 1
                      );
              
              $arr2 = array(
                      'resource_id' => $payload->getIdentity(),
                      'user_id' => $admin->getIdentity(),
                      'active' => 1,
                      'resource_approved' => 1,
                      'user_approved' => 1
                      );
              $table->insert($arr1);
              $table->insert($arr2);
              
              $admin->member_count++;
              $admin->save();
              
              $payloadUser->member_count++;
              $payloadUser->save();
            }
            catch ( Exception $e ) {
              echo '<br />'.$e->getMessage();
            }
          }
        }
      }
    }
  }
}