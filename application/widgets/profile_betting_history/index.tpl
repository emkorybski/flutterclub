<style type="text/css">
	.profile_betting_history {
		background-color: #fff;
		padding: 10px;
	}

	.profile_betting_history ul li {
		margin-bottom: 10px;
	}

	.profile_betting_history ul li + li {
		border-top: 1px solid #eaeaea;
		padding-top: 10px;
	}

	.profile_betting_history .bet_info {
		font-weight: bold;
		margin-bottom: 10px;
	}

	.profile_betting_history .bet_info span + span {
		margin-left: 10px;
	}

	.profile_betting_history .bet_info .bet_stake:before {
		content: 'FB$ ';
	}

	.profile_betting_history .bet_info .bet_status, .profile_betting_history .selection_status {
		text-transform: capitalize;
	}

	.profile_betting_history table {
		margin-left: 20px;
	}

	.profile_betting_history table td:first-child {
		font-weight: bold;
		padding-right: 20px;
	}
</style>

<div class="profile_betting_history">
<?php
if ( count($this->bettingHistory) > 0 ) :
?>
	<ul>
		<?php
		foreach ($this->bettingHistory as $bet) :
			$betSelections = $bet->getSelections();
			$isAccumulator = count($betSelections) > 1;
		?>
		<li>
			<div class="bet_info">
				<span class="bet_type"><?=($isAccumulator ? 'Accumulator' : 'Single')?></span>
				<span class="bet_odds"><?=\bets\fc::formatOdds($bet->odds)?></span>
				<span class="bet_stake"><?=$bet->stake?></span>
				<span class="bet_status"><?=$bet->status?></span>
			</div>
			<?php
			foreach ($betSelections as $betSelection) :
				$selection = \bets\Selection::get($betSelection->idselection);
				$market = \bets\Event::get($selection->idevent);
				$event = $market->getParent();
			?>
			<table>
				<tbody>
					<tr>
						<td class="selection_name">Selection</td>
						<td class="selection_name"><?=$betSelection->name?></td>
					</tr>
					<tr>
						<td class="selection_event">Event</td>
						<td class="selection_event"><?=$event->name?> ( <?=$market->name?> )</td>
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