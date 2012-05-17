<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('custom/config.php');
require_once(PATH_DOMAIN . 'sport.php');

class Widget_Fc_AdminController extends Engine_Content_Widget_Abstract {

	public function indexAction() {
		switch (isset($_REQUEST['action']) ? $_REQUEST['action'] : '') {
			case 'setSports':
				$this->fc_setSports();
				exit;
			default:
				$this->fc_render();
				break;
		}
	}

	public function fc_render() {
		$this->view->sports = \bets\Sport::findAll();
	}

	public function fc_setSports() {
		$ids = (empty($_REQUEST['sports']) ? array() : array_map('intval', $_REQUEST['sports']));
		foreach (\bets\Sport::findAll() as $sport) {
			$sport->enabled = in_array($sport->id, $ids);
			$sport->update();
		}
		echo 'OK';
		exit;
	}

}

