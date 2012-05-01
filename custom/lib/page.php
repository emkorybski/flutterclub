<?php

namespace bets;

require_once(PATH_LIB . 'object.php');

//require_once(PATH_DOMAIN . 'user.php');

abstract class Page extends Object {

	public $template;
	public $name;
	public $user;
	public $allowedUsers = array('any', 'user', 'admin');

	public function initialize() {
//		$this->user = User::getCurrentUser();
		call_user_func_array(array('parent::initialize'), func_get_args());
	}

	protected function getVars() {
		return array();
	}

	private function getBaseVars() {
		return array('user' => $this->user);
	}

	public function display() {
		bets::display($this->template, array_merge($this->getBaseVars(), $this->getVars()));
	}

	public function submit($action, $args = array()) {
		// nothing
	}

	protected function enforceUserAuth() {
		return;
	}

	public function run() {
		$this->enforceUserAuth();
		if (!empty($_REQUEST['action'])) {
			$this->submit(strtolower($_REQUEST['action']), $_REQUEST);
		}
		$this->display();
	}

}

