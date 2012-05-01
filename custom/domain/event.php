<?php

namespace bets;

require_once(PATH_LIB . 'dbrecord.php');
require_once(PATH_DOMAIN . 'sport.php');
require_once(PATH_DOMAIN . 'selection.php');

class Event extends DBRecord {

	protected static $_table = 'fc_event';

	public function delete() {
		foreach ($this->getChildEvents() as $event) {
			$event->delete();
		}
		foreach ($this->getChildSelections() as $selection) {
			$event->delete();
		}
		call_user_func_array('parent::delete', func_get_args());
	}

	public function getChildEvents() {
		return Event::findWhere(array('idparent=' => $this->id));
	}

	public function getChildSelections() {
		return Selection::findWhere(array('idevent=' => $this->id));
	}

	public function getSport() {
		return Sport::get($this->idsport);
	}

	public function getParent() {
		return Event::get($this->idparent);
	}

}
