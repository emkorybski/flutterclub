<?php

namespace bets;

class fc
{
	private static $oddsFormat = 'decimal';

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

	public static function  decimal2fractional($dec)
	{
		// TODO: revise, does not work everytime...
		$decBase = --$dec;
		$div = 1;
		do {
			$div++;
			$dec = $decBase * $div;
		} while (intval($dec) != $dec);

		if ($dec % $div == 0) {
			$dec = $dec / $div;
			$div = $div / $div;
		}
		return $dec . '/' . $div;
	}

	public static function  decimal2american($dec)
	{
		return $dec;
	}

}