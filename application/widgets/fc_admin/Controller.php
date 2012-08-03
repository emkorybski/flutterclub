<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('custom/config.php');
require_once(PATH_DOMAIN . 'sport.php');
require_once(PATH_DOMAIN . 'competition.php');

class Widget_Fc_AdminController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		switch (isset($_REQUEST['action']) ? $_REQUEST['action'] : '') {
			case 'sports_toggle':
				echo $this->toggle_sports();
				exit;
			case 'compUpdate':
				echo $this->fc_compUpdate();
				exit;
			case 'compDelete':
				echo $this->fc_compDelete();
				exit;
			case 'compAdd':
				echo $this->fc_compAdd();
				exit;
			case 'compEvents':
				echo '<script type="text/javascript">parent.Smoothbox.close()</script>';
				exit;
			default:
				echo $this->fc_render();
				break;
		}
	}

	public function fc_render()
	{
		$this->view->sports = bets\Sport::findAll('ORDER BY name ASC');
		$this->view->comps = bets\Competition::findAll();
		return '';
	}

	public function toggle_sports()
	{

		$sportIds = empty($_REQUEST['sportIds']) ? array() : array_map('intval', $_REQUEST['sportIds']);
		foreach (bets\Sport::findAll('ORDER BY id ASC') as $sport) {
			$sport->enabled = in_array($sport->id, $sportIds) ? 'y' : 'n';
			$sport->update();
		}
		return '{ "success" : true }';
	}

	public function fc_compUpdate()
	{
		$c = bets\Competition::get($_REQUEST['comp_id']);
		$c->name = $_REQUEST['comp_name'];
		$c->ts_start = date('Y-m-d H:i:s', strtotime($_REQUEST['comp_start']));
		$c->ts_end = date('Y-m-d H:i:s', strtotime($_REQUEST['comp_end']));
		return $c->update();
	}

	public function fc_compDelete()
	{
		$c = bets\Competition::get($_REQUEST['comp_id']);
		$c->delete();
		return 'OK';
	}

	public function fc_compAdd()
	{
		$c = new bets\Competition();
		$c->name = $_REQUEST['comp_name'];
		$c->ts_start = date('Y-m-d H:i:s', strtotime($_REQUEST['comp_start']));
		$c->ts_end = date('Y-m-d H:i:s', strtotime($_REQUEST['comp_end']));
		return $c->insert();
	}
}
