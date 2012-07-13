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
			$selection->delete();
		}
		call_user_func_array('parent::delete', func_get_args());
	}

	public function getChildEvents() {
		return Event::findWhere(array('idparent=' => $this->id));
	}

	public function addChildEvent($event) {
		$event->idparent = $this->id;
	}

	public function getChildSelections() {
		return Selection::findWhere(array('idevent=' => $this->id));
	}

	public function addChildSelection($selection) {
		$selection->idevent = $this->id;
	}

	public function getSport() {
		return Sport::get($this->idsport);
	}

	public function setSport($sport) {
		$this->idsport = $sport->id;
	}

	public function getParent() {
		return Event::get($this->idparent);
	}

	public function setParent($parent) {
		$this->idparent = $parent->id;
	}

	public function topEvent() {
		$event = $this;
		while ($event->idparent) {
			$event = static::get($event->idparent);
		}

		return $event;
	}

	public function computeVisibility() {
		foreach ($this->getChildEvents() as $childEvent) {
			if ($childEvent->computeVisibility()) {
				return true;
			}
		}
		return Selection::countWhere(array('idevent=' => $this->id));
	}
}

