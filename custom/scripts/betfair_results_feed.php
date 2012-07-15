<?php

namespace bets;

require_once(dirname(__FILE__) . '/../config.php');
require_once(PATH_LIB . 'object.php');
require_once(PATH_DOMAIN . 'betfair_result.php');
require_once(PATH_DOMAIN . 'selection.php');

class BetfairResultsFeedManager extends Object
{
	public $startUrl;

	public function run()
	{
		preg_match_all('#href="([^"]*sportID[^"]*)"#', $this->fetch($this->startUrl), $matches);
		foreach ($matches[1] as $match) {
			$urlQuery = parse_url($match, PHP_URL_QUERY);
			parse_str(html_entity_decode($urlQuery), $queryString);
			if (!array_key_exists('countryID', $queryString) || $queryString['countryID'] == 1) {
				$sportResultsUrl = 'http://rss.betfair.com/RSS.aspx?format=rss&sportID=' . $queryString['sportID'];
				echo "Processing " . $sportResultsUrl . "<br/>";
				$this->getSportResults($sportResultsUrl);
			}
		}
		echo "Done!";
	}

	public function fetch($url)
	{
		$result = file_get_contents($url);
		if ($result === false) {
			throw new \Exception("Could not fetch '{$url}' (file_get_contents() returned false)");
		}
		return $this->fix_utf8($result);
	}

	public function getSportResults($url)
	{
		$xmlContent = $this->fetch($url);
		$xmlObject = simplexml_load_string($xmlContent);

		$results = $xmlObject->channel->item;
		foreach ($results as $resultItem) {
			parse_str(html_entity_decode(parse_url($resultItem->link, PHP_URL_QUERY)), $queryString);
			$betfairMarketId = $queryString['marketID'];
			$winner = str_replace('Winner(s): ', '', $resultItem->description);

			$betfairResult = \bets\BetfairResult::getWhere(array('betfairMarketId=' => $betfairMarketId, 'winner=' => $winner));
			if (!$betfairResult) {
				$betfairResult = new BetfairResult();
				$betfairResult->betfairMarketId = $betfairMarketId;
				$betfairResult->winner = $winner;
				$betfairResult->insert();
			}
		}
	}

	private function fix_utf8($string)
	{
		return preg_replace('/[^(\x20-\x7F)]*/', '', $string);
	}
}

try {
	$rfm = new BetfairResultsFeedManager();
	$rfm->startUrl = 'http://rss.betfair.com/Navigation.aspx';
	$rfm->run();
} catch (Exception $e) {
	die('error');
}