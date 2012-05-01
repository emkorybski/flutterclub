<?php

namespace bets;

if (defined('BETS_INITIALIZED')) {
	return;
}

define('BETS_INITIALIZED', true);

define('BETS_DEFAULT_PAGE_TITLE', 'Celsus Bets');
// DB configuration
define('BETS_DB_HOST', '127.0.0.1');
define('BETS_DB_USER', 'root');
define('BETS_DB_PASS', 'campofrio');
define('BETS_DB_NAME', 'fc');

// Paths
define('PATH_APP', realpath(dirname(__FILE__)) . '/');
define('PATH_LIB', PATH_APP . 'lib/');
define('PATH_DOMAIN', PATH_APP . 'domain/');
define('WEB_ROOT', '/fc/');

// Default includes
require_once(PATH_LIB . 'bets.php');

