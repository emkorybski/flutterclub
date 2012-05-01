<?php

namespace bets;

abstract class Object {

	public function __construct() {
		call_user_func_array(array($this, 'initialize'), func_get_args());
	}

	public function __destruct() {
		call_user_func_array(array($this, 'destroy'), func_get_args());
	}

	// override me
	protected function initialize() {
		
	}

	// override me
	protected function destroy() {
		
	}

}

