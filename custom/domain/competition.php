<?php

namespace bets;

require_once(PATH_LIB . 'dbrecord.php');
require_once(PATH_DOMAIN . 'sport.php');
require_once(PATH_DOMAIN . 'selection.php');

class Competition extends DBRecord
{
	protected static $_table = 'fc_competition';

	public function delete()
	{
		foreach ($this->getChildEvents() as $event) {
			$event->delete();
		}
		call_user_func_array('parent::delete', func_get_args());
	}

	public function getChildEvents()
	{
		return Event::findWhere(array('idparent=' => $this->id));
	}

	public function addChildEvent($event)
	{
		$event->idparent = $this->id;
	}

	public static function getCurrent()
	{
		$current = static::getWhere(array('ts_start<=' => date('Y-m-d 23:59:59')), 'ORDER BY ts_start DESC');
		if (!$current) {
			$current = static::getWhere(array(), 'ORDER BY ts_start DESC');
		}
		return $current;
	}
	
	
	public function getCompetitonPositions($uId = null){
		$data = \bets\UserBalance::getBalancesCompetition();
		$uid = $uId ? $uId : \bets\User::getCurrentUser()->id;
		foreach ($data as $obj){
			if ($obj['iduser'] == $uid)
				return $obj['position'];
		}
	}
}