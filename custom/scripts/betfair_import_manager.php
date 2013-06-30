<?php

namespace betfair;

require_once(dirname(__FILE__) . '/../config.php');
require_once(PATH_LIB . 'object.php');
require_once(PATH_LIB . 'fc.php');
require_once(PATH_DOMAIN . 'competition.php');
require_once(PATH_DOMAIN . 'sport.php');
require_once(PATH_DOMAIN . 'event.php');
require_once(PATH_DOMAIN . 'selection.php');

class BetfairImportManager
{
	private $excludedEvents = array('ANTEPOST', 'ASIAN HANDICAP', 'Ante Post', 'Forecast Betting', 'Reverse Forecast');
	private $excludedSubEvents = array('MO - DRAW NO BET', 'DRAW NO BET', 'NEXT GOAL', 'Reverse FC', 'Forecast');
	private $excludedEventsContaining = array('Match Bets');

	private $soapClient;
	private $sessionToken;

	private $eventsById;
	private $eventsByParentAndName;
	private $eventsByBetfairMarketId;

	private $selectionsById;
	private $selectionsByEventAndName;

	private $competition;

	public function __construct()
	{
		$this->competition = \bets\Competition::getCurrent();
		$this->soapClient = new \SoapClient("https://api.betfair.com/global/v3/BFGlobalService.wsdl");
		$this->login();
	}

	private static function log($message)
	{
		//file_put_contents('betfair_import_manager.log', $message . "\r\n", FILE_APPEND | LOCK_EX);
	}

	public static function setRunning($value)
	{
		// running status needs to be saved in database
		return false;
	}

	public static function getRunning()
	{
		// running status needs to be saved in database
		return false;
	}

	private function addEvent($event)
	{
		if (!$event->isNew()) {
			$this->eventsById[$event->id] = $event;
		} else {
			$this->eventsById[] = $event;
		}

		$this->eventsByParentAndName[$event->idparent . "_" . $event->name] = $event;

		if ($event->betfairMarketId) {
			$this->eventsByBetfairMarketId[$event->betfairMarketId] = $event;
		}
	}

	private function getEventByParentAndName($idParent, $eventName)
	{
		if (array_key_exists($idParent . "_" . $eventName, $this->eventsByParentAndName)) {
			return $this->eventsByParentAndName[$idParent . "_" . $eventName];
		}
		return null;
	}

	private function getEventByBetfairMarketId($betfairMarketId)
	{
		if ($betfairMarketId && array_key_exists($betfairMarketId, $this->eventsByBetfairMarketId)) {
			return $this->eventsByBetfairMarketId[$betfairMarketId];
		}
		return null;
	}

	private function addSelection($selection)
	{
		if (!$selection->isNew()) {
			$this->selectionsById[$selection->id] = $selection;
		} else {
			$this->selectionsById[] = $selection;
		}

		$this->selectionsByEventAndName[$selection->idevent . "_" . $selection->betfairSelectionId . "_" . $selection->name] = $selection;
	}

	private function getSelectionByEventAndName($idEvent, $name, $betfairSelectionId)
	{
		if (array_key_exists($idEvent . "_" . $betfairSelectionId . "_" . $name, $this->selectionsByEventAndName)) {
			return $this->selectionsByEventAndName[$idEvent . "_" . $betfairSelectionId . "_" . $name];
		}
		return null;
	}


	public function login()
	{
		// login
		$soapRequest = new \stdClass();
		$soapRequest->username = "thefly01";
		$soapRequest->password = "jf123456";
		$soapRequest->productId = 82;
		$soapRequest->ipAddress = '';
		$soapRequest->locationId = 0;
		$soapRequest->vendorSoftwareId = 0;
		$response = $this->soapClient->login(array('request' => $soapRequest))->Result;

		if ($response->errorCode != 'OK') {
			var_dump(debug_backtrace());
			var_dump($response);
			die("ERROR!");
		}

		$this->sessionToken = $response->header->sessionToken;
	}

	public function importSportsAndEvents()
	{
		$this->getActiveEventTypes();
	}

	public function importEventsAndSelections($url)
	{
		gc_enable();
		\bets\bets::sql()->autocommit(false);

		preg_match_all('#href="([^"]*SportName[^"]*)"#', $this->loadUrlContent($url), $matches);
		$count = count($matches[1]);
		foreach ($matches[1] as $nr => $match) {
			if ($nr % 4 != 0) continue;

			$xmlContent = $this->loadUrlContent($match);
			$xmlObject = simplexml_load_string($xmlContent);

			$attributes = $xmlObject->attributes();
			$sportName = $attributes['sport'] . '';
			$sport = \bets\Sport::getWhere(array('name=' => $sportName));
			if (!$sport) {
				var_dump(debug_backtrace());
				var_dump($sportName);
				die("ERROR!");
			}

			$this->parseEvents($sport, $xmlObject->event);
		}

//		$sportRows = \bets\bets::sql()->query("SELECT * FROM fc_sport");
//		foreach ($sportRows as $sportRow) {
//			$sportId = $sportRow['id'];
//			$sportName = urlencode($sportRow['name']);
//			$betfairSportId = $sportRow['betfairSportId'];
//
//			$marketDataUrl = "http://www.betfair.com/partner/marketData_loader.asp?fa=ss&id=$betfairSportId&Type=B";
//			$xmlContent = $this->getUrlContent($marketDataUrl);
//			$xmlObject = simplexml_load_string($xmlContent);
//		}

		\bets\bets::sql()->autocommit(true);
		gc_disable();
	}

	private function getActiveEventTypes()
	{
		\bets\bets::sql()->autocommit(false);

		$soapRequest = new \stdClass();
		$soapRequest->header = new \stdClass();
		$soapRequest->header->sessionToken = $this->sessionToken;
		$soapRequest->header->clientStamp = 0;
		$response = $this->soapClient->getActiveEventTypes(array('request' => $soapRequest))->Result;
		if ($response->errorCode != 'OK') {
			var_dump(debug_backtrace());
			var_dump($response);
			die("ERROR!");
		}

		foreach ($response->eventTypeItems->EventType as $eventType) {
			$sport = \bets\Sport::getWhere(array('name=' => trim($eventType->name)));
			if (!$sport) {
				$sport = new \bets\Sport(null, array('name' => trim($eventType->name), 'betfairSportId' => $eventType->id));
				$sport->insert();
			}

			$competitions = $this->getCompetitions($sport);
			foreach ($competitions as $competition) {

				if ($competition->betfairEventId == null) {
					continue;
				}

				$this->eventsById = array();
				$this->eventsByParentAndName = array();
				$this->eventsByBetfairMarketId = array();

				var_dump($sport->name);
				var_dump($competition->name);
				$events = \bets\Event::findWhere(array('idcomp=' => $competition->id));
				foreach ($events as $event) {
					$this->addEvent($event);
				}
				var_dump(count($events));
				echo '<br/>';

				$this->getEvents($competition->betfairEventId, $sport->id, $competition->id, $competition->id);

				\bets\Event::clearCache();
				$this->eventsById = null;
				$this->eventsByParentAndName = null;
				$this->eventsByBetfairMarketId = null;

			}
		}

		\bets\bets::sql()->commit();
		\bets\bets::sql()->autocommit(true);
	}

	private function getCompetitions($sport)
	{
		// getCompetitions
		$soapRequest = new \stdClass();
		$soapRequest->header = new \stdClass();
		$soapRequest->header->sessionToken = $this->sessionToken;
		$soapRequest->header->clientStamp = 0;
		$soapRequest->eventParentId = $sport->betfairSportId;
		$response = $this->soapClient->getEvents(array('request' => $soapRequest))->Result;

		$competitionsByName = array();

		$competitions = \bets\Event::findWhere(array('idsport=' => $sport->id, 'idparent=' => 0));
		foreach ($competitions as $competition) {
			$competitionsByName[$competition->name] = $competition;
		}

		if ($response->errorCode == 'OK' && property_exists($response->eventItems, 'BFEvent')) {
			$bfCompetitions = is_object($response->eventItems->BFEvent)
				? array($response->eventItems->BFEvent)
				: $response->eventItems->BFEvent;
			foreach ($bfCompetitions as $bfCompetition) {

				$competitionName = trim($bfCompetition->eventName);

				$competition = null;
				if (array_key_exists($competitionName, $competitionsByName)) {
					$competition = $competitionsByName[$competitionName];
				} else {
					$competition = new \bets\Event(null, array('name' => $competitionName, 'idparent' => 0, 'betfairMarketId' => null));
					$competition->idsport = $sport->id;
					$competition->betfairEventId = $bfCompetition->eventId;
					$idComp = $competition->insert();
					$competition->idcomp = $idComp;
					$competition->update();

					$competitions[] = $competition;
					$competitionsByName[$competition->name] = $competition;
				}

				$competition->betfairEventId = $bfCompetition->eventId;
			}
		}

		return $competitions;
	}

	private function getEvents($betfairParentId, $sportId, $competitionId, $fcParentId)
	{
		// getEvents
		$soapRequest = new \stdClass();
		$soapRequest->header = new \stdClass();
		$soapRequest->header->sessionToken = $this->sessionToken;
		$soapRequest->header->clientStamp = 0;
		$soapRequest->eventParentId = $betfairParentId;
		$response = $this->soapClient->getEvents(array('request' => $soapRequest))->Result;

		if ($response->errorCode == 'OK' && property_exists($response->eventItems, 'BFEvent')) {
			$bfEvents = is_object($response->eventItems->BFEvent)
				? array($response->eventItems->BFEvent)
				: $response->eventItems->BFEvent;
			foreach ($bfEvents as $bfEvent) {
				$eventName = trim($bfEvent->eventName);

				if (in_array(strtoupper($eventName), $this->excludedEvents)) {
					continue;
				}

				$isExcludedEvent = false;
				foreach ($this->excludedEventsContaining as $exclude) {
					if (strstr($eventName, $exclude) != false) {
						$isExcludedEvent = true;
						break;
					}
				}
				if ($isExcludedEvent)
					continue;

				//$event = \bets\Event::getWhere(array('name=' => trim($bfEvent->eventName), 'idparent=' => $fcParentId));
				$event = $this->getEventByParentAndName($fcParentId, $eventName);
				if (!$event) {
					$event = new \bets\Event(null, array('name' => $eventName, 'idparent' => $fcParentId, 'betfairMarketId' => null));
					$event->idsport = $sportId;
					$event->idcomp = $competitionId;
					$parentId = $event->insert();
					$this->addEvent($event);
				} else {
					$parentId = $event->id;
				}
				$this->getEvents($bfEvent->eventId, $sportId, $competitionId, $parentId);
			}
		}
	}

	private function parseEvents($sport, $bfEvents)
	{
		$this->log('Parsing events for sport: ' . $sport->name);

		$competitions = \bets\Event::findWhere(array('idparent=' => 0));
		foreach ($competitions as $competition) {
			$idCompetition = $competition->idcomp;

			$this->eventsById = array();
			$this->eventsByParentAndName = array();
			$this->eventsByBetfairMarketId = array();

			$this->log('Searching for events...');
			$events = \bets\Event::findWhere(array('idsport=' => $sport->id, 'idcomp=' => $idCompetition), ' AND ((betfairMarketId IS NULL) OR (ts IS NULL) or (ts > \'2013-01-01\')) ');
			$this->log('Found ' . count($events) . ' events');

			$this->log('Loading events...');
			foreach ($events as $event) {
				$this->addEvent($event);
			}
			$this->log('Loaded ' . count($this->eventsById) . ' events');

			foreach ($bfEvents as $bfEvent) {
				$attributes = $bfEvent->attributes();
				$eventName = trim($attributes['name'] . '');
				//$eventDate = date('Y-m-d 00:00:00', \DateTime::createFromFormat('d/m/Y', $attributes['date'])->getTimestamp());

				$eventsList = explode('/', $eventName);

				$idParent = 0;
				$eventName = '';
				for ($i = 0; $i < count($eventsList); $i++) {
					$eventName .= trim($eventsList[$i]);
					//$event = \bets\Event::getWhere(array('idsport=' => $sport->id, 'idparent=' => $idParent, 'name=' => $eventName));
					$event = $this->getEventByParentAndName($idParent, $eventName);
					if (!$event) {
						$eventName .= '/';
						$eventFound = false;
					} else {
						$idParent = $event->id;
						$eventName = '';
						$eventFound = true;
					}
				}

				if (!$eventFound || in_array(strtoupper($event->name), $this->excludedEvents)) {
					continue;
				}

				$this->log('Parsing subevents for event ' . $event->name . '...');
				if (count($bfEvent->subevent) > 0) {
					$this->parseSubEvents($event, $bfEvent->subevent);
				}
				$this->log('Parse complete.');
			}

			$this->log('Bulk updating events...');
			\bets\Event::bulkUpdate($this->eventsById);
			$this->log('Bulk update complete.');

			$this->log('Committing..');
			\bets\bets::sql()->commit();
			$this->log('Commit complete.');

			\bets\Event::clearCache();
			$this->eventsById = null;
			$this->eventsByParentAndName = null;
			$this->eventsByBetfairMarketId = null;

			gc_collect_cycles();
		}
	}

	private function updateParentEventsDate($event, $eventDate)
	{
		$evt = $event;
		while ($evt->idparent) {
			//$evt = \bets\Event::get($evt->idparent);
			$evt = $this->eventsById[$evt->idparent];
			if (!$evt->ts || $evt->ts < $eventDate) {
				$evt->ts = $eventDate;
				//$evt->update();
				$evt->setDirty(true);
			}
		}
	}

	private function parseSubEvents($event, $bfSubEvents)
	{
		foreach ($bfSubEvents as $bfSubEvent) {
			$attributes = $bfSubEvent->attributes();
			if (!$attributes['id'])
				continue;

			$subEventName = trim($attributes['title'] . '');
			$subEventDate = date('Y-m-d H:i:00', \DateTime::createFromFormat('d/m/Y H:i', "{$attributes['date']} {$attributes['time']}")->getTimestamp());
			$subEventBetfairMarketId = intval($attributes['id']);
			$subEventTotalAmountMatched = floatval($attributes['TotalAmountMatched']);

			if (in_array(strtoupper($subEventName), $this->excludedSubEvents)) {
				continue;
			}

			$nowDate = date('Y-m-d H:i:s');
			if ($subEventDate < $nowDate || $subEventDate > $this->competition->ts_end)
				continue;

			//$subEvent = \bets\Event::getWhere(array('betfairMarketId=' => $subEventBetfairMarketId));
			$subEvent = $this->getEventByBetfairMarketId($subEventBetfairMarketId);
			if (!$subEvent) {
				$subEvent = new \bets\Event(null, array('name' => $subEventName, 'ts' => $subEventDate, 'betfairMarketId' => $subEventBetfairMarketId, 'betfairAmountMatched' => $subEventTotalAmountMatched));
				$subEvent->idsport = $event->idsport;
				$subEvent->idparent = $event->id;
				$subEvent->idcomp = $event->idcomp;
				$subEvent->insert();
				$this->addEvent($subEvent);

				$isValidSubEvent = $this->parseSelections($subEvent, $bfSubEvent->selection);
				if (!$isValidSubEvent) {
					$subEvent->ts = null;
					//$subEvent->update();
					$subEvent->setDirty(true);
				} else {
					$this->updateParentEventsDate($subEvent, $subEventDate);
				}
			} else {
				$subEvent->name = $subEventName;
				$subEvent->betfairAmountMatched = $subEventTotalAmountMatched;

				$isValidSubEvent = $this->parseSelections($subEvent, $bfSubEvent->selection);
				if ($subEvent->ts || $isValidSubEvent) {
					if (!$subEvent->ts || $subEvent->ts != $subEventDate) {
						$subEvent->ts = $subEventDate;
						$this->updateParentEventsDate($subEvent, $subEventDate);
					}
				}
				//$subEvent->update();
				$subEvent->setDirty(true);
			}
		}
	}

	private function parseSelections($subEvent, $bfSelections)
	{
		$this->selectionsById = array();
		$this->selectionsByEventAndName = array();

		$selections = \bets\Selection::findWhere(array('idevent=' => $subEvent->id));
		foreach ($selections as $selection) {
			$this->addSelection($selection);
		}

		$bookmakerPercentage = 0;
		$count = 0;
		foreach ($bfSelections as $bfSelection) {
			$count++;

			$attributes = $bfSelection->attributes();
			$selectionName = trim($attributes['name'] . '');
			if ($subEvent->getSport()->name == 'Horse Racing' && $subEvent->topEvent()->name == 'USA') {
				$selectionName = trim(preg_replace('/^(\d)+. /', '', $selectionName));
			}
			if ($subEvent->getSport()->name == 'Greyhound Racing') {
				$selectionName = trim(preg_replace('/^(\d)+. /', '', $selectionName));
			}
			$selectionOdds = floatval($attributes['backp1']);
			$betfairSelectionId = intval($attributes['id']);

			$selectionPercentage = $selectionOdds > 0 ? intval(100 / floatval($selectionOdds)) : 100;
			$bookmakerPercentage += $selectionPercentage;

			//$selection = \bets\Selection::getWhere(array('idevent=' => $subEvent->id, 'name=' => $selectionName, 'betfairSelectionId=' => $betfairSelectionId));
			$selection = $this->getSelectionByEventAndName($subEvent->id, $selectionName, $betfairSelectionId);
			if (!$selection) {
				$selection = new \bets\Selection(null, array('name' => $selectionName, 'odds' => $selectionOdds, 'betfairSelectionId' => $betfairSelectionId, 'betfairOrder' => $count));
				$selection->idevent = $subEvent->id;
				$this->addSelection($selection);
			} else {
				$selection->odds = $selectionOdds;
				$selection->betfairOrder = $count;
				//$selection->update();
				$selection->setDirty(true);
			}
		}

		$isValidMarket = false;
		if (
			//($count <= 2 && $bookmakerPercentage < 120) ||
			//($count <= 5 && $bookmakerPercentage < 130) ||
			//($count <= 8 && $bookmakerPercentage < 150) ||
			//($count > 8 && $bookmakerPercentage < 250)
			($count <= 2 && $bookmakerPercentage < 130) ||
			($count <= 5 && $bookmakerPercentage < 150) ||
			($count <= 8 && $bookmakerPercentage < 200) ||
			($count > 8 && $bookmakerPercentage < 500)
		) {
			$isValidMarket = true;
			\bets\Selection::bulkInsert($this->selectionsById);
		}
		\bets\Selection::bulkUpdate($this->selectionsById);

		\bets\Selection::clearCache();
		$this->selectionsById = null;
		$this->selectionsByEventAndName = null;

		return $isValidMarket;
	}

	private function loadUrlContent($url)
	{
		$content = file_get_contents(htmlspecialchars_decode($url));
		if (!$content) {
			var_dump(debug_backtrace());
			var_dump($url);
			die("ERROR!");
		}

		return preg_replace('/[^(\x20-\x7F)]*/', '', $content);
	}
}

if (!BetfairImportManager::getRunning()) {
	BetfairImportManager::setRunning(true);
	$bfImportManager = new BetfairImportManager();
	$bfImportManager->importSportsAndEvents();
	$bfImportManager->importEventsAndSelections("http://www.betfair.com/partner/marketdata_xml3.asp");
	BetfairImportManager::setRunning(false);
}
