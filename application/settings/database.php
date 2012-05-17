<?php defined('_ENGINE') or die('Access Denied');

if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
  $db_params =   array (
    'host' => '127.0.0.1',
    'username' => 'root',
    'password' => 'campofrio',
    'dbname' => 'fc',
    'charset' => 'UTF8',
    'adapterNamespace' => 'Zend_Db_Adapter',
  );
}
else {
  $db_params =   array (
    'host' => 'mysql-shared-02.phpfog.com',
    'username' => 'Custom App-38630',
    'password' => 'cx10u63r67GA',
    'dbname' => 'flutterclub_phpfogapp_com',
    'charset' => 'UTF8',
    'adapterNamespace' => 'Zend_Db_Adapter',
  );
}

return array (
  'adapter' => 'mysqli',
  'params' => $db_params,
  'isDefaultTableAdapter' => true,
  'tablePrefix' => 'engine4_',
  'tableAdapterClass' => 'Engine_Db_Table',
); ?>