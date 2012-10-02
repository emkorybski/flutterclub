<?php
require_once('custom/config.php');

require_once(PATH_LIB . 'bet_validator.php');

class Widget_FC_ScriptController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		if (!empty($_REQUEST['action'])) {
			$action = $_REQUEST['action'] . 'Action';
			$this->$action();
		}
		$this->setNoRender();
	}

	public function betValidatorAction()
	{
		$betValidator = new \bets\BetValidator();
		$betValidator->validateBets();
	}

	public function competitionSettlementAction()
	{
		$currentDate = new \DateTime();
		$settlementDate = $currentDate->sub(new \DateInterval("P1D"));

		$this->competition = \bets\Competition::getWhere(array('settled=' => 'n', 'ts_end<' => $settlementDate->format('Y-m-d H:i:s')));
		if (!$this->competition)
			return;

		$this->voidPendingBets();
		$this->sendCompetitionLeaderboardEmail();
		$this->createFutureCompetition();

		$this->competition->settled = 'y';
		$this->competition->update();
	}

	private function voidPendingBets()
	{
		$pendingBets = \bets\Bet::findWhere(array('idcompetition=' => $this->competition->id, 'status=' => 'pending'));
		foreach ($pendingBets as $pendingBet) {
			$pendingSelections = \bets\BetSelection::findWhere(array('idbet=' => $pendingBet->id, 'status=' => 'pending'));
			foreach ($pendingSelections as $pendingSelection) {
				$pendingSelection->odds = 1;
				$pendingSelection->status = 'void';
				$pendingSelection->update();
			}
		}

		$betValidator = new \bets\BetValidator();
		$betValidator->validateBets();
	}

	private function sendCompetitionLeaderboardEmail()
	{
		$leaderboard = $this->getCompetitionLeaderboard();

		$emailTextBody = "Position, Name, Profit, Success Rate" . "\r\n";
		$emailHtmlBody = "<table border='1' bordercolor='#000000' cellpadding='5' cellspacing='0'>";
		$emailHtmlBody .= "
			<tr>
				<th>Position</th>
				<th>Name</th>
				<th>Profit</th>
				<th>Success Rate</th>
			</tr>";
		foreach ($leaderboard as $data) {
			$emailHtmlBody .= "<tr>";
			$emailHtmlBody .= "<td>" . $data['position'] . "</td>";
			$emailHtmlBody .= "<td>" . $data['user'] . "</td>";
			$emailHtmlBody .= "<td>" . number_format($data['profit'], 2, '.', ',') . "</td>";
			$emailHtmlBody .= "<td>" . $data['successRate'] . " ";
			$emailHtmlBody .= "(" . $data['won_count'] . "/" . $data['bet_count'] . ")</td>";
			$emailHtmlBody .= "</tr>";

			$emailTextBody .= $data['position'] . ", ";
			$emailTextBody .= $data['user'] . ", ";
			$emailTextBody .= number_format($data['profit'], 2, '.', ',') . ", ";
			$emailTextBody .= $data['successRate'] . " ";
			$emailTextBody .= "(" . $data['won_count'] . "/" . $data['bet_count'] . ")";
			$emailTextBody .= "\r\n";
		}
		$emailHtmlBody .= "</table>";

		try {
			$fromAddress = $toAddress = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.from', 'admin@' . $_SERVER['HTTP_HOST']);
			$fromName = $toName = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.name', 'Site Admin');
			$subject = "Competition settlement - Leaderboard.";

			$mailApi = Engine_Api::_()->getApi('mail', 'core');
			$mail = $mailApi->create();

			$mail->addTo($toAddress, $toName);
			$mail->setFrom($fromAddress, $fromName);
			$mail->setSubject($subject);
			$mail->setBodyHtml($emailHtmlBody);
			$mail->setBodyText($emailTextBody);
			$mailApi->sendRaw($mail);
		} catch (Exception $e) {
		}
	}

	private function getCompetitionLeaderboard()
	{
		$idCompetition = $this->competition->id;
		\bets\bets::sql()->multiQuery("call fc_sp_get_competition_leaderboard($idCompetition)");
		$leaderboardData = \bets\bets::sql()->getResult();
		while (\bets\bets::sql()->moreResults()) {
			\bets\bets::sql()->getResult();
			\bets\bets::sql()->nextResult();
		}

		$leaderboard = array();
		$position = 1;
		foreach ($leaderboardData as $leaderboardUserData) {
			$fcUser = \bets\User::get($leaderboardUserData['iduser']);
			$seUser = Engine_Api::_()->user()->getUser($fcUser->id_engine4_users);
			$successRate = \bets\fc::getPercentage($leaderboardUserData['won_count'], $leaderboardUserData['bet_count']);

			$userData = array(
				'position' => $position,
				'user' => $seUser->getTitle(),
				'profit' => number_format($leaderboardUserData['profit'], 2, '.', ','),
				'won_count' => $leaderboardUserData['won_count'],
				'bet_count' => $leaderboardUserData['bet_count'],
				'successRate' => $successRate);
			$leaderboard[] = $userData;
			$position++;
		}
		return $leaderboard;
	}

	private function createFutureCompetition()
	{
		$lastCompetition = \bets\Competition::getWhere(array(), "ORDER BY ts_end DESC");

		$endDate = new \DateTime($lastCompetition->ts_end);
		$endDate->add(new \DateInterval("P14D"));

		$newCompetition = new \bets\Competition();
		$newCompetition->start_balance = 10000;
		$newCompetition->ts_start = $lastCompetition->ts_end;
		$newCompetition->ts_end = date('Y-m-d H:i:s', $endDate->getTimestamp());
		$newCompetition->settled = 'n';
		$newCompetition->insert();
	}
}