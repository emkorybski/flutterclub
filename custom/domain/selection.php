<?php

namespace bets;

require_once(PATH_LIB . 'dbrecord.php');
require_once(PATH_DOMAIN . 'event.php');
require_once(PATH_DOMAIN . 'sport.php');

class Selection extends DBRecord {

	protected static $_table = 'fc_selection';

	public function getEvent() {
		return Event::get($this->idevent);
	}

	public function setEvent($event) {
		$this->idevent = $event->id;
	}

	public function getSport() {
		return Sport::get($this->idsport);
	}

	public function setSport($sport) {
		$this->idsport = $sport->id;
	}

}

