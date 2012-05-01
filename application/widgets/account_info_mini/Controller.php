<?php

class Widget_Account_Info_MiniController extends Engine_Content_Widget_Abstract
{
	
	public function indexAction()
	{
		require_once('custom/config.php');
		require_once(PATH_DOMAIN . 'user.php');
		$this->view->user = \bets\User::getCurrentUser();
	}
	
}

