<?php

namespace bets;

class fc
{
	private static $oddsFormat = 'fractional';

	public static function formatOdds($dec, $oddsFormat)
	{
		$format = empty($oddsFormat) ? self::$oddsFormat : $oddsFormat;
		$dec = max(floatval($dec), 1);

		if ($format == 'fractional') {
			return self::decimal2fractional($dec);
		}

		if ($format == 'american') {
			return self::decimal2american($dec);
		}

		return number_format($dec, 2, '.', ' ');
	}

	public static function decimal2fractional($dec)
	{
		$chart = self::$conversionChart;

		$decFloor = 0;
		if ($dec > 2) {
			$decFloor = floor($dec) - 1;
			$dec -= $decFloor;
		}
		for ($i = 0; $i < count($chart); $i++) {
			if ($dec <= $chart[$i][0]) {
				if ($decFloor == 0 && ($chart[$i][1] == $chart[$i][2])) {
					return 'Evs';
				} else {
					return ($chart[$i][1] + $decFloor * $chart[$i][2]) . " / " . $chart[$i][2];
				}
			}
		}
	}

	public static function decimal2american($dec)
	{
		return $dec;
	}

	private static $conversionChart = array(
		array(1.00, 0, 1),
		array(1.01, 1, 100),
		array(1.02, 1, 50),
		array(1.03, 1, 33),
		array(1.04, 1, 25),
		array(1.05, 1, 20),
		array(1.06, 1, 16),
		array(1.07, 1, 14),
		array(1.08, 1, 12),
		array(1.09, 1, 11),
		array(1.10, 1, 10),
		array(1.11, 1, 9),
		array(1.12, 1, 8),
		array(1.14, 1, 7),
		array(1.15, 2, 13),
		array(1.16, 1, 6),
		array(1.18, 2, 11),
		array(1.21, 1, 5),
		array(1.22, 2, 9),
		array(1.27, 1, 4),
		array(1.29, 2, 7),
		array(1.31, 3, 10),
		array(1.35, 1, 3),
		array(1.37, 4, 11),
		array(1.42, 2, 5),
		array(1.46, 4, 9),
		array(1.52, 1, 2),
		array(1.54, 8, 15),
		array(1.57, 4, 7),
		array(1.59, 3, 5),
		array(1.63, 8, 13),
		array(1.69, 4, 6),
		array(1.75, 8, 11),
		array(1.82, 4, 5),
		array(1.87, 5, 6),
		array(1.93, 10, 11),
		array(2.00, 1, 1)
	);
}
