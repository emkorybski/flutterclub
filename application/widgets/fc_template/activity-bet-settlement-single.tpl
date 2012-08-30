<?php
	$bet = $this->bet;
	$betSelection = \bets\BetSelection::getWhere(array('idbet=' => $bet->id));
	$selection = $betSelection->getSelection();
	$event = $selection->getEvent();
	$profit = $bet->status == 'won' ? $bet->stake * ($bet->odds - 1) : $bet->stake;
?>
just <?=$bet->status?> FB$ <?=\bets\fc::formatDecimalNumber($profit)?>!

Category: <?=$event->getPath()?>
Event: <?=$event->name?>
Bet: <?=$betSelection->name?>
Odds: <?=\bets\fc::decimal2fractional($bet->odds)?>
Stake:     FB$ <?=$bet->stake?>
Status: <span style="margin-left: 30px; color: red;"><?=$bet->status?></span>