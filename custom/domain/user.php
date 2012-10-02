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

	public function getUserSelections($validate = false)
	{
		if ($validate) {
			$now = date('Y-m-d H:i:s', mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
			$userSelections = UserSelection::findWhere(array('idcompetition=' => $this->currentCompetitionId, 'iduser=' => $this->id));
			foreach ($userSelections as $userSelection) {
				$selection = $userSelection->getSelection();
				$event = $selection->getEvent();
				if ($event->ts < $now) {
					$userSelection->delete();
				}
			}
		}
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
		$user = $uId ? User::get($uId) : self::getCurrentUser();
		$seUserId = $user->id_engine4_users;
		$data = \bets\bets::sql()->query("SELECT * FROM engine4_users WHERE user_id = '$seUserId'");
		return $data[0];
	}

	public static function getActivityBetSettlementNotification($pendingBet)
	{
		$queryStringData = array(
			'name' => 'fc_template',
			'id_bet' => $pendingBet->id,
			'template' => 'activity.bet_settlement',
			'format' => 'html');
		$templateWidgetUrl = WEB_HOST . WEB_ROOT . "widget?" . http_build_query($queryStringData);
		return trim(file_get_contents($templateWidgetUrl));
	}

	public static function getMailBetSettlementNotification($pendingBet)
	{
		$queryStringData = array(
			'name' => 'fc_template',
			'id_bet' => $pendingBet->id,
			'template' => 'mail.bet_settlement',
			'format' => 'html');
		$templateWidgetUrl = WEB_HOST . WEB_ROOT . "widget?" . http_build_query($queryStringData);
		return trim(file_get_contents($templateWidgetUrl));
	}

}
