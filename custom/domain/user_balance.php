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

	public static function getCurrentBalance($forceInsert = true)
	{
		$competition = \bets\Competition::getCurrent();
		$idUser = \bets\User::getCurrentUser()->id;

		$balance = static::getWhere(array('idcompetition=' => $competition->id, 'iduser=' => $idUser));
		if (!$balance) {
			$balance = new UserBalance();
			$balance->idcompetition = $competition->id;
			$balance->iduser = $idUser;
			$balance->balance = $competition->start_points;
			if ($forceInsert) {
				$balance->insert();
			}
		}
		return $balance;
	}

	public static function updateUserBalance($stake)
	{
		$balance = self::getCurrentBalance();
		$balance->balance += $stake;
		$balance->update();
	}
}