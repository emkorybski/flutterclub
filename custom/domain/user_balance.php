<?php

namespace bets;

require_once(PATH_LIB . 'dbrecord.php');

class UserBalance extends DBRecord
{
	protected static $_table = 'fc_user_balance';

	public function insert()
	{
		call_user_func_array('parent::insert', func_get_args());
	}

	public static function getCurrentBalance()
	{
		$idCompetition = \bets\Competition::getCurrent()->id;
		$idUser = \bets\User::getCurrentUser()->id;

		$balance = static::getWhere(array('idcompetition=' => $idCompetition, 'iduser=' => $idUser));
		if (!$balance) {
			$balance = new UserBalance();
			$balance->idcompetition = $idCompetition;
			$balance->iduser = $idUser;
			$balance->balance = 10000;
			$balance->insert();
		}
		return $balance;
	}

	public function updateBalance($stake)
	{
		$this->balance += $stake;
		call_user_func_array('parent::update', func_get_args());
	}
}