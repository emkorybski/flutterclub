<?php
namespace bets;

require_once(PATH_LIB . 'dbrecord.php');
//require_once(PATH_DOMAIN . 'user.php');

class UserFriends extends DBRecord {

	protected static $_table = 'engine4_user_membership';
	
	public static function getFriends()
	{
		$table = static::$_table;
		$Items = array();
		$user = \bets\User::getCurrentUser();
		$query ="
			SELECT resource_id, user_id FROM {$table} WHERE
			resource_id = {$user->id_engine4_users} AND
			active = 1 AND 
			resource_approved = 1 AND
			user_approved = 1";
		$listItems = \bets\bets::sql()->query($query);
		foreach ($listItems as $item){
			$item = (object) $item;
			$friend->id = $item->user_id;
			$friend->data = (object) \bets\User::getCurrentUserData($item->user_id);
			$Items[] = $friend;
		}
		return $Items;
	}
}
?>