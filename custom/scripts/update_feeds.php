#!/usr/bin/php
<?php

namespace bets;

require_once(dirname(__FILE__) . '/../config.php');
require_once(PATH_LIB . 'object.php');
require_once(PATH_DOMAIN . 'sport.php');
require_once(PATH_DOMAIN . 'event.php');
require_once(PATH_DOMAIN . 'selection.php');

class FeedManager extends Object {

	public $startUrl;
	public $feeds = array();

	public function initialize($startUrl) {
		$this->startUrl = $startUrl;
		call_user_func_array('parent::initialize', func_get_args());
	}

	public function run() {
		// caching
		Sport::findAll();
		Event::findAll();
		Selection::findAll();
		// run
		preg_match_all('#href="([^"]*SportName[^"]*)"#', $this->fetch($this->startUrl), $matches);
		$count = count($matches[1]);
		foreach ($matches[1] as $nr => $match) {
			echo "# Fetching {$nr}/{$count}: {$match}\n";
			$this->parseFeed($this->fetch($match));
		}
	}

	public function fetch($url) {
		$result = file_get_contents($url);
		if ($result === false) {
			throw new \Exception("Could not fetch '{$url}' (file_get_contents() returned false)");
		}
		return $this->fix_utf8($result);
	}

	public function parseFeed($xml) {
		$pos = array();
		$reader = new \XMLReader();
		$reader->xml($xml);
		$this->xmlassocParseSports($this->xml2assoc($reader));
	}

	private function xml2assoc($xml) {
		$tree = array();
		while($xml->read()) {
			switch ($xml->nodeType) {
				
				case \XMLReader::END_ELEMENT:
					return $tree;
				
				case \XMLReader::ELEMENT:
					$node = array('tag' => $xml->name, 'value' => $xml->isEmptyElement ? '' : $this->xml2assoc($xml));
						if($xml->hasAttributes) {
						while($xml->moveToNextAttribute()) {
							$node['attributes'][$xml->name] = $xml->value;
						}
					}
					$tree[] = $node;
					break;
				
				case \XMLReader::TEXT:
				
				case \XMLReader::CDATA:
					$tree .= $xml->value;
				
			}
		}
		return $tree;
	}

	private function xmlassocParseSports($xmlassoc) {
		foreach ($xmlassoc as $sportNode) {
			$sportName = $sportNode['attributes']['sport'];
			$sport = Sport::getWhere(array('name=' => $sportName));
			if (!$sport) {
				$sport = new Sport(null, array('name' => $sportName));
				$sport->insert();
			}
			if (!empty($sportNode['value'])) {
				$this->xmlassocParseEvents($sportNode['value'], $sport);
			}
		}
	}

	private function xmlassocParseEvents($xmlassoc, $sport) {
		foreach ($xmlassoc as $eventNode) {
			$eventName = $eventNode['attributes']['name'];
			$eventDate = date('Y-m-d 00:00:00', \DateTime::createFromFormat('d/m/Y', $eventNode['attributes']['date'])->getTimestamp());
			$event = Event::getWhere(array('name=' => $eventName, 'ts=' => $eventDate, 'idparent=' => 0));
			if (!$event) {
				$event = new Event(null, array('idparent' => 0, 'name' => $eventName, 'ts' => $eventDate));
				$event->setSport($sport);
				$event->insert();
			}
			if (!empty($eventNode['value'])) {
				$this->xmlassocParseSubevents($eventNode['value'], $event);
			}
		}
	}

	private function xmlassocParseSubevents($xmlassoc, $event) {
		foreach ($xmlassoc as $subeventNode) {
			$subeventName = $subeventNode['attributes']['title'];
			$subeventDate = date('Y-m-d H:i:00', \DateTime::createFromFormat('d/m/Y H:i', "{$subeventNode['attributes']['date']} {$subeventNode['attributes']['time']}")->getTimestamp());
			$subeventBetfairId = $subeventNode['attributes']['id'];
			$subevent = Event::getWhere(array('betfair_id=' => $subeventBetfairId));
			if (!$subevent) {
				$subevent = new Event(null, array('name' => $subeventName, 'ts' => $subeventDate, 'betfair_id' => $subeventBetfairId));
				$subevent->setSport($event->getSport());
				$subevent->setParent($event);
				$subevent->insert();
			}
			if (!empty($subeventNode['value'])) {
				$this->xmlassocParseSel($subeventNode['value'], $subevent);
			}
		}
	}

	private function xmlassocParseSel($xmlassoc, $subevent) {
		foreach ($xmlassoc as $selNode) {
			$sel = Selection::getWhere(array('betfair_id=' => $selNode['attributes']['id']));
			if (!$sel) {
				$sel = new Selection(null, array('name' => $selNode['attributes']['name'], 'odds' => $selNode['attributes']['backp1'], 'betfair_id' => $selNode['attributes']['id']));
				$sel->setEvent($subevent);
				$sel->insert();
			}
		}
	}

	private function fix_utf8($string) {
		return preg_replace('/[^(\x20-\x7F)]*/','', $string);
	}

}

try {
	$fm = new FeedManager('http://www.betfair.com/partner/marketdata_xml3.asp');
	$fm->run();
}
catch (Exception $e) {
	die('error');
}

