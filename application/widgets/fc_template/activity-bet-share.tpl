<?php
$event = $this->selection->getEvent();
?>
<?=$this->message?><br/>

Selection details:

Date: <?=\bets\fc::formatTimestamp($event->getParent()->ts)?>

Event: <?=$event->getParent()->name?>

Market: <?=$event->name?>

Bet: <?=$this->selection->name?>

Odds: <?=\bets\fc::decimal2fractional($this->selection->odds)?>