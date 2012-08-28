<?php

require_once('custom/config.php');
require_once(PATH_DOMAIN . 'competition.php');
require_once(PATH_DOMAIN . 'event.php');

class Widget_FC_Betting_CategoriesController extends \Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$competition = bets\Competition::getCurrent();
		$this->view->categories = $this->getCategories($competition->ts_start, $competition->ts_end, $_REQUEST['idsport'], $_REQUEST['idevent']);
	}

	public function getCategories($tsStart, $tsStop, $idSport, $idEvent)
	{
		$now = date('Y-m-d H:i:s', mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));

		$result = array(array('idsport' => '', 'idevent' => '', 'name' => 'All'));
		if (empty($idSport)) {
//			$sportRows = \bets\Sport::findWhere(array('enabled=' => 'y'), 'ORDER BY name ASC');
//			foreach ($sportRows as $sport) {
//				$result[] = array(
//					'idsport' => $sport->id,
//					'idevent' => '',
//					'name' => $sport->name);
//			}
			$sportRows = \bets\bets::sql()->run("SELECT DISTINCT S.* FROM fc_event E JOIN fc_sport S ON E.idsport = S.id WHERE S.enabled = 'y' AND E.deleted = 'n' AND E.ts IS NOT NULL ORDER BY name ASC");
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
				'name' => $event->name);

			$result[] = $category;
		}

		return $result;
	}
}
