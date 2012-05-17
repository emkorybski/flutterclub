<?php

namespace bets;

if (defined('BETS_INITIALIZED')) {
	return;
}

define('BETS_INITIALIZED', true);

// DB configuration
if ((!isset($_SERVER['REMOTE_ADDR'])) || ($_SERVER['REMOTE_ADDR'] == '127.0.0.1')) {
	define('BETS_DB_HOST', '127.0.0.1');
	define('BETS_DB_USER', 'root');
	define('BETS_DB_PASS', 'campofrio');
	define('BETS_DB_NAME', 'fc');
}
else {
	define('BETS_DB_HOST', 'mysql-shared-02.phpfog.com');
	define('BETS_DB_USER', 'Custom App-38630');
	define('BETS_DB_PASS', 'cx10u63r67GA');
	define('BETS_DB_NAME', 'flutterclub_phpfogapp_com');
}

// Paths
define('PATH_APP', realpath(dirname(__FILE__)) . '/');
define('PATH_LIB', PATH_APP . 'lib/');
define('PATH_DOMAIN', PATH_APP . 'domain/');
define('WEB_ROOT', '/fc/');

// Default includes
require_once(PATH_LIB . 'bets.php');

