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
			case 'validate_bet':
				$response = array('result' => $this->validateBet());
				exit(json_encode($response));
				break;
			case 'place_bet':
				$response = array('result' => $this->placeBet());
				exit(json_encode($response));
				break;
			case 'remove_selection':
				$this->removeSelection();
				break;
			case 'remove_all':
				$this->removeAllSelections();
				break;
			default:
				$userSelections = bets\User::getCurrentUser()->getUserSelections(true);
				$this->view->betSlipSelections = $userSelections;
				$this->view->accumulatorBetAvailable = $this->validateAccumulator($userSelections);
				$this->view->maxPayoutAlert = $this->validateMaxPayout($userSelections);
		}
	}

	private function validateBalanceExceed()
	{
		$betTotalStake = 0;
		$betSlipSelections = $_REQUEST['bets'];
		foreach ($betSlipSelections as $betSlipSelection) {
			$betTotalStake += floatval($betSlipSelection['stake']);
		}

		$userBalance = \bets\UserBalance::getCurrentBalance(false);
		return $betTotalStake <= $userBalance->balance;
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
			$sport = $event->getSport();
			if ($sport->name == 'Horse Racing' || $sport->name == 'Horse Racing - Todays Card') continue;

			if (in_array($event->id, $betSlipEvents)) {
				$isValid = false;
				break;
			} else {
				array_push($betSlipEvents, $event->id);
				array_push($betSlipBetfairSelections, $selection->betfairSelectionId);
			}
		}

		return $isValid;
	}

	private function validateMaxPayout($userSelections)
	{
		$raiseAlert = false;
		$accumulatorOdds = 1;
		foreach ($userSelections as $userSelection) {
			$selection = $userSelection->getSelection();
			if ($selection->odds > 30) {
				$raiseAlert = true;
				break;
			}
			$accumulatorOdds *= $selection->odds;
		}
		if (!$raiseAlert && count($userSelections) > 1 && $accumulatorOdds > 30) {
			$raiseAlert = true;
		}

		return $raiseAlert;
	}

	private function validateSelectionTimestamp()
	{
		$competition = bets\Competition::getCurrent();
		$user = bets\User::getCurrentUser();

		$nowDatetime = new \DateTime();
		$now = $nowDatetime->format('Y-m-d H:i:s');

		$isValid = true;
		$userSelections = \bets\UserSelection::findWhere(array('idcompetition=' => $competition->id, 'iduser=' => $user->id));
		foreach ($userSelections as $userSelection) {
			$selection = $userSelection->getSelection();
			$event = $selection->getEvent();
			if ($event->ts < $now) {
				$isValid = false;
				$userSelection->delete();
			}
		}

		return $isValid;
	}

	private function validateSelectionMaxStake()
	{
		$user = bets\User::getCurrentUser();
		$betSlipSelectionsStakes = array();

		$betSlipSelections = $_REQUEST['bets'];
		foreach ($betSlipSelections as $betSlipSelection) {
			$betSlipSelectionId = $betSlipSelection['user_selection_id'];
			$betSlipSelectionStake = $betSlipSelection['stake'];

			if ($betSlipSelectionId == 'accumulator') {
				$userSelections = bets\User::getCurrentUser()->getUserSelections();
				foreach ($userSelections as $accUserSelection) {
					$accSelectionId = $accUserSelection->id;
					$betSlipSelectionsStakes[$accSelectionId] += $betSlipSelectionStake;
				}
			} else {
				$betSlipSelectionsStakes[$betSlipSelectionId] += $betSlipSelectionStake;
			}
		}

		$isValid = true;
		foreach ($betSlipSelectionsStakes as $betSlipSelectionId => $betSlipSelectionStake) {
			$userSelection = \bets\UserSelection::get($betSlipSelectionId);
			$selection = $userSelection->getSelection();

			$betSelections = \bets\BetSelection::findWhere(array('idselection=' => $selection->id));
			$totalStake = 0;
			foreach ($betSelections as $betSelection) {
				$bet = \bets\Bet::get($betSelection->idbet);
				if ($bet->iduser == $user->id) {
					$totalStake += $bet->stake;
				}
			}
			if ($totalStake + $betSlipSelectionStake > 500) {
				$isValid = false;
				break;
			}
		}
		return $isValid;
	}

	private function checkBet()
	{

		if (!$this->validateSelectionTimestamp()) {
			return 'invalid_selection_timestamp';
		}

		if (!$this->validateBalanceExceed()) {
			return 'balance_exceeded';
		}

		if (!$this->validateSelectionMaxStake()) {
			return 'max_stake_exceeded';
		}

		return 'ok';
	}

	private function computeBet(&$totalStake, &$betSlips)
	{
		$competition = bets\Competition::getCurrent();
		$user = bets\User::getCurrentUser();

		$totalStake = 0;
		$betSlips = array();

		foreach ($_REQUEST['bets'] as $betSlipSelection) {

			$betSlip = array();

			$bet = new \bets\Bet();
			$bet->idcompetition = $competition->id;
			$bet->iduser = $user->id;
			$bet->stake = $betSlipSelection['stake'];
			$bet->ts_placed = \bets\fc::getGMTTimestamp();
			$totalStake += $bet->stake;

			$betSelections = array();
			if ($betSlipSelection['user_selection_id'] == 'accumulator') {
				$bet->odds_real = 1;
				$bet->odds = 1;

				$userSelections = $user->getUserSelections();
				$odds_real = 1;
				$odds = 1;
				foreach ($userSelections as $userSel) {
					$selection = \bets\Selection::get($userSel->idselection);

					$odds_real *= $selection->odds;
					$odds *= \bets\fc::roundDecimalOdds($selection->odds);

					$betSelection = new \bets\BetSelection();
					$betSelection->idselection = $selection->id;
					$betSelection->name = $selection->name;
					$betSelection->odds = $selection->odds;

					array_push($betSelections, $betSelection);
				}

				$bet->odds_real = $odds_real;
				$bet->odds = \bets\fc::roundDecimalOdds($odds);
			} else {
				$idUserSel = $betSlipSelection['user_selection_id'];
				$userSel = \bets\UserSelection::get($idUserSel);

				$selection = \bets\Selection::get($userSel->idselection);

				$bet->odds = \bets\fc::roundDecimalOdds($selection->odds);
				$bet->odds_real = $selection->odds;

				$betSelection = new \bets\BetSelection();
				$betSelection->idselection = $selection->id;
				$betSelection->name = $selection->name;
				$betSelection->odds = $selection->odds;

				array_push($betSelections, $betSelection);
			}

			$betSlip['bet'] = $bet;
			$betSlip['selections'] = $betSelections;

			array_push($betSlips, $betSlip);
		}
	}

	private function validateBet()
	{

		$check = $this->checkBet();
		if ($check != 'ok') {
			return $check;
		}

		$totalStake = 0;
		$betSlips = null;

		$this->computeBet($totalStake, $betSlips);

		$slipDescription = '<table>';
		foreach ($betSlips as $betSlip) {
			$bet = $betSlip['bet'];
			$betSelections = $betSlip['selections'];
			$isAccumulator = count($betSelections) > 1;

			$slipDescription = $slipDescription . '<tr><td>';
			$slipDescription = $slipDescription . 'Place FB$' . $bet->stake;
			$slipDescription = $slipDescription . ($isAccumulator ? ' Accumulator' : '') . ' bet on ';
			$isFirstSelection = true;
			foreach ($betSelections as $betSelection) {
				$slipDescription = $slipDescription . ($isFirstSelection ? '' : ', ');
				$slipDescription = $slipDescription . $betSelection->name;
				$isFirstSelection = false;
			}
			$odds = min($bet->odds, 30);
			$slipDescription = $slipDescription . ' with a potential return of $FB' . ($bet->stake * $odds) . '. ';
			$slipDescription = $slipDescription . ($isAccumulator ? 'In a multiple bet all selections must win for you to win your bet as the individual odds are multiplied.' : '');

			$slipDescription = $slipDescription . '</td></tr>';
		}
		$slipDescription = $slipDescription . '</table>';

		$slipDescription = $slipDescription . '<p>Do you wish to place bet(s)?</p>';

		return $slipDescription;
	}

	private function placeBet()
	{

		$check = $this->checkBet();
		if ($check != 'ok') {
			return $check;
		}

		$user = bets\User::getCurrentUser();

		$totalStake = 0;
		$betSlips = null;

		$this->computeBet($totalStake, $betSlips);

		foreach ($betSlips as $betSlip) {
			$bet = $betSlip['bet'];
			$betSelections = $betSlip['selections'];

			$bet->insert();
			foreach ($betSelections as $betSelection) {
				$betSelection->idbet = $bet->id;
				$betSelection->insert();
			}
		}

		\bets\UserBalance::updateUserBalance(-1 * $totalStake);

		// remove user selections from the bet slip
		foreach ($_REQUEST['bets'] as $betSlipSelection) {
			if ($betSlipSelection['user_selection_id'] == 'accumulator') {
				$userSelections = $user->getUserSelections();
				foreach ($userSelections as $userSel) {
					$userSel->delete();
				}
			} else {
				$idUserSel = $betSlipSelection['user_selection_id'];
				$userSel = \bets\UserSelection::get($idUserSel);
				$userSel->delete();
			}
		}

		return 'success';
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