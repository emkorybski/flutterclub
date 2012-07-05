<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('custom/config.php');
require_once(PATH_DOMAIN . 'user_selection.php');

class Widget_Fc_Betting_SlipController extends Engine_Content_Widget_Abstract {

	public function indexAction() {
		switch (isset($_REQUEST['action']) ? strtolower($_REQUEST['action']) : '') {
			case 'cancel':
				foreach (bets\User::getCurrentUser()->getUserSelections() as $userSel) {
					$userSel->delete();
				}
				exit;
			case 'remove':
				foreach ($_REQUEST['iduserselection'] as $idUserSel) {
					$userSel = bets\UserSelection::get($idUserSel);
					$userSel->delete();
				}
				exit;
			case 'update':
				$userSel = bets\UserSelection::get($_REQUEST['iduserselection']);
				if ($userSel) {
					$userSel->bet_amount = (float)$_REQUEST['amount'];
					$userSel->update();
				}
				exit;
			default:
				$this->fc_render();
				break;
		}
	}

	public function fc_render() {
		$this->view->slip = bets\User::getCurrentUser()->getUserSelections();
	}

}

