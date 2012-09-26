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

		$competition = \bets\Competition::getWhere(array('settled=' => 'n', 'ts_end<' => $settlementDate->format('Y-m-d H:i:s')));
		if (!$competition)
			return;

		$this->competition = $competition;

		$this->voidPendingBets();

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
			$fromAddress = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.from', 'admin@' . $_SERVER['HTTP_HOST']);
			$fromName = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.name', 'Site Admin');

			$recipientEmail = 'paul.negrutiu@gmail.com';
			$recipientName = 'Paul Negrutiu';
			$subject = "Competition settlement - Leaderboard.";

			$mailApi = Engine_Api::_()->getApi('mail', 'core');
			$mail = $mailApi->create();

			$mail->addTo($recipientEmail, $recipientName);
			$mail->setFrom($fromAddress, $fromName);
			$mail->setSubject($subject);
			//$mail->setBodyHtml($emailHtmlBody);
			$mail->setBodyText($emailTextBody);
			$res = $mailApi->sendRaw($mail);
		} catch (Exception $e) {
		}
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
}