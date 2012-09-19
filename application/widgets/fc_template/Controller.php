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
		'activity.bet_share'
	);

	public function indexAction()
	{
		$templateType = isset($_REQUEST['template']) ? $_REQUEST['template'] : null;
		if (!$templateType || !in_array($templateType, $this->templateTypes)) {
			$this->setNoRender(true);
			return;
		}

		if ($templateType == 'activity.bet_settlement' || $templateType == 'mail.bet_settlement') {
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
		} else if ($templateType == 'activity.bet_share') {
			$this->_action = "activity-bet-share";
			$idSelection = intval($_REQUEST['id_selection']);
			$selection = \bets\Selection::get($idSelection);
			$this->view->message = $_REQUEST['message'];
			$this->view->selection = $selection;
		}

		$this->getElement()->clearDecorators();
	}
}