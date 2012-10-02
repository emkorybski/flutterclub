<?php

require_once('custom/config.php');
require_once(PATH_DOMAIN . 'competition.php');
require_once(PATH_DOMAIN . 'user.php');

class Widget_FC_Profile_StatisticsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		// Test values
		$ranking = 10;
		$num_bets = 10;
		$num_winning_bets = 10;
		$num_losing_bets = 10;
		$success_rate = 50;
		$profit = 100;

		$this->view->ranking = $ranking;
		$this->view->num_bets = $num_bets;
		$this->view->num_winning_bets = $num_winning_bets;
		$this->view->num_losing_bets = $num_losing_bets;
		$this->view->success_rate = $success_rate;
		$this->view->profit = $profit;
	}
}