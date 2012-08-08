<?php
require_once('custom/config.php');

require_once(PATH_LIB . 'bet_validator.php');

class Widget_FC_ScriptController extends Engine_Content_Widget_Abstract
{

	public function indexAction()
	{
		if (!empty($_REQUEST['action'])) {
			$action = $_REQUEST['action'] . 'Action';
			$this->$action();
		}
		$this->setNoRender();
	}

	public function betValidatorAction()
	{
		$betValidator = new \bets\BetValidator();
		$betValidator->validateBets();
	}
}
