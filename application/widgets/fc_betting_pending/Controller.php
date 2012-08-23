<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('custom/config.php');
require_once(PATH_DOMAIN . 'user.php');
require_once(PATH_LIB . 'fc.php');

class Widget_FC_Betting_PendingController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$this->view->pending_bets = \bets\User::getCurrentUser()->getPendingBets(5);
	}
}