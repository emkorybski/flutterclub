<?php

namespace bets;

require_once(PATH_LIB . 'dbrecord.php');
require_once(PATH_DOMAIN . 'event.php');

class Sport extends DBRecord {

	protected static $_table = 'fc_sport';

	public function delete() {
		foreach ($this->getChildEvents() as $event) {
			$event->delete();
		}
		call_user_func_array('parent::delete', func_get_args());
	}

	public function getChildEvents() {
		return Event::findWhere(array('idsport=', $this->id));
	}

}

