<?php

namespace betfair;

require_once(dirname(__FILE__) . '/../config.php');
require_once(PATH_LIB . 'object.php');
require_once(PATH_DOMAIN . 'sport.php');
require_once(PATH_DOMAIN . 'event.php');
require_once(PATH_DOMAIN . 'selection.php');

class BetfairImportManager
{
	public $soapClient;
	public $sessionToken;

	public function __construct()
	{
		$this->soapClient = new \SoapClient("https://api.betfair.com/global/v3/BFGlobalService.wsdl");
		$this->login();
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
		preg_match_all('#href="([^"]*SportName[^"]*)"#', $this->loadUrlContent($url), $matches);
		$count = count($matches[1]);
		foreach ($matches[1] as $nr => $match) {
			if ($nr % 4 != 0) continue;

			echo "# Fetching {$nr}/{$count}: {$match}" . "\n";
			$xmlContent = $this->loadUrlContent($match);
			$xmlObject = simplexml_load_string($xmlContent);

			$sportName = $xmlObject->attributes()['sport'];
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
	}

	private function getActiveEventTypes()
	{
		// getAllEventTypes
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
			$sport = \bets\Sport::getWhere(array('name=' => $eventType->name));
			if (!$sport) {
				$sport = new \bets\Sport(null, array('name' => $eventType->name, 'betfairSportId' => $eventType->id));
				$sport->insert();
			}
			$this->getEvents($sport->betfairSportId, $sport->id, 0);
		}
	}

	private function getEvents($betfairParentId, $sportId, $fcParentId)
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
				$event = \bets\Event::getWhere(array('name=' => $bfEvent->eventName, 'idparent=' => $fcParentId));
				if (!$event) {
					$event = new \bets\Event(null, array('name' => $bfEvent->eventName, 'ts' => $bfEvent->startTime, 'idparent' => $fcParentId));
					$event->idsport = $sportId;
					$parentId = $event->insert();
				} else {
					$parentId = $event->id;
				}
				$this->getEvents($bfEvent->eventId, $sportId, $parentId);
			}
		}
	}

	private function parseEvents($sport, $bfEvents)
	{
		foreach ($bfEvents as $bfEvent) {
			$eventName = $bfEvent->attributes()['name'];
			$eventDate = date('Y-m-d 00:00:00', \DateTime::createFromFormat('d/m/Y', $bfEvent->attributes()['date'])->getTimestamp());

			$eventsList = explode('/', $eventName);

			$idParent = 0;
			$eventName = '';
			for ($i = 0; $i < count($eventsList); $i++) {
				$eventName .= $eventsList[$i];
				$event = \bets\Event::getWhere(array('idsport=' => $sport->id, 'idparent=' => $idParent, 'name=' => $eventName));
				if (!$event) {
					$eventName .= '/';
					$eventFound = false;
				} else {
					$idParent = $event->id;
					$eventName = '';
					$eventFound = true;
				}
			}

			if (!$eventFound) {
				echo $bfEvent->attributes()['name'] . "\n";
				continue;
			}

			if (count($bfEvent->subevent) > 0) {
				$this->parseSubEvents($event, $bfEvent->subevent);
			}
		}
	}

	private function parseSubEvents($event, $bfSubEvents)
	{
		foreach ($bfSubEvents as $bfSubEvent) {
			$subEventName = $bfSubEvent->attributes()['title'];
			$subEventDate = date('Y-m-d H:i:00', \DateTime::createFromFormat('d/m/Y H:i', "{$bfSubEvent->attributes()['date']} {$bfSubEvent->attributes()['time']}")->getTimestamp());
			$subEventBetfairMarketId = $bfSubEvent->attributes()['id'];

			$subEvent = \bets\Event::getWhere(array('betfairMarketId=' => $subEventBetfairMarketId));
			if (!$subEvent) {
				$subEvent = new \bets\Event(null, array('name' => $subEventName, 'ts' => $subEventDate, 'betfairMarketId' => $subEventBetfairMarketId));
				$subEvent->idsport = $event->idsport;
				$subEvent->idparent = $event->id;
				$subEvent->insert();
			}

			if (count($bfSubEvent->selection) > 0) {
				$this->parseSelections($subEvent, $bfSubEvent->selection);
			}
		}
	}

	private function parseSelections($subEvent, $bfSelections)
	{
		foreach ($bfSelections as $bfSelection) {
			$selectionName = $bfSelection->attributes()['name'];
			$selectionOdds = $bfSelection->attributes()['backp1'];
			$betfairSelectionId = $bfSelection->attributes()['id'];

			$selection = \bets\Selection::getWhere(array('idevent=' => $subEvent->id, 'name=' => $selectionName, 'betfairSelectionId=' => $betfairSelectionId));
			if (!$selection) {
				$selection = new \bets\Selection(null, array('name' => $selectionName, 'odds' => $selectionOdds, 'betfairSelectionId' => $betfairSelectionId));
				$selection->idevent = $subEvent->id;
				$selection->insert();
			} else {
				$selection->odds = $selectionOdds;
				$selection->update();
			}
		}
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

$bfImportManager = new BetfairImportManager();
//$bfImportManager->importSportsAndEvents();
$bfImportManager->importEventsAndSelections('http://www.betfair.com/partner/marketdata_xml3.asp');