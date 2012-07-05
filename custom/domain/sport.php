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
		return Event::findWhere(array('idsport=' => $this->id));
	}

	public function addChildEvent($event) {
		$event->idsport = $this->id;
	}

	public function initialize() {
		call_user_func_array('parent::initialize', func_get_args());
		$this->enabled = (isset($this->_data['enabled']) && ($this->enabled == 'y'));
	}

	public function update() {
		$this->enabled = ($this->enabled ? 'y' : 'n');
		call_user_func_array('parent::update', func_get_args());
		$this->enabled = ($this->enabled == 'y');
	}

	public function insert() {
		$this->enabled = ($this->enabled ? 'y' : 'n');
		call_user_func_array('parent::insert', func_get_args());
		$this->enabled = ($this->enabled == 'y');
	}

	public function computeVisibility() {
		foreach ($this->getChildEvents() as $childEvent) {
			if ($childEvent->computeVisibility()) {
				return true;
			}
		}
		return false;
	}

}

