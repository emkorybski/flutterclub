<?php

namespace bets;

require_once(PATH_LIB . 'dbrecord.php');
require_once(PATH_DOMAIN . 'event.php');

class Selection extends DBRecord {

	protected static $_table = 'fc_selection';

	public function getEvent() {
		return Event::get($this->idevent);
	}

	public function getSport() {
		return Sport::get($this->idsport);
	}

	public function getParent() {
		return Event::get($this->idparent);
	}

}

