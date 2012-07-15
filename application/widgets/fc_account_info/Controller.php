<?php

require_once('custom/config.php');
require_once(PATH_DOMAIN . 'user.php');
require_once(PATH_DOMAIN . 'user_balance.php');

class Widget_FC_Account_InfoController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$this->view->user = \bets\User::getCurrentUser();
		$this->view->userBalance = \bets\UserBalance::getCurrentBalance();
	}
}