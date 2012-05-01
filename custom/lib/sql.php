<?php

namespace bets;

require_once(PATH_LIB . 'singleton.php');

class sql extends Singleton {

	/**
	 * @var mysqli
	 */
	protected static $_link = null;

	protected function initialize() {
		call_user_func_array('parent::initialize', func_get_args());
		static::$_link = new \mysqli(BETS_DB_HOST, BETS_DB_USER, BETS_DB_PASS);
		$this->run('USE ' . BETS_DB_NAME);
	}

	/**
	 * Escapes the given $value using mysqli::real_escape_string
	 * @param string $value
	 * @return string
	 */
	public function escape($value) {
		return (is_null($value) ? 'NULL' : (is_int($value) ? $value : "'" . static::$_link->real_escape_string($value) . "'" ));
	}

	/**
	 * @param string $sql
	 * @return \mysqli_result
	 */
	public function run($sql) {
		return static::$_link->query($sql);
	}

	/**
	 * @param string $sql
	 * @return mixed[string][] Array of rows, each row is an associative array
	 */
	public function query($sql) {
		if (defined('SQLDIE')) {
			while (ob_get_level()) {
				ob_end_clean();
			}
			bets::debug($sql);
		}
		$mysqlResult = $this->run($sql);
		$result = array();
		for ($i = $mysqlResult->num_rows; $i; --$i) {
			$result[] = $mysqlResult->fetch_array(MYSQLI_ASSOC);
		}
		if ($mysqlResult) {
			$mysqlResult->free();
		}
		return $result;
	}

	/**
	 * Use this to get only one (the first) row of a query
	 * @param string $sql
	 * @return mixed[string] Associative array, key=column name, value=column value
	 */
	public function queryRow($sql) {
		$rows = $this->query($sql);
		return (count($rows) ? $rows[0] : null);
	}

	/**
	 * Use this to get only one (the first) field of the first row in a query
	 * @param string $sql
	 * @return mixed
	 */
	public function queryField($sql) {
		$row = array_values($this->queryRow($sql));
		return ($row && count($row) ? $row[0] : null);
	}

	/**
	 * @return int The value set to the AUTO_INCREMENT column in a query that just executed or 0 if there wasn't
	 */
	public function insertId() {
		return static::$_link->insert_id;
	}

}

