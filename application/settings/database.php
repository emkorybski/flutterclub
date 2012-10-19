<?php defined('_ENGINE') or die('Access Denied');
return array(
	'adapter' => 'mysqli',
	'params' =>
	array(
		'host' => 'localhost',
		'username' => 'fc_app',
		'password' => 'zZ92u]0',
		'dbname' => 'fc_live',
		'charset' => 'UTF8',
		'adapterNamespace' => 'Zend_Db_Adapter',
	),
	'isDefaultTableAdapter' => true,
	'tablePrefix' => 'engine4_',
	'tableAdapterClass' => 'Engine_Db_Table',
);
?>
