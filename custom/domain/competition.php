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
		$current = static::getWhere(array('ts_start<=' => date('Y-m-d 23:59:59')), 'ORDER BY ts_start DESC');
		if (!$current) {
			$current = static::getWhere(array(), 'ORDER BY ts_start DESC');
		}
		return $current;
	}

	public static function getCurrentId()
	{
		$currentCompetition = self::getCurrent();
		return $currentCompetition->id;
	}
}
