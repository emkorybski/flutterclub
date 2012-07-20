<?php

namespace bets;

require_once(PATH_LIB . 'dbrecord.php');
require_once(PATH_DOMAIN . 'event.php');

class Sport extends DBRecord
{
	protected static $_table = 'fc_sport';

	public function insert()
	{
		call_user_func_array('parent::insert', func_get_args());
	}

	public function update()
	{
		call_user_func_array('parent::update', func_get_args());
	}

	public function delete()
	{
		foreach ($this->getEvents() as $event) {
			$event->delete();
		}
		call_user_func_array('parent::delete', func_get_args());
	}

	public function getEvents()
	{
		return Event::findWhere(array('idsport=' => $this->id));
	}
}