<?php
$bet = $this->bet;
$betSelections = $bet->getSelections();
?>
just <?=$bet->status?> FB$ <?=\bets\fc::formatDecimalNumber($profit)?>!<br/><br/>
Odds: <?=\bets\fc::decimal2fractional($bet->odds)?><br/>
Stake: FB$ <?=$bet->stake?><br/>
Status: <?=$bet->status?><br/>
<br/>
<?php
$counter = 1;
foreach($betSelections as $betSelection) :
	$selection = $betSelection->getSelection();
	$event = $selection->getEvent();
?>
Selection #<?=$counter?>
Category: <?=$event->getPath()?>
Event: <?=$event->name?>
Bet: <?=$betSelection->name?>
Odds: <?=\bets\fc::decimal2fractional($bet->odds)?>
Status: <?=$bet->status?>
<br/>
<?php endforeach; ?>
