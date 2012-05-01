<?php


namespace bets;

abstract class Singleton extends Object {

	private static $_instances = array();
	private static $_allowInstantiation = false;

	protected function initialize() {
		if (!self::$_allowInstantiation) {
			$className = get_class($this);
			throw new Exception("Cannot instantiate class {$className} because it is a singleton.");
		}
		call_user_func_array('parent::initialize', func_get_args());
	}

	/**
	 * Returns the isntance of this class
	 * @return __CLASS__
	 */
	public static function getInstance() {
		$className = get_called_class();
		if ($className == __CLASS__) {
			throw new Exception('You cannot get the instance of the abstract Singleton class.');
		}
		if (!isset(self::$_instances[$className])) {
			$reflection = new \ReflectionClass($className);
			self::$_allowInstantiation = true;
			self::$_instances[$className] = $reflection->newInstanceArgs(func_get_args());
			self::$_allowInstantiation = false;
		}
		return self::$_instances[$className];
	}

}

