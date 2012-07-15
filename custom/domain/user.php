<?php

namespace bets;

require_once(PATH_LIB . 'dbrecord.php');
require_once(PATH_DOMAIN . 'user_selection.php');
require_once(PATH_DOMAIN . 'competition.php');
require_once(PATH_DOMAIN . 'bet.php');

class User extends DBRecord
{
	protected static $_table = 'fc_user';

	public static function getCurrentUser()
	{
		$socialId = (int)(\Zend_Auth::getInstance()->getIdentity());
		if (!$socialId) return null;

		$currentUser = static::getWhere(array('id_engine4_users=' => $socialId));
		if (!$currentUser) {
			$currentUser = new User();
			$currentUser->id_engine4_users = $socialId;
			$currentUser->points = 0;
			$currentUser->insert();
		}
		return $currentUser;
	}

	public function getUserSelections()
	{
		return \bets\UserSelection::findWhere(array('iduser=' => $this->id));
	}

	public function getPendingBets()
	{
		return Bet::findWhere(array('iduser=' => $this->id, 'status=' => 'pending'));
	}

	public function getRecentBets()
	{
		return Bet::findWhere(array('iduser=' => $this->id, 'status!=' => 'pending'));
	}
}