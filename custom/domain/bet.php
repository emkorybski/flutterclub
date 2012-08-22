<?php

namespace bets;

require_once(PATH_LIB . 'dbrecord.php');
require_once(PATH_DOMAIN . 'bet_selection.php');

class Bet extends DBRecord
{
	protected static $_table = 'fc_bet';

	public function insert()
	{
		call_user_func_array('parent::insert', func_get_args());
	}

	public function getSelections()
	{
		return BetSelection::findWhere(array('idbet=' => $this->id), "ORDER BY name ASC");
	}
	
	public function getEarnings(){
		return $this->status == 'won' ? $this->stake * ($this->odds - 1) : '';
	}
	
	public static function getSuccessRate($uId = null){
		$uI = $uId ? $uId : \bets\User::getCurrentUser()->id;
		$uComp = \bets\Competition::getCurrent()->id;
		$countAll = self::countWhere(array('iduser=' => $uI, 'idcompetition=' => $uComp));
		$countWon = self::countWhere(array('iduser=' => $uI, 'idcompetition=' => $uComp, 'status=' => 'won'));
		$countPending = self::countWhere(array('iduser=' => $uI, 'idcompetition=' => $uComp, 'status=' => 'pending'));
		return $countWon.($countPending ? ' ('.$countPending.')' : '').'/'.$countAll;
	}
}
