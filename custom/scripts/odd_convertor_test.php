<?php
require_once(dirname(__FILE__) . '/../config.php');
require_once(PATH_LIB . 'fc.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>Flutterclub - Odd Convertor Test</title>
</head>
<body>
<?php
if (isset($_GET['dec'])) {
	$dec = floatval($_GET['dec']);
	echo number_format($dec, 2, '.', ',') . " => " . \bets\fc::decimal2fractional($dec);
	echo "<br/><br/>";
}
echo "Conversion Chart<br/>";
for ($dec = 1; $dec < 20; $dec += 0.01) {
	echo number_format($dec, 2, '.', ',') . " => " . \bets\fc::decimal2fractional($dec);
	echo "<br/>";
}
?>
</body>
</html>

