<?php

class Widget_Betting_HistoryController extends Engine_Content_Widget_Abstract
{
	
	public function indexAction()
	{
		require_once('custom/config.php');
		require_once(PATH_DOMAIN . 'user_selection.php');
		
		$currentUser = \bets\User::getCurrentUser();
		$this->view->userSelections = \bets\UserSelection::findWhere(array('iduser=' => $currentUser->id));
	/*
		$user = \bets\User::getCurrentUser();
	*/
	}
	
}

