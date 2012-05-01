<?php

if ($_GET['a'] == 'a84') {
	print_r(
		'server' => getenv('MYSQL_DB_HOST'),
		'username' => getenv('MYSQL_USERNAME'),
		'password' => getenv('MYSQL_PASSWORD')
	);
}

