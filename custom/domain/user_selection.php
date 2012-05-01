<?php

namespace bets;

require_once(PATH_LIB . 'dbrecord.php');
require_once(PATH_DOMAIN . 'user.php');
require_once(PATH_DOMAIN . 'selection.php');

class UserSelection extends DBRecord {

	protected static $_table = 'fc_user_selection';

	public function setUser($user) {
		$this->iduser = ($user ? $user->id : null);
	}

	public function getUser() {
		return User::get($this->iduser);
	}

	public function setSelection($selection) {
		$this->idselection = ($selection ? $selection->id : null);
	}

	public function getSelection() {
		return Selection::get($this->idselection);
	}

}

