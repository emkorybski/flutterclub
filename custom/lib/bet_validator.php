<?php

namespace bets;

require_once(dirname(__FILE__) . '/../config.php');
require_once(PATH_LIB . 'bets.php');
require_once(PATH_LIB . 'fc.php');
require_once(PATH_DOMAIN . 'user.php');
require_once(PATH_DOMAIN . 'user_balance.php');
require_once(PATH_DOMAIN . 'bet.php');
require_once(PATH_DOMAIN . 'bet_selection.php');

class BetValidator
{
	public function validateBets()
	{
		$pendingBetsList = \bets\Bet::findWhere(array('status=' => 'pending'));
		foreach ($pendingBetsList as $pendingBet) {
			$betSelectionsList = \bets\BetSelection::findWhere(array('idbet=' => $pendingBet->id));

			$selectionStatus = array('void' => 1, 'won' => 2, 'pending' => 3, 'lost' => 4);
			$betTotalOdds = 1;
			$betStatus = 0;
			foreach ($betSelectionsList as $betSelection) {
				$betStatus = max($betStatus, $selectionStatus[$betSelection->status]);

				if ($betSelection->status == 'void') {
					$betSelection->odds = 1;
					$betSelection->update();
				} else {
					$betTotalOdds *= $betSelection->odds;
				}
			}
			$betStatus = array_search($betStatus, $selectionStatus);

			$pendingBet->odds = $betTotalOdds;
			$pendingBet->status = $betStatus;
			$pendingBet->update();
			if ($betStatus != 'pending') {
				$pendingBet->ts_settled = fc::getGMTTimestamp();
				$pendingBet->update();

				if ($betStatus == 'won') {
					$balance = \bets\UserBalance::getWhere(array('idcompetition=' => $pendingBet->idcompetition, 'iduser=' => $pendingBet->iduser));
					if ($balance) {
						$balance->balance += $pendingBet->stake * $pendingBet->odds;
						$balance->update();
					}
				}

				$seUserId = \bets\User::getSocialEngineUserId($pendingBet->iduser);
				$seUser = \Engine_Api::_()->user()->getUser($seUserId);
				$notificationText = \bets\User::getActivityBetSettlementNotification($pendingBet);

				// Add activity
				\Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($seUser, $seUser, 'status', $notificationText);
				//$seUser->status()->setStatus($notificationText);

				$userData = \bets\User::getCurrentUserData($pendingBet->iduser);
				\Engine_Api::_()->getApi('mail', 'core')->sendSystem($userData['email'], 'notify_bet_settlement', array(
					'bet_data' => $notificationText
				));
			}
		}
	}
}
