<?php
require_once('custom/config.php');
require_once(PATH_DOMAIN . 'competition.php');
require_once(PATH_DOMAIN . 'user.php');
require_once(PATH_DOMAIN . 'user_balance.php');

class Widget_Fc_Competition_GlobalController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$this->_data = $this->getCompetitionGlobal();
		$this->view->winners = $this->_data;
		$this->view->position = $this->getCompetitonPosition();
	}

	public function getCompetitionGlobal(){
		$balaces = bets\UserBalance::getBalances();
		foreach ($balaces as $o){
			$count++;
			$user = bets\User::getCurrentUserData($o->iduser);
			$data->idcompetition = $o->idcompetition;
			$data->iduser = $o->iduser;
			$data->balance = $o->balance;
			$data->position = $count;
			$data->userdata = (object) array(			
				'name' => $user->displayname,
				'email' => $user->email,
				'locale' => $user->locale,
				'timezone'=> $user->timezone,
				
			) ;
			$array[] = $data;
			unset($data);
		}
		return $array;	
	}
	
	public function getCompetitonPosition(){
		$data = $this->_data;
		$uid = bets\User::getCurrentUser();
		foreach ($data as $obj){
			if ($obj->iduser == $uid->id)
				return $obj->position;
		}
	}
}

