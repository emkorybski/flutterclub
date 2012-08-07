<?php

namespace betfair;

require_once(dirname(__FILE__) . '/../config.php');
require_once(PATH_LIB . 'object.php');
require_once(PATH_DOMAIN . 'competition.php');
require_once(PATH_DOMAIN . 'sport.php');
require_once(PATH_DOMAIN . 'event.php');
require_once(PATH_DOMAIN . 'selection.php');

class BetfairImportManager
{
	public $soapClient;
	public $sessionToken;

	public function __construct()
	{
		//file_put_contents('betfair_import_manager.log', "Betfair Import Manager" . "\r\n");
		$this->soapClient = new \SoapClient("https://api.betfair.com/global/v3/BFGlobalService.wsdl");
		$this->login();
	}

	private static function log($message)
	{
		//file_put_contents('betfair_import_manager.log', $message . "\r\n", FILE_APPEND | LOCK_EX);
	}

	public function login()
	{
		// login
		$soapRequest = new \stdClass();
		$soapRequest->username = "thefly01";
		$soapRequest->password = "jf123456";
		$soapRequest->productId = 264;
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

			self::log("# Fetching {$nr}/{$count}: {$match}" . "\r\n");
			$xmlContent = $this->loadUrlContent($match);
			$xmlObject = simplexml_load_string($xmlContent);

			$attributes = $xmlObject->attributes();
			$sportName = $attributes['sport'];
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
		self::log("getActiveEventTypes");
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
			self::log("   " . $eventType->name);
			$sport = \bets\Sport::getWhere(array('name=' => trim($eventType->name)));
			if (!$sport) {
				$sport = new \bets\Sport(null, array('name' => trim($eventType->name), 'betfairSportId' => $eventType->id));
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
				$event = \bets\Event::getWhere(array('name=' => trim($bfEvent->eventName), 'idparent=' => $fcParentId));
				if (!$event) {
					$event = new \bets\Event(null, array('name' => trim($bfEvent->eventName), 'idparent' => $fcParentId));
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
			$attributes = $bfEvent->attributes();
			$eventName = trim($attributes['name']);
			//$eventDate = date('Y-m-d 00:00:00', \DateTime::createFromFormat('d/m/Y', $attributes['date'])->getTimestamp());

			self::log($eventName);
			$eventsList = explode('/', $eventName);

			$idParent = 0;
			$eventName = '';
			for ($i = 0; $i < count($eventsList); $i++) {
				$eventName .= trim($eventsList[$i]);
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
				self::log("EVENT NOT FOUND: " . $attributes['name'] . "\r\n");
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
			$attributes = $bfSubEvent->attributes();
			$subEventName = trim($attributes['title']);
			$subEventDate = date('Y-m-d H:i:00', \DateTime::createFromFormat('d/m/Y H:i', "{$attributes['date']} {$attributes['time']}")->getTimestamp());
			$subEventBetfairMarketId = $attributes['id'];

			self::log("   * " . $subEventName);

			$competition = \bets\Competition::getCurrent();
			$nowDate = date('Y-m-d H:i:s');
			self::log("      " . $nowDate . " < " . $subEventDate . " < " . $competition->ts_end);
			if ($subEventDate < $nowDate || $subEventDate > $competition->ts_end)
				continue;

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
			$attributes = $bfSelection->attributes();
			$selectionName = trim($attributes['name']);
			$selectionOdds = $attributes['backp1'];
			$betfairSelectionId = $attributes['id'];

			self::log("         * " . $selectionName);

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
$bfImportManager->importSportsAndEvents();
$bfImportManager->importEventsAndSelections('http://www.betfair.com/partner/marketdata_xml3.asp');
echo "DONE!";
