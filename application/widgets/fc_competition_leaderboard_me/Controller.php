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
	
		
	
	
	
	//below is original code, above from fc_profile_statistics Controller
	
	
	
		$competition = \bets\Competition::getCurrent();
		$curUserId = Engine_Api::_()->user()->getViewer()->getIdentity();
		
		bets\bets::sql()->multiQuery("call fc_sp_get_competition_leaderboard_me(2," . $curUserId .")");
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
		
// user position calc from fc_profile_statistics starts	

		
		
		//$userProfit = $userStats['profit'];
		//$userPosition = $userPosition[0];
		
		//$this->view->ranking = $userPosition['pos'];

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
	}
}
