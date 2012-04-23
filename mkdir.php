<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
echo '<pre>';

chdir(sys_get_temp_dir());
mkdir('flutter-club');
chdir('flutter-club');
chmod('flutter-club', 0777);

ini_set('session.save_path', sys_get_temp_dir());

$dirs = explode(' ', 'backup cache log package scaffold session package/archives package/manifests package/packages package/repositories package/sdk');
foreach ($dirs as $dir) {
	echo '<hr />';
	echo "mkdir /tmp/{$dir}<br />";
	mkdir($dir);
	chmod($dir, 0777);
	file_put_contents($dir.'/index.html', '<meta http-equiv="refresh" content="0;url=/">');
}

file_put_contents('.session', "allow from none
deny from all
");

echo '<hr />DONE<hr />';

echo 'Dir: ' . getcwd() . '<br />';
echo '<br />';

passthru('ls -al ' . sys_get_temp_dir() . '/flutter-club');

phpinfo();

