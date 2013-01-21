<?php

require_once('custom/config.php');
//require_once('../fc_profile_statistics/Controller.php');
require_once(PATH_DOMAIN . 'competition.php');
require_once(PATH_DOMAIN . 'get_fc_user.php');


class Widget_FC_User_PosController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
	
		//$subject = Engine_Api::_()->core()->setSubject();
	
		//$competition = \bets\Competition::getCurrent();
		
		$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		
		//$fc_user_id = \bets\Get_Fc_User::getFcUser();
		

		//bets\bets::sql()->multiQuery("call fc_sp_get_user_stats($competition->id,$user->id)");
		//$userStats = bets\bets::sql()->getResult();
		//$this->view->test = $user_id;
	
		$this->view->test2 = $competition->id;
		//$this->view->test3 = $userStats;
/*		
		while (bets\bets::sql()->moreResults()) {
			bets\bets::sql()->getResult();
			bets\bets::sql()->nextResult();
		}

		if (!isset($userStats[0])) {
			$this->view->ranking = '-';
			$this->view->num_bets = 0;
			$this->view->num_winning_bets = 0;
			$this->view->num_losing_bets = 0;
			$this->view->success_rate = '-';
			$this->view->profit = \bets\fc::formatDecimalNumber(0);
			return;
		}

		$userStats = $userStats[0];
		$userProfit = $userStats['profit'];

		bets\bets::sql()->multiQuery("call fc_sp_get_user_position($competition->id, $userProfit)");
		$userPosition = bets\bets::sql()->getResult();
		while (bets\bets::sql()->moreResults()) {
			bets\bets::sql()->getResult();
			bets\bets::sql()->nextResult();
		}
		$userPosition = $userPosition[0];

		$this->view->ranking = $userPosition['pos'];
		$this->view->num_bets = $userStats['bet_count'];
		$this->view->num_winning_bets = $userStats['won_count'];
		$this->view->num_losing_bets = $userStats['lost_count'];
		$this->view->success_rate = \bets\fc::getPercentage($userStats['won_count'], $userStats['bet_count']);
		$this->view->profit = \bets\fc::formatDecimalNumber($userProfit);
		
	*/
	
	
	}
}

