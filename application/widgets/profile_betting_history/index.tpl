<style type="text/css">
/* add styling in here */
</style>

<div class="profile_betting_history">
<?php
if ( count($this->bettingHistory) > 0 ) :
?>
	<!--
	<div class="bet_info">
		<span class="bet_type">Bet Type</span> -
		<span class="bet_odds">Odds</span> -
		<span class="bet_stake">Stake</span> -
		<span class="bet_status">Status</span> -
	</div>
	-->
	<ul>
		<?php
		foreach ($this->bettingHistory as $bet) :
			$betSelections = $bet->getSelections();
			$isAccumulator = count($betSelections) > 1;
		?>
		<li>
			<div class="bet_info">
				<span class="bet_type"><?=($isAccumulator ? 'Accumulator' : 'Single')?></span> -
				<span class="bet_odds"><?=\bets\fc::formatOdds($bet->odds)?></span> -
				<span class="bet_stake"><?=$bet->stake?></span> -
				<span class="bet_status"><?=$bet->status?></span> -
			</div>
			<?php
			foreach ($betSelections as $betSelection) :
				$selection = \bets\Selection::get($betSelection->idselection);
				$event = \bets\Event::get($selection->idevent);
			?>
			<table>
				<tbody>
					<tr>
						<td class="selection_name">Selection</td>
						<td class="selection_name"><?=$betSelection->name?></td>
					</tr>
					<tr>
						<td class="selection_event">Event</td>
						<td class="selection_event"><?=$event->name?></td>
					</tr>
					<tr>
						<td class="selection_event_date">Event Date</td>
						<td class="selection_event_date"><?=$event->ts?></td>
					</tr>
					<tr>
						<td class="selection_odds">Odds</td>
						<td class="selection_odds"><?=\bets\fc::formatOdds($betSelection->odds)?></td>
					</tr>
					<tr>
						<td class="selection_status">Status</td>
						<td class="selection_status"><?=$betSelection->status?></td>
					</tr>
				</tbody>
			</table>
			<?php
			endforeach;
			?>
		</li>
	<?php
	endforeach;
	?>
	</ul>
<?php
else :
?>
	There are no settled bets for this user.
<?php
endif;
?>
</div>