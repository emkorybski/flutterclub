<?php
	$bet = $this->bet;
	$betSelection = \bets\BetSelection::getWhere(array('idbet=' => $bet->id));
	$selection = $betSelection->getSelection();
	$event = $selection->getEvent();
	$profit = $bet->status == 'won' ? $bet->stake * ($bet->odds - 1) : $bet->stake;
?>
Event: <?=$event->getParent()->name?>

Market: <?=$event->name?>

Bet: <?=$betSelection->name?>

Odds: <?=\bets\fc::decimal2fractional($bet->odds)?>

Stake: FB$ <?=$bet->stake?>

Status: <?=$bet->status?>