<?php

namespace bets;

require_once(PATH_LIB . 'sql.php');
require_once(PATH_LIB . 'bets.php');

class SocialEngine
{
	public static function addActivityFeed($userId, $message)
	{
		// engine4_activity_actions
		$query = "INSERT INTO `engine4_activity_actions` (`type`, `subject_type`, `subject_id`, `object_type`, `object_id`, `body`, `params`, `date`, `attachment_count`, `comment_count`, `like_count`) VALUES " .
			"('status', 'user', $userId, 'user', $userId, '$message', '[]', UTC_TIMESTAMP(), 0, 0, 0)";
		bets::sql()->run($query);

		$actionId = bets::sql()->insertId();
		$actionDate = bets::sql()->queryField("SELECT `date` FROM `engine4_activity_actions` WHERE `action_id` = $actionId");


		// engine4_activity_stream
		$query = "INSERT INTO `engine4_activity_stream` (`target_type`, `target_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`, `action_id`) VALUES " .
			"('everyone', 0, 'user', $userId, 'user', $userId, 'status', $actionId)," .
			"('members', $userId, 'user', $userId, 'user', $userId, 'status', $actionId)," .
			"('owner', $userId, 'user', $userId, 'user', $userId, 'status', $actionId)," .
			"('parent', $userId, 'user', $userId, 'user', $userId, 'status', $actionId)," .
			"('registered', 0, 'user', $userId, 'user', $userId, 'status', $actionId);";
		bets::sql()->run($query);

		// engine4_core_status
		$query = "INSERT INTO `engine4_core_status` (`resource_type`, `resource_id`, `body`, `creation_date`) VALUES " .
			"('user', $userId, $message', '$actionDate'')";
		bets::sql()->run($query);


		// engine4_users
		$query = "UPDATE `engine4_users` SET `status` = '$message', `status_date` = '$actionDate' WHERE `user_id` = $userId";
		bets::sql()->run($query);
	}
}