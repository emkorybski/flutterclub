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
    
    public static function bulkUpdate($selections)
    {
        $stmt = null;

        foreach($selections as $selection)
        {
            if (!$selection->isDirty())
                continue;

            if (!$stmt) {
                $stmt =  bets::sql()->stmt_init();
                if (!$stmt) {
                    throw new \Exception(bets::sql()->getLastError());
                }
                $stmt->prepare("UPDATE fc_selection SET odds = ? WHERE id = ?");
            }

            $odds = $selection->odds;
            $id = $selection->id;
            $stmt->bind_param('di', $odds, $id);

            $stmt->execute();
        }

        if ($stmt) {
            $stmt->close();
        }
    }

    public static function bulkInsert($selections)
    {
        $stmt = null;

        foreach($selections as $selection)
        {
            if (!$selection->isNew())
                continue;

            if (!$stmt) {
                $stmt = bets::sql()->stmt_init();
                if (!$stmt) {
                    throw new \Exception(bets::sql()->getLastError());
                }
                $stmt->prepare(
                    "INSERT INTO fc_selection(idevent, name, odds, betfairSelectionId) " .
                        "VALUES(?, ?, ?, ?)");
            }
            
            $idevent = $selection->idevent;
            $name = $selection->name;
            $odds = $selection->odds;
            $betfairSelectionId = $selection->betfairSelectionId;
            $stmt->bind_param('isdi', $idevent, $name, $odds, $betfairSelectionId);

            $stmt->execute();
        }

        if ($stmt) {
            $stmt->close();
        }
    }
}