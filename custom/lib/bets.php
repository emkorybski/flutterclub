<?php

namespace bets;

require_once(PATH_LIB . 'object.php');
require_once(PATH_LIB . 'sql.php');

abstract class bets {

	protected static $_smarty;
	protected static $_oldErrorHandler;

	private function __construct() {
		// never allow instantiation of this class, so constructor is private
	}

	public static function initialize() {
		bets::sql();
	//	self::$_oldErrorHandler = set_error_handler(array(__CLASS__, 'handleError'), error_reporting());
	}

	/**
	 * @return \bets\sql
	 */
	public static function sql() {
		return sql::getInstance();
	}

	public static function debug($var, $exit = true) {
		echo '<pre>';
		print_r($var);
		if ($exit) {
			exit;
		}
		echo '</pre>';
	}

	public static function redirect($url) {
		header('Location: ' . $url);
		exit;
	}

	public static function handleError($errno, $errstr, $errfile, $errline, $errcontext) {
		$report = array(
			'Error number' => $errno,
			'Error message' => $errstr,
			'File name' => $errfile,
			'File line' => $errline
		);
		file_put_contents(dirname(__FILE__) . '/../../temporary/log/fc-errors', print_r($report, 1), FILE_APPEND);
		call_user_func_array(self::$_oldErrorHandler, func_get_args());
	}

}

bets::initialize();

