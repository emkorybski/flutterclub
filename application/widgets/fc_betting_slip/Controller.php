<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('custom/config.php');
require_once(PATH_LIB . 'fc.php');
require_once(PATH_DOMAIN . 'user.php');
require_once(PATH_DOMAIN . 'user_balance.php');
require_once(PATH_DOMAIN . 'selection.php');
require_once(PATH_DOMAIN . 'user_selection.php');
require_once(PATH_DOMAIN . 'bet.php');
require_once(PATH_DOMAIN . 'bet_selection.php');

class Widget_FC_Betting_SlipController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$action = isset($_REQUEST['action'])
			? strtolower($_REQUEST['action'])
			: 'render';

		switch ($action) {
			case 'place_bet':
				$response = array('success' => $this->placeBet());
				exit(json_encode($response));
				break;
			case 'remove_selection':
				$this->removeSelection();
				break;
			case 'remove_all':
				$this->removeAllSelections();
				break;
			default:
				$userSelections = bets\User::getCurrentUser()->getUserSelections();
				$this->view->betSlipSelections = $userSelections;
				$this->view->accumulatorBetAvailable = $this->validateAccumulator($userSelections);
		}
	}

	private function validateAccumulator($userSelections)
	{
		$betSlipEvents = array();
		$betSlipBetfairSelections = array();

		$isValid = true;
		foreach ($userSelections as $userSelection) {
			$selection = $userSelection->getSelection();
			$market = \bets\Event::get($selection->idevent);
			$event = $market->getParent();

			if (in_array($event->id, $betSlipEvents) || in_array($selection->betfairSelectionId, $betSlipBetfairSelections)) {
				$isValid = false;
				break;
			} else {
				array_push($betSlipEvents, $event->id);
				array_push($betSlipBetfairSelections, $selection->betfairSelectionId);
			}
		}

		return $isValid;
	}

	private function validateSelectionMaxStake()
	{
		$betSlipSelectionsStakes = array();

		$betSlipSelections = $_REQUEST['bets'];
		foreach ($betSlipSelections as $betSlipSelection) {
			$betSlipSelectionId = $betSlipSelection['user_selection_id'];
			$betSlipSelectionStake = $betSlipSelection['stake'];

			if ($betSlipSelectionId == 'accumulator') {
				foreach ($betSlipSelections as $accSelection) {
					if ($accSelection['user_selection_id'] == 'accumulator') continue;

					$accSelectionId = $accSelection['user_selection_id'];
					$betSlipSelectionsStakes[$accSelectionId] += $betSlipSelectionStake;
				}
			} else {
				$betSlipSelectionsStakes[$betSlipSelectionId] += $betSlipSelectionStake;
			}
		}

		$isValid = true;
		$user = bets\User::getCurrentUser();
		foreach ($betSlipSelectionsStakes as $betSlipSelectionId => $betSlipSelectionStake) {
			$userSelection = \bets\UserSelection::get($betSlipSelectionId);
			$selection = $userSelection->getSelection();

			$betSelections = \bets\BetSelection::findWhere(array('iduser=' => $user->id, 'idselection=' => $selection->id));
			$totalStake = 0;
			foreach ($betSelections as $betSelection) {
				$bet = \bets\Bet::get($betSelection->idbet);
				$totalStake += $bet->stake;
			}
			if ($totalStake + $betSlipSelectionStake > 500) {
				$isValid = false;
				break;
			}
		}
		return $isValid;
	}

	private function placeBet()
	{
		if (!$this->validateSelectionMaxStake()) {
			return false;
		}

		$competition = bets\Competition::getCurrent();
		$user = bets\User::getCurrentUser();

		$totalStake = 0;
		foreach ($_REQUEST['bets'] as $betSlipSelection) {
			if ($betSlipSelection['user_selection_id'] == 'accumulator') {
				$bet = new \bets\Bet();
				$bet->idcompetition = $competition->id;
				$bet->iduser = $user->id;
				$bet->odds = 1;
				$bet->stake = $betSlipSelection['stake'];
				$bet->ts_placed = \bets\fc::getGMTTimestamp();
				$bet->insert();
				$totalStake += $bet->stake;

				$userSelections = \bets\UserSelection::findWhere(array('iduser=' => $user->id));
				$odds = 1;
				foreach ($userSelections as $userSel) {
					$odds *= $userSel->odds;

					$selection = \bets\Selection::get($userSel->idselection);

					$betSelection = new \bets\BetSelection();
					$betSelection->idbet = $bet->id;
					$betSelection->idselection = $selection->id;
					$betSelection->name = $selection->name;
					$betSelection->odds = $selection->odds;
					$betSelection->insert();
				}

				$bet->odds = $odds;
				$bet->update();
			} else {
				$idUserSel = $betSlipSelection['user_selection_id'];
				$userSel = \bets\UserSelection::get($idUserSel);

				$selection = \bets\Selection::get($userSel->idselection);

				$bet = new \bets\Bet();
				$bet->idcompetition = $competition->id;
				$bet->iduser = $user->id;
				$bet->odds = $selection->odds;
				$bet->stake = $betSlipSelection['stake'];
				$bet->ts_placed = \bets\fc::getGMTTimestamp();
				$bet->insert();
				$totalStake += $bet->stake;

				$betSelection = new \bets\BetSelection();
				$betSelection->idbet = $bet->id;
				$betSelection->idselection = $selection->id;
				$betSelection->name = $selection->name;
				$betSelection->odds = $selection->odds;
				$betSelection->insert();
			}
		}
		\bets\UserBalance::updateUserBalance(-1 * $totalStake);

		// remove user selections from the bet slip
		foreach ($_REQUEST['bets'] as $betSlipSelection) {
			if ($betSlipSelection['user_selection_id'] == 'accumulator') {
				$userSelections = \bets\UserSelection::findWhere(array('iduser=' => $user->id));
				foreach ($userSelections as $userSel) {
					$userSel->delete();
				}
			} else {
				$idUserSel = $betSlipSelection['user_selection_id'];
				$userSel = \bets\UserSelection::get($idUserSel);
				$userSel->delete();
			}
		}

		return true;
	}

	private function removeSelection()
	{
		$userSelectionId = $_REQUEST['user_selection_id'];
		$userSelection = bets\UserSelection::get($userSelectionId);
		$userSelection->delete();
	}

	private function removeAllSelections()
	{
		foreach (bets\User::getCurrentUser()->getUserSelections() as $userSel) {
			$userSel->delete();
		}
	}
}