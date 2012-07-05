<?php

require_once('custom/config.php');
require_once(PATH_DOMAIN . 'event.php');
require_once(PATH_DOMAIN . 'competition.php');
require_once(PATH_DOMAIN . 'user_selection.php');
require_once(PATH_DOMAIN . 'user.php');

class Widget_UpcomingController extends Engine_Content_Widget_Abstract {

	public function indexAction() {
		if (isset($_REQUEST['vote_selection_id'])) {
			$this->fc_vote((int) $_REQUEST['vote_selection_id']);
		} else {
			$this->fc_render((int) $_REQUEST['idsport'], (int) $_REQUEST['idevent']);
		}
	}

	public function fc_render($idsport, $idevent) {
		$this->view->comp = bets\Competition::getCurrent();
		$this->view->sel = bets\Selection::findWhere(array('idevent=' => $idevent), 'ORDER BY odds');
		$this->view->idsport = $idsport;
		$this->view->idevent = $idevent;
	}

	public function fc_vote($idSelection) {
		$user = bets\User::getCurrentUser();
		$selection = bets\Selection::get($idSelection);
		$userSel = bets\UserSelection::getWhere(array('iduser=' => $user->id, 'idselection=' => $idSelection));
		if (!$user || !$selection || $userSel) {
			return;
		}
		$userSel = new \bets\UserSelection();
		$userSel->setUser($user);
		$userSel->setSelection($selection);
		$userSel->bet_amount = 0;
		$userSel->odds = $selection->odds;
		$userSel->idcompetition = bets\Competition::getCurrent()->id;
		$userSel->insert();
	}

}

