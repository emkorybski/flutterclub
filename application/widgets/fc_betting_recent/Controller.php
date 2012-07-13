<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('custom/config.php');
require_once(PATH_DOMAIN . 'user.php');
require_once(PATH_LIB . 'fc.php');

class Widget_FC_Betting_RecentController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$this->view->recent_bets = \bets\User::getCurrentUser()->getRecentBets();
	}
}