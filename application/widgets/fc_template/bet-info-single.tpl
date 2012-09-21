<?php
	$bet = $this->bet;
	$betSelection = \bets\BetSelection::getWhere(array('idbet=' => $bet->id));
	$selection = $betSelection->getSelection();
	$event = $selection->getEvent();
	$profit = $bet->status == 'won' ? \bets\fc::getProfit($bet->stake, $bet->odds) : $bet->stake;
?>
Date: <?=\bets\fc::formatTimestamp($event->getParent()->ts)?>

Event: <?=$event->getParent()->name?>

Market: <?=$event->name?>

Bet: <?=$betSelection->name?>

Odds: <?=\bets\fc::decimal2fractional($bet->odds)?>

Stake: FB$ <?=$bet->stake?>

Status: <?=ucfirst($bet->status)?>