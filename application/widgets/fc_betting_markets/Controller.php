<?php

require_once('custom/config.php');
require_once(PATH_DOMAIN . 'event.php');
require_once(PATH_DOMAIN . 'competition.php');
require_once(PATH_DOMAIN . 'user_selection.php');
require_once(PATH_DOMAIN . 'user.php');
require_once(PATH_LIB . 'fc.php');

class Widget_FC_Betting_MarketsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		if (isset($_REQUEST['action'])) {
			$this->submitSelection((int)$_REQUEST['idselection']);
			$this->setNoRender(true);
			return;
		}

		if (isset($_REQUEST['idevent']) || isset($_REQUEST['event'])) {
			$idEvent = -1;
			if (isset($_REQUEST['event']) || !empty($_REQUEST['event'])) {
				$idEvent = intval($_REQUEST['event']);
			}
			if (isset($_REQUEST['idevent']) && !empty($_REQUEST['idevent'])) {
				$idEvent = intval($_REQUEST['idevent']);
			}

			$event = \bets\Event::get($idEvent);
			if (!$event || !$event->betfairMarketId) {
				$this->showDefaultMarkets();
				return;
			}

			$this->view->user = bets\User::getCurrentUser();
			$this->view->event = $event;
			$this->view->parentEvent = \bets\Event::get($event->idparent);
			$this->view->selections = bets\Selection::findWhere(array('idevent=' => $idEvent), 'ORDER BY betfairOrder, id');
		} else {
			$this->showDefaultMarkets();
		}
	}

	private function showDefaultMarkets()
	{
		$this->_action = 'default';

		$now = date('Y-m-d H:i:s', mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
		$then = date('Y-m-d H:i:s', mktime(date("H") + 8, date("i"), date("s"), date("m"), date("d"), date("Y")));

		$topSports = array('Soccer', 'Golf', 'Horse Racing', 'Rugby Union', 'Tennis', 'Cricket');
		$upcomingEvents = array();
		foreach ($topSports as $topSport) {
			$sport = \bets\Sport::getWhere(array('name=' => $topSport));
			if (!$sport) continue;

			$upcomingEvent = \bets\Event::getWhere(array(
				'idsport = ' => $sport->id,
				'betfairMarketId IS NOT ' => null,
				//'betfairAmountMatched > ' => 0,
				'ts > ' => $now,
				'ts < ' => $then),
				'ORDER BY betfairAmountMatched DESC, ts ASC');
			if ($upcomingEvent) {
				$upcomingEvents[] = $upcomingEvent;
			}
		}

		$this->view->upcomingEvents = $upcomingEvents;
		$this->view->user = bets\User::getCurrentUser();
	}

	public function submitSelection($idSelection)
	{
		$competition = bets\Competition::getCurrent();
		$user = bets\User::getCurrentUser();
		$selection = bets\Selection::get($idSelection);

		$userSelection = bets\UserSelection::getWhere(array('idcompetition=' => $competition->id, 'iduser=' => $user->id, 'idselection=' => $idSelection));
		if (!$userSelection) {
			$userSelection = new \bets\UserSelection();
			$userSelection->idcompetition = $competition->id;
			$userSelection->iduser = $user->id;
			$userSelection->idselection = $selection->id;
			$userSelection->insert();
		}
	}
}
