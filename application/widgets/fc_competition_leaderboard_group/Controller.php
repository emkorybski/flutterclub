<?php
require_once('custom/config.php');
require_once(PATH_LIB . 'fc.php');
require_once(PATH_DOMAIN . 'user.php');
require_once(PATH_DOMAIN . 'user_balance.php');

class Widget_FC_Competition_Leaderboard_GroupController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$competition = \bets\Competition::getCurrent();

		bets\bets::sql()->multiQuery("call fc_sp_get_competition_leaderboard_friends($competition->id)");
		$leaderboardData = bets\bets::sql()->getResult();
		while (bets\bets::sql()->moreResults()) {
			bets\bets::sql()->getResult();
			bets\bets::sql()->nextResult();
		}

		$group = Engine_Api::_()->core()->getSubject('group');
		$select = $group->membership()->getMembersObjectSelect();
		$paginator = Zend_Paginator::factory($select);

		$groupMembersIds = array();
		$count = 1;
		$break = false;
		for ($pageNo = 1; $pageNo <= $paginator->count(); $pageNo++) {
			$paginator->setCurrentPageNumber($pageNo);
			$groupMembers = $paginator->getCurrentItems();
			foreach ($groupMembers as $groupMember) {
				$seUserId = $groupMember->user_id;
				$fcUser = \bets\User::getWhere(array('id_engine4_users=' => $seUserId));
				if ($fcUser) {
					$groupMembersIds[] = $fcUser->id;
					$count++;
				}

				if ($count > 20) {
					$break = true;
					break;
				}
			}
			if ($break) break;
		}

		$leaderboard = array();
		$position = 1;
		foreach ($leaderboardData as $leaderboardUserData) {
			$fcUser = \bets\User::get($leaderboardUserData['iduser']);
			$seUser = Engine_Api::_()->user()->getUser($fcUser->id_engine4_users);

			if (!in_array($fcUser->id, $groupMembersIds))
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
