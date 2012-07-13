<?php

require_once('custom/config.php');
require_once(PATH_DOMAIN . 'user_selection.php');
require_once(PATH_LIB . 'fc.php');

class Widget_Betting_HistoryController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$currentUser = \bets\User::getCurrentUser();
		$this->view->userSelections = \bets\UserSelection::findWhere(array('iduser=' => $currentUser->id));
	}
}

