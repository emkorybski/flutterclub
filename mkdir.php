<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
echo '<pre>';

passthru('ls ' . sys_get_temp_dir());

/*

chdir(sys_get_temp_dir());
mkdir('flutter-club');
chdir('flutter-club');

$dirs = explode(' ', 'backup cache log package scaffold session package/archives package/manifests package/packages package/repositories package/sdk');
foreach ($dirs as $dir) {
	mkdir($dir);
	file_put_contents($dir.'/index.html', "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
  <head>
    <title></title>
    <meta http-equiv=\"refresh\" content=\"0;url=/\">
  </head>
  <body>
  </body>
</html>
");
}

file_put_contents('.session', "allow from none
deny from all
");

echo '<hr />DONE<hr />';

echo 'Dir: ' . getcwd() . '<br />';
foreach (glob('*') as $dir) {
    echo $dir.' ; ';
}

*/

