<?php

class AutoFriender_AdminManageController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    // SAVE
    if ( isset( $_POST['friendusers'] ) ) {
      $friendUserEmailIds = $_POST['friendusers'];
      $friendUserEmailIds = explode(',', $friendUserEmailIds);
      $friendUserEmailIds = array_map('trim', $friendUserEmailIds);
      $friendUserIds = array();
      if ( count($friendUserEmailIds) ) {
        foreach ( $friendUserEmailIds as $friendUserEmailId ) {
          if ( $friendUserEmailId ) {
            $user = Engine_Api::_()->user()->getUser($friendUserEmailId);
            if ( $user->getIdentity() ) {
              $friendUserIds[] = $user->getIdentity();
            }
          }
        }
      }
      Engine_Api::_()->getApi('settings', 'core')->setSetting('auto.friender.friend_id', implode(',', $friendUserIds));
      
      $enable = isset($_POST['enable']) ? 1 : 0;
      Engine_Api::_()->getApi('settings', 'core')->setSetting('auto.friender.enable', $enable);
    }
    
		if ( isset($_POST['applyAll']) || $this->_getParam('next') ) {
			$limit = $this->_getParam('limit', 50);
			$friendUserIds = Engine_Api::_()->getApi('settings', 'core')->getSetting('auto.friender.friend_id', '');
			$friendUserIds = explode(',', $friendUserIds);
			
			$table = Engine_Api::_()->getDbTable('membership', 'user');
			$user_table = Engine_Api::_()->getDbTable('users', 'user')->info('name');
			$friend_table = Engine_Api::_()->getDbTable('membership', 'user')->info('name');
			foreach ( $friendUserIds as $friendUserId ) {
				$next = $this->_getParam('next', 0);
				for ( $i = 0; $i < $limit; $i++ ) {
					$select = Engine_Api::_()->getDbTable('users', 'user')->select()
						->setIntegrityCheck(false)
						->from($user_table, array('*'))
						->joinLeft($friend_table, $user_table.".user_id = ".$friend_table.".user_id", array())
						->where($user_table.".user_id > ".$next)
						->where($user_table.".user_id != ".$friendUserId)
						->where($friend_table.".resource_id != ".$friendUserId." OR ".$friend_table.".resource_id IS null")
						->limit(1);
					$row = Engine_Api::_()->getDbTable('users', 'user')->fetchRow($select);
					if ( $row ) {
						$user = Engine_Api::_()->user()->getUser($row->user_id);
						$friendUser = Engine_Api::_()->user()->getUser($friendUserId);
						$next = $user->getIdentity();
						if ( $user->getIdentity() && !$user->membership()->isMember($friendUser) && !$friendUser->membership()->isMember($user) ) {
							try {
								$arr1 = $table->createRow();
								$arr1->resource_id = $friendUser->getIdentity();
								$arr1->user_id = $user->getIdentity();
								$arr1->active = 1;
								$arr1->resource_approved = 1;
								$arr1->user_approved = 1;
								
								$arr2 = $table->createRow();
								$arr2->resource_id = $user->getIdentity();
								$arr2->user_id = $friendUser->getIdentity();
								$arr2->active = 1;
								$arr2->resource_approved = 1;
								$arr2->user_approved = 1;
								
								$arr1->save();
								$arr2->save();
								
								$user->member_count++;
								$user->save();
								$friendUser->member_count++;
								$friendUser->save();
							}
							catch ( Exception $e ) {
								// DO NOTHING
								echo $e->getMessage();
							}
						}
					}
					else {
						$this->view->status = "Auto-friendship has been applied to all existing users.";
						$next = null;
						break;
					}
				}
			}
			if ( $next ) {
				echo '<br />H: ';
				$user_count_select = Engine_Api::_()->getDbTable('users', 'user')->select();
				$user_count_select->from(Engine_Api::_()->getDbTable('users', 'user'), 'count(`user_id`) AS user_count');
				$user_number = Engine_Api::_()->getDbTable('users', 'user')->fetchRow($user_count_select);
				$this->view->status = "Up to user# ".$next." of total ".$user_number->user_count." users have been processed so far. Auto refreshing in 1 second...";
				//echo '<script type="text/javascript"> window.location="http://'.$_SERVER['HTTP_HOST'].$this->view->baseUrl().'/admin/auto-friender/manage/index/next/'.$next.'/limit/'.$limit.'" </script>';
			}
		}
		
    // GET USER ID's
    $friendUserIds = Engine_Api::_()->getApi('settings', 'core')->getSetting('auto.friender.friend_id', '');
    $friendUserIds = explode(',', $friendUserIds);
    $friendUsers = array();
    if ( is_array($friendUserIds) && count($friendUserIds) ) {
      foreach ( $friendUserIds as $friendUserId ) {
        $user = Engine_Api::_()->user()->getUser($friendUserId);
        if ( $user->getIdentity() ) {
          $friendUsers[] = $user;
        }
      }
    }
    $this->view->friendUsers = $friendUsers;
    $this->view->enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('auto.friender.enable', 0);;
    $this->view->applyAll = Engine_Api::_()->getApi('settings', 'core')->getSetting('auto.friender.applyAll', 0);;
  }
}
