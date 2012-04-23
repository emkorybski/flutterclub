<?php

ob_start();
echo '<pre>';
print_r($_SERVER);

sleep(3);

chdir('social');
require_once('index.php');

