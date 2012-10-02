<?php

require_once('custom/config.php');
require_once(PATH_DOMAIN . 'competition.php');
require_once(PATH_DOMAIN . 'user.php');

class Widget_FC_Profile_StatisticsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		if (!Engine_Api::_()->core()->hasSubject()) {
			$this->setNoRender();
			return;
		}

		$subject = Engine_Api::_()->core()->getSubject();

		$competition = \bets\Competition::getCurrent();
		$user = \bets\User::getUser($subject);

		bets\bets::sql()->multiQuery("call fc_sp_get_user_stats($competition->id, $user->id)");
		$userStats = bets\bets::sql()->getResult();
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
	}
}