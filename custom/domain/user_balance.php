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
		$competition = \bets\Competition::getCurrent();
		$idUser = \bets\User::getCurrentUser()->id;

		$balance = static::getWhere(array('idcompetition=' => $competition->id, 'iduser=' => $idUser));
		if (!$balance) {
			$balance = new UserBalance();
			$balance->idcompetition = $competition->id;
			$balance->iduser = $idUser;
			$balance->balance = $competition->start_points;
			$balance->insert();
		}
		return $balance;
	}

	public static function updateUserBalance($stake)
	{
		$balance = self::getCurrentBalance();
		$balance->balance += $stake;
		$balance->update();
	}

	public static function getBalancesCompetition($Object = null)
	{
		$idCompetition = $Object->idcompetition ? $Object->idcompetition : \bets\Competition::getCurrent()->id;
		$startPoints = \bets\Competition::getCurrent($idCompetition)->start_points;
		$admins = \bets\User::getAdminUsers();
		$extraQuery = !empty($admins) ? "AND p.iduser NOT IN (".implode(',', $admins).")" : '';
		$query = "SELECT
                                b.*, 
                                @rownum:=@rownum+1 as position 
                          FROM 
                                (SELECT 
                                    p.*, (p.balance-{$startPoints}) as earnings 
                                FROM 
                                    fc_user_balance p, 
                                    fc_competition c 
                                WHERE 
                                    p.idcompetition = c.id AND 
                                    p.idcompetition = {$idCompetition} 
									{$extraQuery} 
                                ORDER BY earnings DESC) b,  
                        (SELECT @rownum:=0) r";
		
		return \bets\bets::sql()->query($query);
	}
}