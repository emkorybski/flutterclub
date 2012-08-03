<?php
require_once('custom/config.php');
require_once(PATH_DOMAIN . 'competition.php');
require_once(PATH_DOMAIN . 'user_balance.php');
require_once(PATH_DOMAIN . 'user_friends.php');

class Widget_Fc_Competition_FriendsController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$this->view->friends = $this->getPositionsFriends();
		$this->view->position = bets\Competition::getCompetitonPositions();
	}
	public function getPositionsFriends(){
		$friends = bets\UserFriends::getFriends();
		foreach ($friends as $friend){
			$data->position = bets\Competition::getCompetitonPositions($friend->id);
			$data->friend = $friend->data;
			$data->successrate = bets\Bet::getSuccessRate($friend->id);
			$array[] = $data;
		}
		return $array;
	}	
}

