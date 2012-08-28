<?php

namespace bets;

require_once(PATH_LIB . 'dbrecord.php');
require_once(PATH_DOMAIN . 'user_selection.php');
require_once(PATH_DOMAIN . 'competition.php');
require_once(PATH_DOMAIN . 'bet.php');
require_once(PATH_DOMAIN . 'bet_selection.php');
require_once(PATH_DOMAIN . 'event.php');
require_once(PATH_DOMAIN . 'selection.php');

class User extends DBRecord
{
	protected static $_table = 'fc_user';
	private $currentCompetitionId;

	public static function getCurrentUser()
	{
		$socialId = (int)(\Zend_Auth::getInstance()->getIdentity());
		if (!$socialId) return null;

		$currentUser = static::getWhere(array('id_engine4_users=' => $socialId));
		if (!$currentUser) {
			$currentUser = new User();
			$currentUser->id_engine4_users = $socialId;
			$currentUser->insert();
		}
		$currentUser->currentCompetitionId = Competition::getCurrentId();
		return $currentUser;
	}

	public static function getUser($seUser)
	{
		$seUserId = $seUser->user_id;
		$fcUser = static::getWhere(array('id_engine4_users=' => $seUserId));
		$fcUser->currentCompetitionId = Competition::getCurrentId();
		return $fcUser;
	}

	public static function getSocialEngineUserId($fcUserId)
	{
		$user = User::get($fcUserId);
		return $user->id_engine4_users;
	}

	public function getUserSelections()
	{
		return UserSelection::findWhere(array('idcompetition=' => $this->currentCompetitionId, 'iduser=' => $this->id));
	}

	public function getPendingBets($limit = null)
	{
		$extraQuery = "ORDER BY ts_settled DESC" . ($limit != null ? " LIMIT $limit" : "");
		return Bet::findWhere(array('idcompetition=' => $this->currentCompetitionId, 'iduser=' => $this->id, 'status=' => 'pending'), $extraQuery);
	}

	public function getSettledBets($limit = null)
	{
		$extraQuery = "ORDER BY ts_settled DESC" . ($limit != null ? " LIMIT $limit" : "");
		return Bet::findWhere(array('idcompetition=' => $this->currentCompetitionId, 'iduser=' => $this->id, 'status != ' => 'pending'), $extraQuery);
	}

	public static function getCurrentUserData($uId = null)
	{
		$uId = $uId ? $uId : self::getCurrentUser()->id_engine4_users;
		$data = \bets\bets::sql()->query("SELECT * FROM engine4_users WHERE user_id = '$uId'");
		return $data[0];
	}

	public static function getSettledBetNotificationText($pendingBet)
	{
		$notificationText = file_get_contents(PATH_APP . "data/settled_bet_notification.tpl");

		$patterns = array();
		$patterns[0] = '/{name}/';
		$patterns[1] = '/{status}/';
		$patterns[2] = '/{odds}/';
		$patterns[3] = '/{stake}/';
		$patterns[4] = '/{earnings}/';
		$patterns[5] = '/{category}/';
		$patterns[6] = '/{event}/';
		$patterns[7] = '/{selection}/';

		$replacements = array();

		$userData = \bets\User::getCurrentUserData($pendingBet->iduser);
		$replacements[0] = $userData['displayname'];

		$replacements[1] = $pendingBet->status;
		$replacements[2] = fc::decimal2fractional($pendingBet->odds);
		$replacements[3] = number_format($pendingBet->stake, 2, '.', ',');
		$earnings = $pendingBet->status == 'won' ? $pendingBet->stake * ($pendingBet->odds - 1) : $pendingBet->stake;
		$replacements[4] = number_format($earnings, 2, '.', ',');

		$betSelections = $pendingBet->getSelections();
		$isAccumulator = count($betSelections) > 1;
		if (!$isAccumulator) {
			$selection = \bets\Selection::get($betSelections[0]->idselection);
			$event = $selection->getEvent();
			$replacements[5] = $event->getPath();
			$replacements[6] = $event->name;
			$replacements[7] = $selection->name;
		} else {
			$replacements[5] = '-';
			$replacements[6] = '-';
			$replacements[7] = 'ACCUMULATOR';
		}

		return preg_replace($patterns, $replacements, $notificationText);
	}
}
