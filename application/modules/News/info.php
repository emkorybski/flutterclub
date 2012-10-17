<?php
$file_path = dirname(__FILE__);
$pos = strpos($file_path,DIRECTORY_SEPARATOR.'module'.DIRECTORY_SEPARATOR);
if( $pos!== false)
{
    $path = (dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'LoadDataFromFeed.php';    
}
else
{
    $path = ((dirname(__FILE__))).DIRECTORY_SEPARATOR.'LoadDataFromFeed.php';
}
echo "php ".$path;
?>
