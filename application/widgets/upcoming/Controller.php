<?php

class Widget_UpcomingController extends Engine_Content_Widget_Abstract
{
	
	public function indexAction()
	{
		if (isset($_REQUEST['vote_selection_id'])) {
			$this->fc_vote_selection_id();
		}
		else {
			$this->fc_render();
		}
	}
	
	public function fc_render() {
		require_once('custom/config.php');
		require_once(PATH_DOMAIN . 'event.php');
		
		$cond = array('idparent=' => 0);
		
		$sport = (isset($_REQUEST['idsport']) ? (int)$_REQUEST['idsport'] : 0);
		if ($sport) {
			$cond['idsport='] = $sport;
		}
		$this->view->upcoming = \bets\Event::findWhere($cond);
	}
	
	public function fc_vote_selection_id() {
		require_once('custom/config.php');
		require_once(PATH_DOMAIN . 'user_selection.php');
		
		$user = \bets\User::getCurrentUser();
		$selection = \bets\Selection::get((int)$_REQUEST['vote_selection_id']);
		$bet_amount = (float)$_REQUEST['vote_amount'];
		if (!$user || !$selection || ($bet_amount < 1)) {
			exit; // internal error
		}
		if ($user->points < $bet_amount) {
			exit; // not enough points
		}
		
		$us = new \bets\UserSelection();
		$us->setUser($user);
		$us->setSelection(\bets\Selection::get($selection));
		$us->bet_amount = $bet_amount;
		$us->odds = $selection->odds;
		$us->insert();
		$user->points = $user->points - $bet_amount;
		$user->update();
		
		echo $us->id;
		exit();
	}
	
}

