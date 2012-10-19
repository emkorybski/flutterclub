<?php

namespace bets;

if (defined('BETS_INITIALIZED')) {
	return;
}
define('BETS_INITIALIZED', true);

define('BETS_DB_HOST', 'localhost');
define('BETS_DB_USER', 'fc_app');
define('BETS_DB_PASS', 'zZ92u]0');
define('BETS_DB_NAME', 'fc_live');

// Paths
define('PATH_APP', realpath(dirname(__FILE__)) . '/');
define('PATH_LIB', PATH_APP . 'lib/');
define('PATH_DOMAIN', PATH_APP . 'domain/');
define('WEB_HOST', 'http://www.flutterclub.com');
define('WEB_ROOT', '/fc/');

// Default includes
require_once(PATH_LIB . 'bets.php');
