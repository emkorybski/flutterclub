<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('custom/config.php');
require_once(PATH_DOMAIN . 'competition.php');

class Widget_FC_Admin_MiscellaneousController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		if (!empty($_REQUEST['action'])) {
			$action = $_REQUEST['action'] . 'Action';
			$this->$action();
		}

		$competition = \bets\Competition::getCurrent();
		$this->view->competitionStartBalance = $competition->start_balance;
	}

	private function updateBalanceAction()
	{
		$balanceUpdateValue = floatval($_REQUEST['balance_update_value']);

		// update balances
		\bets\bets::sql()->run("UPDATE fc_user_balance SET balance = balance + $balanceUpdateValue");

		// update competition start balance
		$competition = \bets\Competition::getCurrent();
		$competition->start_balance += $balanceUpdateValue;
		$competition->update();
	}
}
