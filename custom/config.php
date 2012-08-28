<?php

namespace bets;

if (defined('BETS_INITIALIZED')) {
	return;
}

define('BETS_INITIALIZED', true);

// DB configuration
if (!isset($_SERVER['REMOTE_ADDR']) || ($_SERVER['REMOTE_ADDR'] == '127.0.0.1')) {
	define('BETS_DB_HOST', 'localhost');
	define('BETS_DB_USER', 'root');
	define('BETS_DB_PASS', '');
	define('BETS_DB_NAME', 'fc');
} else {
	define('BETS_DB_HOST', '5.79.7.99');
	define('BETS_DB_USER', 'fc_app');
	define('BETS_DB_PASS', 'zZ92u]0');
	define('BETS_DB_NAME', 'fc_demo');
}

// Paths
define('PATH_APP', realpath(dirname(__FILE__)) . '/');
define('PATH_LIB', PATH_APP . 'lib/');
define('PATH_DOMAIN', PATH_APP . 'domain/');
define('WEB_ROOT', '/fc/');

// Default includes
require_once(PATH_LIB . 'bets.php');
