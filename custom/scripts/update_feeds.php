<?php

namespace bets;

require_once(dirname(__FILE__) . '/../config.php');
require_once(PATH_LIB . 'object.php');
require_once(PATH_DOMAIN . 'sport.php');
require_once(PATH_DOMAIN . 'event.php');
require_once(PATH_DOMAIN . 'selection.php');

class FeedManager extends Object
{
	public $startUrl;
	public $feeds = array();

	public function initialize($startUrl)
	{
		$this->startUrl = $startUrl;
		call_user_func_array('parent::initialize', func_get_args());
	}

	public function run()
	{
		// caching
//		Sport::findAll();
//		Event::findAll();
//		Selection::findAll();
		// run
		preg_match_all('#href="([^"]*SportName[^"]*)"#', $this->fetch($this->startUrl), $matches);
		$count = count($matches[1]);
		foreach ($matches[1] as $nr => $match) {
			if ($nr % 4 != 0) continue;

			echo "# Fetching {$nr}/{$count}: {$match}\n";
			$this->parseFeed($this->fetch($match));
		}
//		foreach (Sport::findAll() as $sport) {
//			$sport->visible = ($sport->computeVisibility() ? 'y' : 'n');
//			$sport->update();
//		}
//		foreach (Event::findAll() as $event) {
//			$event->visible = ($event->computeVisibility() ? 'y' : 'n');
//			$event->update();
//		}
	}

	public function fetch($url)
	{
		$result = file_get_contents($url);
		if ($result === false) {
			throw new \Exception("Could not fetch '{$url}' (file_get_contents() returned false)");
		}
		return $this->fix_utf8($result);
	}

	public function parseFeed($xml)
	{
		$reader = new \XMLReader();
		$reader->xml($xml);

		$xmlTree = $this->xml2tree($reader);
		$this->parseSports($xmlTree);
	}

	private function xml2tree($xmlReader)
	{
		$tree = array();
		while ($xmlReader->read()) {
			switch ($xmlReader->nodeType) {

				case \XMLReader::END_ELEMENT:
					return $tree;

				case \XMLReader::ELEMENT:
					$node = array('tag' => $xmlReader->name, 'children' => $xmlReader->isEmptyElement ? '' : $this->xml2tree($xmlReader));
					if ($xmlReader->hasAttributes) {
						while ($xmlReader->moveToNextAttribute()) {
							$node['attributes'][$xmlReader->name] = $xmlReader->value;
						}
					}
					$tree[] = $node;
					break;

				case \XMLReader::TEXT:

				case \XMLReader::CDATA:
					$tree .= $xmlReader->value;
			}
		}
		return $tree;
	}

	private function parseSports($xmlTree)
	{
		foreach ($xmlTree as $sportNode) {
			if ($sportNode['tag'] != 'betfair') continue;

			$sportName = $sportNode['attributes']['sport'];
			$sport = Sport::getWhere(array('name=' => $sportName));
			if (!$sport) {
				$sport = new Sport(null, array('name' => $sportName));
				$sport->insert();
			}
			if (!empty($sportNode['children'])) {
				$this->parseSportEvents($sport, $sportNode['children']);
			}
		}
	}

	private function parseSportEvents($sport, $xmlTree)
	{
		foreach ($xmlTree as $eventNode) {
			if ($eventNode['tag'] != 'event') continue;

			$eventName = $eventNode['attributes']['name'];
			$eventDate = date('Y-m-d 00:00:00', \DateTime::createFromFormat('d/m/Y', $eventNode['attributes']['date'])->getTimestamp());

			$eventsList = explode('/', $eventName);
			$idParent = 0;
			for ($i = 0; $i < count($eventsList); $i++) {
				$eventName = $eventsList[$i];
				$event = Event::getWhere(array('name=' => $eventName, 'idparent=' => $idParent));
				if (!$event) {
					$event = new Event(null, array('name' => $eventName, 'ts' => $eventDate, 'idparent' => $idParent));
					$event->setSport($sport);
					$idParent = $event->insert();
				} else {
					$idParent = $event->id;
				}
			}

			if (!empty($eventNode['children'])) {
				$this->parseSubEvents($event, $eventNode['children']);
			}
		}
	}

	private function parseSubEvents($event, $xmlTree)
	{
		foreach ($xmlTree as $subEventNode) {
			if ($subEventNode['tag'] != 'subevent') continue;

			$subEventName = $subEventNode['attributes']['title'];
			$subEventDate = date('Y-m-d H:i:00', \DateTime::createFromFormat('d/m/Y H:i', "{$subEventNode['attributes']['date']} {$subEventNode['attributes']['time']}")->getTimestamp());
			$subEventBetfairMarketId = $subEventNode['attributes']['id'];

			$subEvent = Event::getWhere(array('betfairMarketId=' => $subEventBetfairMarketId));
			if (!$subEvent) {
				$subEvent = new Event(null, array('name' => $subEventName, 'ts' => $subEventDate, 'betfairMarketId' => $subEventBetfairMarketId));
				$subEvent->setSport($event->getSport());
				$subEvent->setParent($event);
				$subEvent->insert();
			}
			if (!empty($subEventNode['children'])) {
				$this->parseSelections($subEvent, $subEventNode['children']);
			}
		}
	}

	private function parseSelections($subEvent, $xmlTree)
	{
		foreach ($xmlTree as $selectionNode) {
			if ($selectionNode['tag'] != 'selection') continue;

			$selectionName = $selectionNode['attributes']['name'];
			$selectionOdds = $selectionNode['attributes']['backp1'];
			$betfairSelectionId = $selectionNode['attributes']['id'];

			$selection = Selection::getWhere(array('idevent=' => $subEvent->id, 'name=' => $selectionName, 'betfairSelectionId=' => $betfairSelectionId));
			if (!$selection) {
				$selection = new Selection(null, array('name' => $selectionName, 'odds' => $selectionOdds, 'betfairSelectionId' => $betfairSelectionId));
				$selection->setEvent($subEvent);
				$selection->insert();
			} else {
				$selection->odds = $selectionOdds;
				$selection->update();
			}
		}
	}

	private function fix_utf8($string)
	{
		return preg_replace('/[^(\x20-\x7F)]*/', '', $string);
	}
}

try {
	$fm = new FeedManager('http://www.betfair.com/partner/marketdata_xml3.asp');
	$fm->run();
} catch (Exception $e) {
	die('error');
}