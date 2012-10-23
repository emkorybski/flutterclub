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
			$betStatus = 1;
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

				if ($betStatus == 'won' || $betStatus == 'void') {
					$balance = \bets\UserBalance::getWhere(array('idcompetition=' => $pendingBet->idcompetition, 'iduser=' => $pendingBet->iduser));
					if ($balance) {
						$balance->balance += $pendingBet->stake + \bets\fc::getProfit($pendingBet->stake, $pendingBet->odds);
						$balance->update();
					}
				}

				if ($betStatus == 'void') continue;

				$seUserId = \bets\User::getSocialEngineUserId($pendingBet->iduser);
				$seUser = \Engine_Api::_()->user()->getUser($seUserId);

				// Add activity
				$activityNotificationText = \bets\User::getActivityBetSettlementNotification($pendingBet);
				\Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($seUser, $seUser, 'status', $activityNotificationText);
				//$seUser->status()->setStatus($notificationText);

				// Send email
				$userData = \bets\User::getCurrentUserData($pendingBet->iduser);
				$mailBetInfoText = \bets\User::getMailBetSettlementNotification($pendingBet);
				$profit = $betStatus == 'won' ? \bets\fc::getProfit($pendingBet->stake, $pendingBet->odds) : $pendingBet->stake;
				\Engine_Api::_()->getApi('mail', 'core')->sendSystem(
					$userData['email'],
					$betStatus == 'won' ? 'notify_bet_won' : 'notify_bet_lost',
					array(
						'profit' => fc::formatDecimalNumber($profit),
						'bet_info' => $mailBetInfoText,
						'betting_page' => WEB_HOST . WEB_ROOT . "pages/betting"
					));
			}
		}
	}
}
