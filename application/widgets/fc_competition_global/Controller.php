<?php

require_once('custom/config.php');

require_once(PATH_DOMAIN . 'event.php');
require_once(PATH_DOMAIN . 'competition.php');
require_once(PATH_DOMAIN . 'user_selection.php');

class Widget_Fc_Competition_GlobalController extends Engine_Content_Widget_Abstract {
	
	private static $bet_Status = 'settled';
	
	public function indexAction() {
		$this->view->winners = $this->getWinnerPoints(1);
	}
	
	public function getWinnerUsers(){
		$competition = bets\Competition::getCurrent();
		return $uId = $sportRows = bets\bets::sql()->query("SELECT DISTINCT iduser FROM fc_user_selection WHERE status = '$bet_Status' AND idcompetition = '$competition->id'");
	}
	
	public function getWinnerBets(){
		$c = bets\Competition::getCurrent();
		return bets\UserSelection::findWhere(
			array(
				'idcompetition=' => $c->id,
				'status=' => $bet_Status // here must be 'win';
			)
		);
	}
	
	public function getLossBets(){
		$c = bets\Competition::getCurrent();
		return bets\UserSelection::findWhere(
			array(
				'idcompetition=' => $c->id,
				'status=' => 'loss'
			)
		);
	}
	
	public function getWinnerPoints($user){
		$array = $this->getWinnerBets();
		foreach($array as $value){
			if ($value->iduser == $user)
				$Total += $value->bet_amount;
		}
		
		return $Total;
	}
	
	
	public function getWinnersBetsByUser(){
		$users = $this->getWinnersUsersId();
		foreach ($users as $value)
			$userIds[] = $value['iduser'];
		
		$users = implode(',',$userIds);
		return $BetsId = bets\bets::sql()->query("SELECT fc_user_selection.*, fc_selection.* FROM fc_user_selection INNER JOIN fc_selection ON fc_selection.id = fc_user_selection.idselection WHERE fc_user_selection.status = 'settled' AND fc_user_selection.iduser in ($users)");
	}
	
	
	public function getTotalPoints(){
		$user = \bets\User::getCurrentUser();
		
		
	}
	
	
	
	
	public function getSportIds($tsStart, $tsStop, $appendIds = array()) {
		$sportRows = bets\bets::sql()->query("SELECT DISTINCT fc_event.idsport FROM fc_event INNER JOIN sport_to_selection ON sport_to_selection.idevent = fc_event.id WHERE (fc_event.ts BETWEEN '{$tsStart}' AND '$tsStop') AND fc_event.visible = 'y'");
		$sportIds = array();
		foreach ($sportRows as $row) {
			$sportIds[] = $row['idsport'];
		}
		$sportIds = array_merge($appendIds, $sportIds);
		return (count($sportIds) ? implode(',', $sportIds) : array());
	}

	public function getEventIds($tsStart, $tsStop, $idsport, $idparent, $appendIds = array()) {
		$eventRows = bets\bets::sql()->query("SELECT DISTINCT fc_event.id FROM fc_event INNER JOIN sport_to_selection ON sport_to_selection.idevent = fc_event.id OR sport_to_selection.idsubevent = fc_event.id WHERE (fc_event.ts BETWEEN '{$tsStart}' AND '$tsStop') AND fc_event.visible = 'y' AND fc_event.idsport = {$idsport} AND fc_event.idparent = {$idparent}");
		$eventIds = array();
		foreach ($eventRows as $row) {
			$eventIds[] = $row['id'];
		}
		$eventIds = array_merge($appendIds, $eventIds);
		return (count($eventIds) ? implode(',', $eventIds) : array());
	}

	public function getAll($tsStart, $tsStop, $idsport, $idevent) {
		$result = array(array('idsport' => '', 'idevent' => '', 'name' => 'All'));
		$sportIds = $this->getSportIds($tsStart, $tsStop, array($idsport));
		foreach (bets\Sport::findWhere(array('enabled=' => 'y'), "AND id IN ({$sportIds})") as $sport) {
			$categ = array('idsport' => $sport->id, 'idevent' => '', 'name' => $sport->name);
			if ($sport->id == $idsport) {
				$categ['children'] = $this->getForSport($tsStart, $tsStop, $idsport, $idevent);
				$result[] = $categ;
			} elseif (empty($idsport)) {
				$result[] = $categ;
			}
		}
		return $result;
	}

	public function getForSport($tsStart, $tsStop, $idsport, $idevent) {
		$result = array();
		$idparent = bets\Event::get($idevent)->idparent;
		if (!$idparent) {
			$idparent = $idevent;
		}
		$eventIds = $this->getEventIds($tsStart, $tsStop, $idsport, $idparent, array($idparent));
		foreach (bets\Event::findWhere(array(), " AND (id IN ({$eventIds})) ") as $event) {
			$categ = array('idsport' => $event->idsport, 'idevent' => $event->id, 'name' => $event->name);
			if ($event->id == $idparent) {
				$categ['children'] = $this->getForEvent($tsStart, $tsStop, $event->id);
				$result[] = $categ;
			} elseif (empty($idparent)) {
				$result[] = $categ;
			}
		}
		return $result;
	}

	public function getForEvent($tsStart, $tsStop, $idevent) {
		$result = array();
		$eventIds = $this->getEventIds($tsStart, $tsStop, bets\Event::get($idevent)->idsport, $idevent, array());
		foreach (bets\Event::findWhere(array(), " AND (id IN ({$eventIds})) ") as $event) {
			$categ = array('idsport' => $event->idsport, 'idevent' => $event->id, 'name' => $event->name);
			if ($event->id == $idevent) {
				$categ['children'] = $this->getForEvent($tsStart, $tsStop, $event->id);
			}
			$result[] = $categ;
		}
		return $result;
	}

}

