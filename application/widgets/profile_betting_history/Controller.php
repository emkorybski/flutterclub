<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('custom/config.php');
require_once(PATH_LIB . 'fc.php');
require_once(PATH_DOMAIN . 'user.php');

class Widget_Profile_Betting_HistoryController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$bettingHistory = \bets\User::getCurrentUser()->getBettingHistory();
		$this->view->bettingHistory = $bettingHistory;
	}
}
