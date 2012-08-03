<?php

namespace bets;

require_once(PATH_LIB . 'sql.php');

abstract class DBRecord extends Object {

	protected static $_table = null;
	protected static $_fieldNames = array();
	protected static $_instances = array();
	protected $_data = array();

	public static $SQLDIE = 0;
	
	/**
	 * Returns the database record with the given ID as a DBRecord object
	 * @param int $id
	 */
	public static function get($id, $customDbClass = null) {
		$className = ($customDbClass ? $customDbClass : get_called_class());
		if (isset(static::$_instances[$className][$id = (int) $id])) {
			return static::$_instances[$className][$id];
		}
		try {
			if (!isset(static::$_instances[$className])) {
				static::$_instances[$className] = array();
			}
			return static::$_instances[$className][$id] = new $className($id);
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Creates a MySQL "WHERE" clause from the given array by mixing it's keys and values and then glueing the pieces together with ' AND '
	 * @param string[string] $conditions An array like array('id>' => 3, 'ts=' => time())
	 * @return string
	 */
	protected static function conditionsToSQL($conditions) {
		$conditions = array_merge(array('deleted=' => 'n'), $conditions);
		$sql = '';
		foreach ($conditions as $cond => $value) {
			if (is_int($cond)) {
				throw new \Exception('You probably have an error in an SQL condition array. The correct form is array("cond"=>"value") not array("cond", "value")');
			}
			$value = bets::sql()->escape($value);
			$sql .= " AND ({$cond}{$value}) ";
		}
		return substr($sql, 4); // " AND"
	}

	/**
	 * Returns the first database record that satisfies all the given conditions
	 * @param string[] $conditions array('field1>' => $v1, 'field2=' => $v2, ..)
	 */
	public static function getWhere($conditions = array(), $extraQuery = '') {
		$table = static::$_table;
		$where = static::conditionstoSQL($conditions);
		$sql = "SELECT `{$table}`.`id` AS `id` FROM `{$table}` WHERE {$where} {$extraQuery} LIMIT 1";
		$result = bets::sql()->query($sql);
		return (count($result) ? static::get($result[0]['id']) : null);
	}

	public static function countWhere($conditions = array(), $extraQuery = '') {
		$table = static::$_table;
		$where = static::conditionstoSQL($conditions);
		$sql = "SELECT Count(`{$table}`.`id`) AS `cnt` FROM `{$table}` WHERE {$where} {$extraQuery}";
		$result = bets::sql()->query($sql);
		return $result[0]['cnt'];
	}

	/**
	 * Returns the number of rows in the table
	 * @param string $extraQuery
	 * @return int
	 */
	public static function count($extraQuery = '') {
		return static::countWhere(array(), $extraQuery);
	}

	/**
	 * Returns all the database records that satisfy all the given conditions
	 * @param string[] $conditions array('field1>' => $v1, 'field2=' => $v2, ..)
	 */
	public static function findWhere($conditions = array(), $extraQuery = '') {
		// find all ids
		$table = static::$_table;
		$where = static::conditionstoSQL($conditions);
		$sql = "SELECT `{$table}`.`id` AS `id` FROM `{$table}` WHERE {$where} {$extraQuery}";
		
		$rows = bets::sql()->query($sql);
		if (!count($rows)) {
			return array();
		}
		$ids = array();
		$missingIds = array();
		$className = get_called_class();
		$instances = &static::$_instances[$className];
		$row = null;
		foreach ($rows as &$row) {
			if (!isset($instances[$ids[] = $id = (int) $row['id']])) {
				$missingIds[] = $id;
			}
		}
		unset($row);
		// fill static::$_instances with missing records
		if (count($missingIds)) {
			$dbFieldNames = '';
			foreach (static::getFieldNames() as $fieldName) {
				$dbFieldNames .= ", `{$table}`.`{$fieldName}` AS `{$fieldName}`";
			}
			$dbFieldNames = substr($dbFieldNames, 2);
			$missingIdsStr = implode(', ', $missingIds);
			$missingRecords = bets::sql()->query("SELECT {$dbFieldNames} FROM `{$table}` WHERE (`{$table}`.`id` IN ({$missingIdsStr}))");
			$record = null;
			foreach ($missingRecords as &$record) {
				$instances[(int) $record['id']] = new $className(null, $record);
			}
			unset($record);
		}
		// return db records
		$result = array();
		foreach ($ids as $id) {
			$result[] = $instances[$id];
		}
		return $result;
	}

	/**
	 * Returns all the database records of this type
	 */
	public static function findAll($extraQuery = '') {
		return static::findWhere(array(), $extraQuery);
	}

	protected static function getFieldNames() {
		if (empty(static::$_fieldNames[$className = get_called_class()])) {
			static::$_fieldNames[$className] = array();
			foreach (bets::sql()->query('DESCRIBE `' . static::$_table . '`') as $rowDescription) {
				static::$_fieldNames[$className][] = $rowDescription['Field'];
			}
		}
		return static::$_fieldNames[$className];
	}

	/**
	 * Construct the database record object for the given ID
	 */
	protected function initialize($id = null, $customData = array()) {
		call_user_func_array('parent::initialize', func_get_args());
		if (empty($id)) {
			$this->_data = $customData;
			return;
		}
		$id = bets::sql()->escape($id);
		$table = static::$_table;

		$dbFieldNames = "`{$table}`.`id` AS `id`";
		foreach ($this->getFieldNames() as $fieldName) {
			$dbFieldNames .= ", `{$table}`.`{$fieldName}` AS `{$fieldName}`";
		}
		$result = bets::sql()->query("SELECT {$dbFieldNames} FROM `{$table}` WHERE (`{$table}`.`deleted` = 'n') AND (`{$table}`.`id` = {$id}) LIMIT 1");
		if (!count($result)) {
			throw new \Exception("Database error, element #{$id} not found in '{$table}'");
		}
		$this->_data = array_merge($result[0], $customData);
	}

	public function __set($field, $value) {
		if (in_array($field, static::getFieldNames())) {
			$this->_data[$field] = $value;
		} else {
			return call_user_func_array('parent::__set', func_get_args());
		}
	}

	public function __get($field) {
		return (array_key_exists($field, $this->_data) ? $this->_data[$field] : call_user_func_array('parent::__get', func_get_args()));
	}

	public function __unset($field) {
		if (array_key_exists($field, $this->_data)) {
			unset($this->_data[$field]);
		} else {
			call_user_func_array('parent::__unset', func_get_args());
		}
	}

	/**
	 * Insert the current database record object in the database and updates
	 * the ID field of the current object with the ID that has just been created
	 * @return int Returns the ID of this object
	 */
	public function insert() {
		$table = static::$_table;
		$sql = "INSERT INTO `{$table}` SET ";
		foreach ($this->_data as $field => $value) {
			if (is_null($value)) {
				$sql .= "`{$table}`.`{$field}` = NULL, ";
			} else {
				$value = bets::sql()->escape($value);
				$sql .= "`{$table}`.`{$field}` = {$value}, ";
			}
		}
		bets::sql()->run(substr($sql, 0, strlen($sql) - 2));
		$this->_data['id'] = intval(bets::sql()->insertId());
		static::$_instances[__NAMESPACE__ . "\\" . $table][$this->_data['id']] = $this;
		return $this->_data['id'];
	}

	/**
	 * Reset an object with the given ID (get a new DBRecord from the database) 
	 */
	public static function refresh($id, $customDbClass = null) {
		$id = (int) $id;
		$className = ($customDbClass ? $customDbClass : get_called_class());
		if (isset(static::$_instances[$className])) {
			unset(static::$_instances[$className]);
		}
		return static::get($id, $className);
	}

	/**
	 * Update the database with the values from the current database record object
	 */
	public function update() {
		$table = static::$_table;
		$sql = "UPDATE `{$table}` SET ";
		foreach ($this->_data as $field => $value) {
			$value = bets::sql()->escape($value);
			$sql .= "`{$table}`.`{$field}` = {$value}, ";
		}
		return bets::sql()->run(substr($sql, 0, strlen($sql) - 2) . " WHERE `{$table}`.`id` = {$this->_data['id']}");
	}

	/**
	 * Delete this database object from the database
	 */
	public function delete() {
		$table = static::$_table;
		$this->_data['deleted'] = 'y';
		$result = $this->update();
		unset(static::$_instances[$table][$this->id]);
		return $result;
	}

	public static function undelete($id) {
		$table = static::$_table;
		return bets::sql()->run("UPDATE `{$table}` SET `{$table}`.`deleted` = 'n' WHERE `{$table}`.`id` = '{$id}'");
	}

	/**
	 * @return string[string] The DB fields and values of the this object as associative array
	 */
	public function dbFields() {
		return $this->_data;
	}

}

