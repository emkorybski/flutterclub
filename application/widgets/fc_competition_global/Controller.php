<?php
require_once('custom/config.php');

require_once(PATH_DOMAIN . 'competition.php');
require_once(PATH_DOMAIN . 'user.php');
require_once(PATH_DOMAIN . 'user_balance.php');

class Widget_Fc_Competition_GlobalController extends Engine_Content_Widget_Abstract {
	
	public function indexAction() {
		$this->view->winners = $this->getCompetitionGlobal();
		$this->view->position = bets\Competition::getCompetitonPositions();
	}

	public function getCompetitionGlobal(){
		$balances = bets\UserBalance::getBalancesCompetition();               
		foreach ($balances as $obj){
			$obj = (object) $obj;
			$user = (object) bets\User::getCurrentUserData($obj->iduser);
			$data->idcompetition = $obj->idcompetition;
			$data->iduser = $obj->iduser;
			$data->balance = $obj->balance;
			$data->position = $obj->position;
			$data->earnings = $obj->earnings;
			$data->successrate = bets\Bet::getSuccessRate($obj->iduser);
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
}

