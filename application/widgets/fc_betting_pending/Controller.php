<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('custom/config.php');
require_once(PATH_DOMAIN . 'user_selection.php');

class Widget_Fc_Betting_PendingController extends Engine_Content_Widget_Abstract {

	public function indexAction() {
		switch (isset($_REQUEST['action']) ? strtolower($_REQUEST['action']) : '') {
			case 'remove':
				foreach ($_REQUEST['iduserselection'] as $idUserSel) {
					$userSel = bets\UserSelection::get($idUserSel);
					$userSel->delete();
				}
				exit;
			case 'approve':
				foreach ($_REQUEST['iduserselection'] as $idUserSel) {
					$userSel = bets\UserSelection::get($idUserSel);
					if($userSel){
						$userSel->status = 'settled';
						$userSel->update();
					}
				}
				exit;
			default:
				$this->fc_render();
				break;
		}
	}

	public function fc_render() {
		$this->view->pending = bets\User::getCurrentUser()->getUserSelectionsPending();
	}

}

