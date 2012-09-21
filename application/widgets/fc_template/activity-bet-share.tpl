<?php
$event = $this->selection->getEvent();
?>
<?=$this->message?><br/>

<a href="<?=WEB_HOST . WEB_ROOT?>pages/betting?event=<?=$event->id?>" target="_blank" title="<?=$event->name?>"><?=$event->getParent()->name?>: <?=$event->name?></a>

Date: <?=\bets\fc::formatTimestamp($event->getParent()->ts)?>

Bet: <?=$this->selection->name?>

Odds: <?=\bets\fc::decimal2fractional($this->selection->odds)?>