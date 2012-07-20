<?php

namespace bets;

require_once(PATH_LIB . 'dbrecord.php');
require_once(PATH_DOMAIN . 'sport.php');
require_once(PATH_DOMAIN . 'selection.php');

class Event extends DBRecord
{
	protected static $_table = 'fc_event';

	public function delete()
	{
		foreach ($this->getSubEvents() as $event) {
			$event->delete();
		}
		foreach ($this->getSelections() as $selection) {
			$selection->delete();
		}
		call_user_func_array('parent::delete', func_get_args());
	}

	public function getSubEvents()
	{
		return Event::findWhere(array('idparent=' => $this->id));
	}

	public function getSelections()
	{
		return Selection::findWhere(array('idevent=' => $this->id));
	}

	public function getSport()
	{
		return Sport::get($this->idsport);
	}

	public function getParent()
	{
		return Event::get($this->idparent);
	}

	public function topEvent()
	{
		$event = $this;
		while ($event->idparent) {
			$event = static::get($event->idparent);
		}

		return $event;
	}
}