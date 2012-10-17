<?php

namespace betfair;

require_once(dirname(__FILE__) . '/../config.php');
require_once(PATH_DOMAIN . 'betfair_result.php');
require_once(PATH_DOMAIN . 'event.php');
require_once(PATH_DOMAIN . 'selection.php');
require_once(PATH_DOMAIN . 'bet_selection.php');

class BetfairResultsManager
{
	public function run($url)
	{
		preg_match_all('#href="([^"]*sportID[^"]*)"#', $this->loadUrlContent($url), $matches);
		foreach ($matches[1] as $match) {
			$urlQuery = parse_url($match, PHP_URL_QUERY);
			parse_str(html_entity_decode($urlQuery), $queryString);

			if (!array_key_exists('countryID', $queryString) || $queryString['countryID'] == 1) {
				$sportResultsUrl = 'http://rss.betfair.com/RSS.aspx?format=rss&sportID=' . $queryString['sportID'];
				$this->getSportResults($sportResultsUrl);
			}
		}

		$this->updateSelectionStatus();
		$this->updateBetSelectionStatus();
	}

	private function updateSelectionStatus()
	{
		$unprocessedBetfairResults = \bets\BetfairResult::findWhere(array('processed=' => 'n'));
		foreach ($unprocessedBetfairResults as $betfairResult) {
			$winners = explode(",", $betfairResult->winner);
			array_walk($winners, create_function('&$val', '$val = trim($val);'));

			$event = \bets\Event::getWhere(array('betfairMarketId=' => $betfairResult->betfairMarketId));
			$eventSelections = \bets\Selection::findWhere(array('idevent=' => $event->id));
			foreach ($eventSelections as $selection) {
				$selection->status = in_array($selection->name, $winners)
					? 'won'
					: 'lost';
				$selection->update();
			}

			$betfairResult->processed = 'y';
			$betfairResult->update();
		}
	}

	private function updateBetSelectionStatus()
	{
		$pendingBetSelections = \bets\BetSelection::findWhere(array('status=' => 'pending'));
		foreach ($pendingBetSelections as $betSelection) {
			$selection = \bets\Selection::get($betSelection->idselection);
			if ($selection->status != 'pending') {
				$betSelection->status = $selection->status;
				$betSelection->update();
			}
		}
	}

	private function getSportResults($url)
	{
		$xmlContent = $this->loadUrlContent($url);
		$xmlObject = simplexml_load_string($xmlContent);

		$results = $xmlObject->channel->item;
		foreach ($results as $resultItem) {
			parse_str(html_entity_decode(parse_url($resultItem->link, PHP_URL_QUERY)), $queryString);
			$betfairMarketId = $queryString['marketID'];
			$winner = str_replace('Winner(s): ', '', trim($resultItem->description));

			$betfairResult = \bets\BetfairResult::getWhere(array('betfairMarketId=' => $betfairMarketId, 'winner=' => $winner));
			if (!$betfairResult) {
				$betfairResult = new \bets\BetfairResult();
				$betfairResult->betfairMarketId = $betfairMarketId;
				$betfairResult->winner = $winner;
				$betfairResult->insert();
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

$bfResultsManager = new BetfairResultsManager();
$bfResultsManager->run("http://rss.betfair.com/Navigation.aspx");