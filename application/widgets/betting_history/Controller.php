<?php

require_once('custom/config.php');
require_once(PATH_DOMAIN.'user.php');
require_once(PATH_LIB.'fc.php');


class Widget_Betting_HistoryController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$this->view->history = $this->userBetsHistory();
	}
	
	function userBetsHistory(){
		$cId = $Id ? $Id : \bets\User::getCurrentUser()->id;
		$bets = \bets\Bet::findWhere(array('iduser=' => $cId));
		foreach ($bets as $bet){
			$obj->bet=$bet;
			$obj->betselection = $bet->getSelections();
			$obj->selection = \bets\Selection::get($obj->betselection[0]->idselection);
			$array[] = $obj;
			unset($obj);
		}
		return $array;
	}
}

