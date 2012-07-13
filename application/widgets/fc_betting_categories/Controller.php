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
		$result = array(array('idsport' => '', 'idevent' => '', 'name' => 'All'));
		if (empty($idSport)) {
			$sportRows = bets\bets::sql()->query("SELECT * FROM fc_sport");
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
				$category['children'] = $this->getSubcategories($tsStart, $tsStop, $idSport, 0);
			} else {
				$event = \bets\Event::get($idEvent);
				$categoryHelper = array(
					'idsport' => $sport->id,
					'idevent' => $event->id,
					'name' => $event->name,
					'children' => $this->getSubcategories($tsStart, $tsStop, $idSport, $event->id));
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

	public function getSubcategories($tsStart, $tsStop, $idSport, $idParent)
	{
		// TODO: make use of $tsStart & $tsStop
		$result = array();
		foreach (bets\Event::findWhere(array('idsport=' => $idSport, 'idparent=' => $idParent)) as $event) {
			$category = array(
				'idsport' => $event->idsport,
				'idevent' => $event->id,
				'name' => $event->name);
			$result[] = $category;
		}
		return $result;
	}
}

