<?php

require_once('custom/config.php');
require_once(PATH_DOMAIN . 'competition.php');
require_once(PATH_DOMAIN . 'event.php');

class Widget_FC_Betting_CategoriesController extends \Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$competition = bets\Competition::getCurrent();
		$idSport = null;
		$idEvent = null;
		if (isset($_REQUEST['idsport']) || isset($_REQUEST['idevent'])) {
			$idSport = $_REQUEST['idsport'];
			$idEvent = $_REQUEST['idevent'];
		} else if (isset($_REQUEST['event'])) {
			$event = \bets\Event::get(intval($_REQUEST['event']));
			if ($event) {
				$idSport = $event->getSport()->id;
				$idEvent = $event->id;
			}
		}

		$this->view->categories = $this->getCategories($competition->ts_start, $competition->ts_end, $idSport, $idEvent);
	}

	public function getCategories($tsStart, $tsStop, $idSport, $idEvent)
	{
		$nowDatetime = new \DateTime();
		$now = $nowDatetime->format('Y-m-d H:i:s');

		$result = array(array('idsport' => '', 'idevent' => '', 'name' => 'All'));
		if (empty($idSport)) {
			$sportRows = \bets\bets::sql()->query("SELECT DISTINCT S.* FROM fc_event E JOIN fc_sport S ON E.idsport = S.id WHERE S.enabled = 'y' AND E.ts IS NOT NULL AND E.ts > '$now' ORDER BY name ASC");
			foreach ($sportRows as $sport) {
				$result[] = array(
					'idsport' => $sport['id'],
					'idevent' => '',
					'name' => $sport['name']);
			}
		} else {
			$sport = \bets\Sport::get($idSport);

			$category = array(
				'idsport' => $sport->id,
				'idevent' => '',
				'name' => $sport->name);
			if (empty($idEvent)) {
				$category['children'] = $this->getSubcategories($now, $tsStop, $idSport, 0);
			} else {
				$event = \bets\Event::get($idEvent);
				$categoryHelper = array(
					'idsport' => $sport->id,
					'idevent' => $event->id,
					'name' => $event->name,
					'children' => $this->getSubcategories($now, $tsStop, $idSport, $idEvent));

				while ($event->idparent != 0) {
					$event = \bets\Event::get($event->idparent);
					$categoryHelper = array(
						'idsport' => $sport->id,
						'idevent' => $event->id,
						'name' => $event->name,
						'children' => array($categoryHelper));
				}
				$category['children'] = array($categoryHelper);
			}
			$result[] = $category;
		}
		return $result;
	}

	public function getSportCategories($idSport, $idParent)
	{
		$result = array();
		foreach (bets\Event::findWhere(array('idsport=' => $idSport, 'idparent=' => $idParent), "ORDER BY name ASC") as $event) {
			$category = array(
				'idsport' => $event->idsport,
				'idevent' => $event->id,
				'name' => $event->name);
			$result[] = $category;
		}
		return $result;
	}

	public function getSubcategories($tsStart, $tsStop, $idSport, $idParent)
	{
		$result = array();

		$activeEvents = bets\Event::findWhere(
			array('idsport=' => $idSport, 'idparent=' => $idParent, 'ts>' => $tsStart, 'ts<' => $tsStop),
			"ORDER BY betfairAmountMatched DESC, name ASC, id ASC");
		foreach ($activeEvents as $event) {
			$category = array(
				'idsport' => $event->idsport,
				'idevent' => $event->id,
				'name' => $event->name,
				'isLeaf' => $event->betfairMarketId != null);
			$result[] = $category;
		}

		return $result;
	}
}
