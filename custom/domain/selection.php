<?php

namespace bets;

require_once(PATH_LIB . 'dbrecord.php');
require_once(PATH_DOMAIN . 'sport.php');
require_once(PATH_DOMAIN . 'event.php');

class Selection extends DBRecord
{
	protected static $_table = 'fc_selection';

	public function getSport()
	{
		return Sport::get($this->idsport);
	}

	public function getEvent()
	{
		return Event::get($this->idevent);
	}
	
	public function getParent()
	{
		return Event::get($this->idevent)->getParent();
	}
	
	public function topEvent()
	{
		return Event::get($this->idevent)->topEvent();
	}

}