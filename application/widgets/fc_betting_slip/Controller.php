<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('custom/config.php');
require_once(PATH_DOMAIN . 'user.php');
require_once(PATH_DOMAIN . 'user_balance.php');
require_once(PATH_DOMAIN . 'selection.php');
require_once(PATH_DOMAIN . 'user_selection.php');
require_once(PATH_DOMAIN . 'bet.php');
require_once(PATH_DOMAIN . 'bet_selection.php');
require_once(PATH_LIB . 'fc.php');

class Widget_FC_Betting_SlipController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		switch (isset($_REQUEST['action']) ? strtolower($_REQUEST['action']) : '') {

			case 'place_bet':
				$competition = bets\Competition::getCurrent();
				$user = bets\User::getCurrentUser();
				$totalStake = 0;
				foreach ($_REQUEST['bets'] as $userBet) {
					if ($userBet['user_selection_id'] == 'accumulator') {
						$bet = new \bets\Bet();
						$bet->idcompetition = $competition->id;
						$bet->iduser = $user->id;
						$bet->odds = 1;
						$bet->stake = $userBet['stake'];
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
						$idUserSel = $userBet['user_selection_id'];
						$userSel = \bets\UserSelection::get($idUserSel);

						$selection = \bets\Selection::get($userSel->idselection);

						$bet = new \bets\Bet();
						$bet->idcompetition = $competition->id;
						$bet->iduser = $user->id;
						$bet->odds = $selection->odds;
						$bet->stake = $userBet['stake'];
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
				$balance = \bets\UserBalance::getCurrentBalance();
				$balance->balance = $balance->balance - $totalStake;
				$balance->update();
				//TODO: update from within the class

				// remove user selections from the bet slip
				/*
				foreach ($_REQUEST['bets'] as $userBet) {
					if ($userBet['user_selection_id'] == 'accumulator') {
						$userSelections = \bets\UserSelection::findWhere(array('iduser=' => $user->id));
						foreach ($userSelections as $userSel) {
							$userSel->delete();
						}
					} else {
						$idUserSel = $userBet['user_selection_id'];
						$userSel = \bets\UserSelection::get($idUserSel);
						$userSel->delete();
					}
				}
				*/
				exit;

			case 'remove_selected':
				foreach ($_REQUEST['user_selection_ids'] as $idUserSel) {
					$userSel = bets\UserSelection::get($idUserSel);
					$userSel->delete();
				}
				exit;

			case 'remove_all':
				foreach (bets\User::getCurrentUser()->getUserSelections() as $userSel) {
					$userSel->delete();
				}
				exit;

			default:
				$this->fc_render();
				break;
		}
	}

	public function fc_render()
	{
		$this->view->betting_slip = bets\User::getCurrentUser()->getUserSelections();
	}
}