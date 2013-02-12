<?php

namespace bets;

class fc
{
	private static $oddsFormat = 'fractional';

	public static function isMobileVersion()
	{
		$isMobile = \Engine_Api::_()->hasModuleBootstrap('mobi') && \Engine_Api::_()->mobi()->isMobile();
		return $isMobile;
	}

	public static function getGMTTimestamp()
	{
		$datetime = new \DateTime();
		return $datetime->format('Y-m-d H:i:s');
	}

	public static function formatTimestamp($timestamp)
	{
		return date("j M Y, G:i T", strtotime($timestamp));
	}

	public static function formatDecimalNumber($number)
	{
		return number_format($number, 2, '.', ',');
	}

	public static function getPercentage($num, $den)
	{
		$percent1 = $num / $den;
		$percent2 = $percent1 * 100;
		return number_format($percent2, 2, '.', ',') . '%';
	}

	public static function getProfit($stake, $odds)
	{
		return $stake * ($odds - 1);
	}

	public static function formatOdds($dec, $oddsFormat = null)
	{
		$format = empty($oddsFormat) ? self::$oddsFormat : $oddsFormat;
		$dec = max(floatval($dec), 1);

		if ($format == 'fractional') {
			return self::decimal2fractional($dec);
		}

		if ($format == 'american') {
			return self::decimal2american($dec);
		}

		return number_format($dec, 2, '.', ',');
	}

	public static function decimal2fractional($dec)
	{
		$fractions = self::getFractions($dec);
		return $fractions[0] == $fractions[1]
			? 'Evs'
			: $fractions[0] . '/' . $fractions[1];
	}

	public static function decimal2american($dec)
	{
		return $dec;
	}

	public static function roundDecimalOdds($dec)
	{
		$fractions = self::getFractions($dec);
		return $fractions[0] == $fractions[1]
			? 2.00
			: 1 + round($fractions[0] / $fractions[1], 2);
	}

	private static function getFractions($dec)
	{
		$chart = self::$conversionChart;

		if ($dec < 10) {
			for ($i = 0; $i < count($chart); $i++) {
				if ($dec <= $chart[$i][0]) {
					return array($chart[$i][1], $chart[$i][2]);
				}
			}
		} else if ($dec < 40) {
			return array(intval(round($dec)), 1);
		} else {
			$quotient = floor($dec / 10);
			$reminder = $dec - $quotient * 10;
			$round = $reminder > 2.5
				? $reminder < 7.5
					? 5
					: 10
				: 0;

			return array($quotient * 10 + $round, 1);
		}
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
		array(2.06, 1, 1),
		array(2.22, 6, 5),
		array(2.30, 5, 4),
		array(2.40, 11, 8),
		array(2.62, 6, 4),
		array(2.90, 7, 4),
		array(3.19, 2, 1),
		array(3.39, 9, 4),
		array(3.59, 5, 2),
		array(3.79, 11, 4),
		array(4.34, 3, 1),
		array(4.64, 7, 2),
		array(5.34, 4, 1),
		array(5.64, 9, 2),
		array(6.34, 5, 1),
		array(6.64, 11, 2),
		array(7.34, 6, 1),
		array(7.64, 13, 2),
		array(8.34, 7, 1),
		array(8.64, 15, 2),
		array(9.34, 8, 1),
		array(9.64, 17, 2),
		array(9.99, 9, 1)
	);
}

//echo fc::roundDecimalOdds(1.73) . '<br/>';
//echo fc::decimal2fractional(1.73) . '<br/>';
//echo fc::getProfit(50, 32.5) . '<br/>';
//echo fc::getProfit(50, 6.5) . '<br/>';
//echo fc::getProfit(50, 1.44) . '<br/>';
//echo fc::getProfit(50, 2) . '<br/>';

