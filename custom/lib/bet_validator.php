<?php

namespace bets;

require_once(dirname(__FILE__) . '/../config.php');
require_once(PATH_LIB . 'bets.php');
require_once(PATH_LIB . 'social_engine.php');
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
				if ($betStatus == 'won') {
					$balance = \bets\UserBalance::getWhere(array('idcompetition=' => $pendingBet->idcompetition, 'iduser=' => $pendingBet->iduser));
					if ($balance) {
						$balance->balance += $pendingBet->stake * $pendingBet->odds;
						$balance->update();
					}
				}

				$seUserId = \bets\User::getSocialEngineUserId($pendingBet->iduser);
				$notificationText = \bets\User::getSettledBetNotificationText($pendingBet);
				\bets\SocialEngine::addActivityFeed($seUserId, $notificationText);
				\bets\User::sendEmail($pendingBet->iduser, $notificationText);
			}
		}
	}
}
