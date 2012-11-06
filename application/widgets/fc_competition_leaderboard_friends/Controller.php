<?php
require_once('custom/config.php');
require_once(PATH_LIB . 'fc.php');
require_once(PATH_DOMAIN . 'user.php');
require_once(PATH_DOMAIN . 'user_balance.php');

class Widget_FC_Competition_Leaderboard_FriendsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$competition = \bets\Competition::getCurrent();

		bets\bets::sql()->multiQuery("call fc_sp_get_competition_complete_leaderboard($competition->id)");
		$leaderboardData = bets\bets::sql()->getResult();
		while (bets\bets::sql()->moreResults()) {
			bets\bets::sql()->getResult();
			bets\bets::sql()->nextResult();
		}

		$seCurrentUser = Engine_Api::_()->user()->getViewer();
		$paginator = Zend_Paginator::factory($seCurrentUser->membership()->getMembersOfSelect());

		$seUserId = $seCurrentUser->user_id;
		$fcUser = \bets\User::getWhere(array('id_engine4_users=' => $seUserId));
		$userFriendsIds = array($fcUser->id);

		for ($pageNo = 1; $pageNo <= $paginator->count(); $pageNo++) {
			$paginator->setCurrentPageNumber($pageNo);
			$friends = $paginator->getCurrentItems();
			foreach ($friends as $friend) {
				$seUserId = $friend->resource_id;
				$fcUser = \bets\User::getWhere(array('id_engine4_users=' => $seUserId));
				if ($fcUser) {
					$userFriendsIds[] = $fcUser->id;
				}
			}
		}

		$leaderboard = array();
		$position = 1;
		foreach ($leaderboardData as $leaderboardUserData) {
			$fcUser = \bets\User::get($leaderboardUserData['iduser']);
			$seUser = Engine_Api::_()->user()->getUser($fcUser->id_engine4_users);

			if (!in_array($fcUser->id, $userFriendsIds))
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

			if ($position > 20) break;
		}
		$this->view->leaderboardUsers = $leaderboard;
	}
}
