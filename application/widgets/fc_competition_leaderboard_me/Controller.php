
<?php
require_once('custom/config.php');
require_once(PATH_LIB . 'fc.php');
require_once(PATH_DOMAIN . 'competition.php');
require_once(PATH_DOMAIN . 'user.php');
require_once(PATH_DOMAIN . 'user_balance.php');

class Widget_FC_Competition_Leaderboard_MeController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
	
	
	$curUserId = Engine_Api::_()->user()->getViewer()->getIdentity();
	
	//$comp = \bets\Competition::getCurrent();
	//$this->view->comp_id = $comp->id;
	
	//$subject = Engine_Api::_()->core()->getSubject();

		$competition = \bets\Competition::getCurrent();
		//$user = \bets\User::getUser($subject);
	
	/*
	if (!Engine_Api::_()->core()->hasSubject()) {
			$this->setNoRender();
			return;
		}
	
		$subject = Engine_Api::_()->core()->getSubject();
		$curUserId = Engine_Api::_()->user()->getViewer()->getIdentity();
		$competition = \bets\Competition::getCurrent();
		//$user = \bets\User::getUser($subject);
	*/
		//bets\bets::sql()->multiQuery("call fc_sp_get_user_stats($competition->id, $user->id)");
		bets\bets::sql()->multiQuery("call fc_sp_get_user_stats(7, $curUserId)");
		$userStats = bets\bets::sql()->getResult();
		while (bets\bets::sql()->moreResults()) {
			bets\bets::sql()->getResult();
			bets\bets::sql()->nextResult();
		}

		/* if (!isset($userStats[0])) {
			$this->view->ranking = '-';
			//$this->view->num_bets = 0;
			//$this->view->num_winning_bets = 0;
			//$this->view->num_losing_bets = 0;
			//$this->view->success_rate = '-';
			//$this->view->profit = \bets\fc::formatDecimalNumber(0);
			return;
		} */  //if uncommented returns 'No data yet..' message for prev. competiton (8)
	
		$userStats = $userStats[0];
		$userProfit = $userStats['profit'];
//$this->view->userStats = $userStats;

		//bets\bets::sql()->multiQuery("call fc_sp_get_user_position($competition->id, $userProfit)");
		bets\bets::sql()->multiQuery("call fc_sp_get_user_position(7, $userProfit)");
		$userPosition = bets\bets::sql()->getResult();
		while (bets\bets::sql()->moreResults()) {
			bets\bets::sql()->getResult();
			bets\bets::sql()->nextResult();
		}
		$userPosition = $userPosition[0];

		$this->view->user_pos = $userPosition['pos'];
		//echo $userPosition['pos'];
		/*
		//$this->view->num_bets = $userStats['bet_count'];
		//$this->view->num_winning_bets = $userStats['won_count'];
		//$this->view->num_losing_bets = $userStats['lost_count'];
		//$this->view->success_rate = \bets\fc::getPercentage($userStats['won_count'], $userStats['bet_count']);
		//$this->view->profit = \bets\fc::formatDecimalNumber($userProfit);
		
	
		*/
	
	//below is original code, above from fc_profile_statistics Controller
	
	
	
		//$competition = \bets\Competition::getCurrent();
		
		
		bets\bets::sql()->multiQuery("call fc_sp_get_competition_leaderboard_me(7, $curUserId)");
		//bets\bets::sql()->multiQuery("call fc_sp_get_competition_leaderboard_me($competition->id, $user->id)");
		$leaderboardData = bets\bets::sql()->getResult();
		while (bets\bets::sql()->moreResults()) {
			bets\bets::sql()->getResult();
			bets\bets::sql()->nextResult();
		}

		$seCurrentUser = Engine_Api::_()->user()->getViewer();
		
		$friends = Zend_Paginator::factory($seCurrentUser->membership()->getMembersOfSelect());

		$userFriendsIds = array($seCurrentUser->user_id);
		foreach ($friends as $friend) {
			$userFriendsIds[] = $friend->resource_id;
		}
		


		$leaderboard = array();
		$position = 1;
		foreach ($leaderboardData as $leaderboardUserData) {
			$fcUser = \bets\User::get($leaderboardUserData['iduser']);
			$seUser = Engine_Api::_()->user()->getUser($fcUser->id_engine4_users);

			if (!in_array($seUser->user_id, $userFriendsIds))
				continue;

			$successRate = \bets\fc::getPercentage($leaderboardUserData['won_count'], $leaderboardUserData['bet_count']);
			$userData = array(
				'position' => $position,
				'user' => $seUser,
				'profit' => number_format($leaderboardUserData['profit'], 2, '.', ','),
				'won_count' => $leaderboardUserData['won_count'],
				'bet_count' => $leaderboardUserData['bet_count'],
				'successRate' => $successRate);
			$leaderboard[] = $userData;
			$position++;
		}
		$this->view->leaderboardUsers = $leaderboard;
		//$this->view->user_position = $userData['position'];
	}
}
