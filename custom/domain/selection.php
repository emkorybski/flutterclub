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

	public static function updateResults()
	{
		$unprocessedResults = \bets\bets::sql()->query("SELECT * FROM fc_betfair_result WHERE deleted='n'");
		foreach($unprocessedResults as $result)
		{
			$betfairResult = \bets\BetfairResult::get($result['id']);
			$event = \bets\Event::getWhere(array('betfairMarketId=' => $betfairResult->betfairMarketId));
			if ($event) {
				\bets\bets::sql()->query("UPDATE fc_selection SET status='lost' WHERE idevent=" . $event->id);
				$winnerSelection = \bets\Selection::getWhere(array('idevent=' => $event->id, 'name=' => $betfairResult->winner));
				if ($winnerSelection) {
					$winnerSelection->status = 'won';
					$winnerSelection->update();
				}
			}
			$betfairResult->delete();
		}
	}

}

