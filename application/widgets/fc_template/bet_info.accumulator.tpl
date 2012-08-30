<?php
$bet = $this->bet;
$betSelection = $bet->getSelections();
?>
Odds: <?=\bets\fc::decimal2fractional($bet->odds)?>
Stake: FB$ <?=$bet->stake?>
Status: <?=$bet->status?>

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
<?php endforeach; ?>