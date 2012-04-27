<?php

namespace bets;

require_once(PATH_LIB . 'dbrecord.php');

class User extends DBRecord {

	protected static $_table = 'user';
	protected static $_salt = '4jGkb89Vp5KYupu6'; // https://random.org/
	protected static $_loginPage = 'user/login-request';

	public function isAdmin() {
		return ($this->id == 1);
	}

	public function login() {
		bets::session()->set('iduser', $this->id);
	}

	public static function logout() {
		bets::session()->un_set('iduser');
	}

	public static function getCurrentUser() {
		return static::getWhere(array('id=' => bets::session()->get('iduser')));
	}

	public static function isLoggedIn() {
		return (bool) static::getCurrentUser();
	}

	public static function internal() {
		$user = static::getCurrentUser();
		return ($user ? $user->internal : 'n');
	}

	public static function enforceAuth() {
		if (!static::isLoggedIn()) {
			bets::redirect(WEB_ROOT . static::$_loginPage);
		}
	}

	public static function enforceAuthAdmin() {
		static::enforceAuth();
		if (!static::getCurrentUser()->isAdmin()) {
			static::logout();
		}
		static::enforceAuth();
	}

	public static function getUserForLogin($email, $password) {
		return static::getWhere(array('email=' => $email, 'password=' => static::encryptPassword($password)));
	}

	public static function encryptPassword($password) {
		return sha1(str_rot13(static::$_salt . $password . sha1($password . static::$_salt)));
	}

	public function insert() {
		$this->confirm = substr(md5(microtime(true) . rand()), 0, 10);
		$oldSettings = $this->_data['settings'];
		$this->_data['settings'] = serialize($oldSettings);
		call_user_func_array('parent::insert', func_get_args());
		$this->_data['settings'] = $oldSettings;
	}

	public function update() {
		$oldSettings = $this->_data['settings'];
		$this->_data['settings'] = serialize($oldSettings);
		call_user_func_array('parent::update', func_get_args());
		$this->_data['settings'] = $oldSettings;
	}

	protected function initialize() {
		$this->_data['settings'] = serialize(array());
		call_user_func_array(array($this, 'parent::initialize'), func_get_args());
		$this->_data['settings'] = unserialize($this->_data['settings']);

		if (!isset($this->_data['settings']['selectedTests'])) {
			$this->_data['settings']['selectedTests'] = array();
		}
	}

	protected function destroy() {
		$this->_data['settings'] = serialize($this->_data['settings']);
		call_user_func_array(array($this, 'parent::destroy'), func_get_args());
	}

	public function setConfig($name, $value) {
		if (is_null($value)) {
			unset($this->_data['settings'][$name]);
		} else {
			$this->_data['settings'][$name] = $value;
		}
	}

	public function getConfig($name) {
		return (isset($this->_data['settings'][$name]) ? $this->_data['settings'][$name] : null);
	}

}

