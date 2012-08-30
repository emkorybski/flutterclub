<?php

require_once('custom/config.php');
require_once(PATH_DOMAIN . 'bet.php');
require_once(PATH_DOMAIN . 'bet_selection.php');
require_once(PATH_DOMAIN . 'selection.php');
require_once(PATH_DOMAIN . 'event.php');
require_once(PATH_LIB . 'fc.php');

class Widget_FC_TemplateController extends Engine_Content_Widget_Abstract
{
	private $templateTypes = array(
		'activity.bet_settlement',
		'mail.bet_settlement',
	);

	public function indexAction()
	{
		$templateType = isset($_REQUEST['template']) ? $_REQUEST['template'] : null;
		if (!$templateType || !in_array($templateType, $this->templateTypes)) {
			$this->setNoRender(true);
			return;
		}

		$idBet = intval($_REQUEST['id_bet']);
		$bet = \bets\Bet::get($idBet);
		$betType = count($bet->getSelections()) == 1 ? 'single' : 'accumulator';
		switch ($templateType) {
			case 'activity.bet_settlement':
				$this->_action = "activity-bet-settlement-$betType";
				break;
			case 'mail.bet_settlement':
				$this->_action = "bet-info-$betType";
				break;
		}

		$this->view->bet = $bet;
		$this->getElement()->clearDecorators();
	}
}