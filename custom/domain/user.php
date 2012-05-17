<?php

namespace bets;

require_once(PATH_LIB . 'dbrecord.php');

class User extends DBRecord {

	protected static $_table = 'fc_user';

	public function login() {
	//	bets::session()->set('iduser', $this->id);
		throw new Exception('Can\'t login()');
	}

	public static function logout() {
	//	bets::session()->un_set('iduser');
		throw new Exception('Can\'t logout()');
	}

	public static function getCurrentUser() {
		$socialId = (int)(\Zend_Auth::getInstance()->getIdentity());
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

