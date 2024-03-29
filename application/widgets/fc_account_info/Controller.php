<?php

require_once('custom/config.php');
require_once(PATH_DOMAIN . 'competition.php');
require_once(PATH_DOMAIN . 'user.php');
require_once(PATH_DOMAIN . 'user_balance.php');

class Widget_FC_Account_InfoController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$this->view->user = \bets\User::getCurrentUser();
		$this->view->userBalance = \bets\UserBalance::getCurrentBalance(false);

		$competition = bets\Competition::getCurrent();
		$timeSpan = strtotime($competition->ts_end) - time();
		$days = floor($timeSpan / 86400);
		$hours = floor(($timeSpan - $days * 86400) / 3600);

		if ($days > 0) {
			$countdown = sprintf('%d %s, %d %s', $days, $days > 1 ? 'days' : 'day', $hours, $hours > 1 ? 'hours' : 'hour');
		} else if ($hours > 0) {
			$countdown = sprintf('%d %s', $hours, $hours > 1 ? 'hours' : 'hour');
		} else {
			$countdown = 'less then a hour';
		}
		$this->view->competitionCountdown = $countdown;
	}
}