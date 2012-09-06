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

	public function getPath($excludeSport = false)
	{
		$path = '';
		$event = $this;
		while ($event->idparent) {
			$event = static::get($event->idparent);
			$path = " > " . $event->name . $path;
		}
		$path = $excludeSport
			? substr($path, 3)
			: $this->getSport()->name . $path;

		return $path;
	}

	public function getSubEvents()
	{
		return Event::findWhere(array('idparent=' => $this->id));
	}

	public function getSelections($limit = null)
	{
		$extraQuery = ($limit != null ? " LIMIT $limit" : "");
		return Selection::findWhere(array('idevent=' => $this->id), $extraQuery);
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

	public static function bulkUpdate($events)
	{
		$stmt = null;

		foreach ($events as $event) {
			if (!$event->isDirty())
				continue;

			if (!$stmt) {
				!$stmt = bets::sql()->stmt_init();
				if (!$stmt) {
					throw new \Exception(bets::sql()->getLastError());
				}
				$stmt->prepare("UPDATE fc_event SET name = ?, ts = ? WHERE id = ?");
			}

			$name = $event->name;
			$ts = $event->ts;
			$id = $event->id;
			$stmt->bind_param('ssi', $name, $ts, $id);

			$stmt->execute();
		}

		if ($stmt) {
			$stmt->close();
		}
	}
}