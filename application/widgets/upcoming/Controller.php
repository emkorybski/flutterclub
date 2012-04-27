<?php

class Widget_UpcomingController extends Engine_Content_Widget_Abstract
{
	
	public function indexAction()
	{
		require_once('custom/config.php');
		require_once(PATH_DOMAIN . 'event.php');
		$sport = (isset($_REQUEST['idsport']) ? (int)$_REQUEST['idsport'] : 0);
		$cond = array('idparent=' => 0);
		if ($sport) {
			$cond['idsport='] = $sport;
		}
		$this->view->upcoming = \bets\Event::findWhere($cond);
	}
	
}

