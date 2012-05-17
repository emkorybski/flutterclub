<?php

require_once('custom/config.php');
require_once(PATH_DOMAIN . 'sport.php');

class Widget_CategoriesController extends \Engine_Content_Widget_Abstract {

	public function indexAction() {
		$this->view->categories = \bets\Sport::findWhere(array('enabled=' => 'y'));
	}

}

