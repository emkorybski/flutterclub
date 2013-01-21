<?php
$qstring_p1 = isset($_GET['id']);
$qstring_p2 = isset($_GET['ref']);


//echo "counter's value is: " . $counter;

if($qstring_p1 == 'fc' && $qstring_p2 == 'mon')
{

	
	header('location: http://www.flutterclub.com/fc/pages/signup-alt');
	//include('ext.html');
	
	$counter = "counter.txt";
	
	$handler_open = fopen($counter, 'r');
	
	$value =  fread($handler_open, 5);
	
	$read_value = (int) $value;
	
	$handler_open_write = fopen($counter, 'w');

	$value_incr = fwrite($handler_open_write, ++$read_value);
	
	//$value = ++$value;
	
	//fwrite($handler_open, $value."\r\n");
	
	fclose($handler_open_write);
	
	
} else {echo "Sorry, something wrong with your URL";}


?>
	
	