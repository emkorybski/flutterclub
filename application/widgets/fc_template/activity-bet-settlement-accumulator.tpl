<?php
$bet = $this->bet;
$betSelections = $bet->getSelections();
$profit = $bet->status == 'won' ? $bet->stake * ($bet->odds - 1) : $bet->stake;
?>
just <?=$bet->status?> FB$ <?=\bets\fc::formatDecimalNumber($profit)?> with an accumulator!
Odds: <?=\bets\fc::decimal2fractional($bet->odds)?>

Stake: FB$ <?=$bet->stake?>

Status: <?=$bet->status?>
<br/>
<?php
$counter = 0;
foreach($betSelections as $betSelection) :
	$counter++;
	$selection = $betSelection->getSelection();
	$event = $selection->getEvent();
?>
Selection #<?=$counter?>

Date: <?=\bets\fc::formatTimestamp($event->getParent()->ts)?>

Event: <?=$event->getParent()->name?>

Market: <?=$event->name?>

Bet: <?=$betSelection->name?>

Odds: <?=\bets\fc::decimal2fractional($betSelection->odds)?>

Status: <?=$bet->status?>
<br/>
<?php endforeach; ?>