<?php

namespace bets;

require_once(PATH_LIB . 'dbrecord.php');
require_once(PATH_DOMAIN . 'user_selection.php');
require_once(PATH_DOMAIN . 'competition.php');

class User extends DBRecord {

	protected static $_table = 'fc_user';

	/** @return null|\bets\User */
	public static function getCurrentUser() {
		$socialId = (int) (\Zend_Auth::getInstance()->getIdentity());
		if (!$socialId) {
			return null;
		}
		$currentUser = static::getWhere(array('id_engine4_users=' => $socialId));
		if (!$currentUser) {
			$currentUser = new User();
			$currentUser->id_engine4_users = $socialId;
			$currentUser->points = 0;
			$currentUser->insert();
		}
		return $currentUser;
	}

	// /** @return \bets\UserSelection[] */
	// public function getUserSelections() {
		// return UserSelection::findWhere(array('iduser=' => $this->id));
	// }
	
	/** @return \bets\UserSelection[] */
	public function getUserSelectionsNotConfirmed() {
		return UserSelection::findWhere(array('iduser=' => $this->id, 'status=' => 'notconfirmed'));
	}
	
	/** @return \bets\UserSelection[] */
	public function getUserSelectionsPending() {
		return UserSelection::findWhere(array('iduser=' => $this->id, 'status=' => 'placed'));
	}
	
	/** @return \bets\UserSelection[] */
	public function getUserSelectionsSettled() {
		return UserSelection::findWhere(array('iduser=' => $this->id), " and status in ('settled', 'win', 'loss')");
	}

	public function getPoints() {
		$comp = Competition::getCurrent();
		if (!$comp) {
			// no active competition, no problem
			return 0;
		}
		$startPoints = $comp->start_points;

		$userSelections = UserSelection::findWhere(array('idcompetition=' => $comp->id, 'iduser=' => $this->id));
		$betAmount = 0;
		foreach ($userSelections as $sel) {
			$betAmount += $sel->bet_amount;// * $sel->odds;
		}

		return $startPoints - $betAmount;
	}

	public static function isLoggedIn() {
		return (bool) static::getCurrentUser();
	}

	public static function isInternal() {
		$user = static::getCurrentUser();
		return ($user ? $user->internal : 'n');
	}

	public function isAdmin() {
		return ($this->id == 1);
	}

}

