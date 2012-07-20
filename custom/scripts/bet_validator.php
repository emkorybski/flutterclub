<?php

namespace bets;

require_once(dirname(__FILE__) . '/../config.php');
require_once(PATH_LIB . 'bets.php');
require_once(PATH_LIB . 'social_engine.php');
require_once(PATH_DOMAIN . 'user.php');
require_once(PATH_DOMAIN . 'bet.php');
require_once(PATH_DOMAIN . 'bet_selection.php');

class BetValidator
{
	public function validateBets()
	{
		$pendingBetsList = \bets\bets::sql()->query("SELECT * FROM fc_bet WHERE status = 'pending'");
		foreach ($pendingBetsList as $pendingBetData) {
			$pendingBet = \bets\Bet::get($pendingBetData['id']);
			$betSelectionsList = \bets\bets::sql()->query("SELECT * FROM fc_bet_selection WHERE idbet = " . $pendingBet->id);

			$selectionStatus = array('void' => 1, 'won' => 2, 'pending' => 3, 'lost' => 4);
			$betTotalOdds = 1;
			$betStatus = 0;
			foreach ($betSelectionsList as $betSelectionData) {
				$betSelection = \bets\BetSelection::get($betSelectionData['id']);
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
				$seUserId = \bets\User::getSocialEngineUserId($pendingBet->iduser);
				$betInfo = $pendingBet->id . " ; " . $pendingBet->odds . " ; " . $pendingBet->stake . " ; " . $pendingBet->status;
				echo $betInfo;
				\bets\SocialEngine::addActivityFeed($seUserId, "Another bet settled: " . $betInfo);
			}
		}
	}
}

$betValidator = new BetValidator();
$betValidator->validateBets();