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
		} else {
			$idEvent = (int)$_REQUEST['idevent'];
			$event = \bets\Event::get($idEvent);
			$parentEvent = \bets\Event::get($event->idparent);

			$this->view->event = $event;
			$this->view->parentEvent = $parentEvent;
			$this->view->selections = bets\Selection::findWhere(array('idevent=' => $idEvent), 'ORDER BY id ASC');
			$this->view->user = bets\User::getCurrentUser();
		}
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
