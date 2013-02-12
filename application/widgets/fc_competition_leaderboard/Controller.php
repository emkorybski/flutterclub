<?php
require_once('custom/config.php');
require_once(PATH_LIB . 'fc.php');
require_once(PATH_DOMAIN . 'user.php');
require_once(PATH_DOMAIN . 'user_balance.php');

class Widget_FC_Competition_LeaderboardController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$competition = \bets\Competition::getCurrent();

		bets\bets::sql()->multiQuery("call fc_sp_get_competition_leaderboard($competition->id)");
		$leaderboardData = bets\bets::sql()->getResult();
		while (bets\bets::sql()->moreResults()) {
			bets\bets::sql()->getResult();
			bets\bets::sql()->nextResult();
		}
		
		//GET BLACKJACK RECORD
                
                bets\bets::sql()->multiQuery("call fc_sp_get_blackjack_record($competition->id)");
		$blackjackData = bets\bets::sql()->getResult();
		while (bets\bets::sql()->moreResults()) {
			bets\bets::sql()->getResult();
			bets\bets::sql()->nextResult();
		}

		$leaderboard = array();
		$blackjack = array();
		$position = 1;
		foreach ($leaderboardData as $leaderboardUserData) {
			$fcUser = \bets\User::get($leaderboardUserData['iduser']);
			$seUser = Engine_Api::_()->user()->getUser($fcUser->id_engine4_users);
			$successRate = \bets\fc::getPercentage($leaderboardUserData['won_count'], $leaderboardUserData['bet_count']);
			$userData = array(
			        'fcuser' => $fcUser,
				'position' => $position,
				'user' => $seUser,
				'profit' => number_format($leaderboardUserData['profit'], 2, '.', ','),
				'won_count' => $leaderboardUserData['won_count'],
				'bet_count' => $leaderboardUserData['bet_count'],
				'successRate' => $successRate);
			$leaderboard[] = $userData;
			$position++;
		}
		$this->view->leaderboardUsers = $leaderboard;
		
		foreach($blackjackData as $blackjackUserData){
                    $user_b_Data = array(
				
				'user' => $blackjackUserData['userID'],
				'blackjack_profit' => number_format($blackjackUserData['blackjack'], 2, '.', ',')
			        );	
                        $blackjack[] = $user_b_Data;
			
                }
                $this->view->blackjackUsers = $blackjack;
	}
}
