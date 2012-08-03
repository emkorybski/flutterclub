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

	public static function getBalances($Object = null)
	{
		$idCompetition = $Object->idcompetition ? $Object->idcompetition : \bets\Competition::getCurrent()->id;
		return static::findWhere(array('idcompetition=' => $idCompetition), ' ORDER BY balance DESC');
	}

	public static function updateUserBalance($stake)
	{
		$balance = self::getCurrentBalance();
		$balance->balance += $stake;
		$balance->update();
	}
}