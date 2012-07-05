<?php

error_reporting(E_ALL);
ini_set('dispay_errors',1);
require_once('custom/config.php');
require_once(PATH_DOMAIN . 'user_selection.php');


class Widget_Fc_Betting_RecentController extends Engine_Content_Widget_Abstract {

	protected function getRecentBets(){
	    
	
	}
	
	public function indexAction() {
	    $this->view->recent = \bets\User::getCurrentUser()->getUserSelections();
	}
	
	
	
}

