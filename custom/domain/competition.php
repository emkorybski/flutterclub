<?php

namespace bets;

require_once(PATH_LIB . 'dbrecord.php');
require_once(PATH_DOMAIN . 'sport.php');
require_once(PATH_DOMAIN . 'selection.php');

class Competition extends DBRecord
{
	protected static $_table = 'fc_competition';

	public static function getCurrent()
	{
		$nowDatetime = new \DateTime();
		$now = $nowDatetime->format('Y-m-d H:i:s');
		$current = static::getWhere(array(
			'settled=' => 'n',
			'ts_start<=' => $now,
			'ts_end>=' => $now));
		if (!$current) {
			die("ERROR: NO COMPETITION DEFINED!!!");
		}
		return $current;
	}

	public static function getCurrentId()
	{
		$currentCompetition = self::getCurrent();
		return $currentCompetition->id;
	}
}
