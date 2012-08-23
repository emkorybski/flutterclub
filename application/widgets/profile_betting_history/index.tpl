<style type="text/css">
	.profile_betting_history {
		background-color: #fff;
		padding: 10px;
	}

	.profile_betting_history ul li {
		margin-bottom: 10px;
	}

	.profile_betting_history ul li:last-child {
		margin-bottom: 0;
	}

	.profile_betting_history ul li + li {
		border-top: 1px solid #eaeaea;
		padding-top: 10px;
	}

	.profile_betting_history .bet_info {
		font-weight: bold;
		background-color: #CD4849;
		color: white;
		padding: 10px 10px;
		margin-bottom: 0px;
		-webkit-box-shadow: 0px 5px 0px rgba(50, 50, 50, 0.5);
		-moz-box-shadow: 0px 5px 0px rgba(50, 50, 50, 0.5);
		box-shadow: 0px 3px 2px rgba(50, 50, 50, 0.5);
		position: relative;
	}

	.profile_betting_history .bet_info span + span {
		margin-left: 10px;
	}

	.profile_betting_history .bet_info .bet_stake:before {
		content: 'FB$ ';
	}

	.profile_betting_history .bet_info .bet_status,
	.profile_betting_history .selection_status {
		text-transform: capitalize;
	}

	.profile_betting_history .bet_info .bet_odds:before {
		content: '(';
	}

	.profile_betting_history .bet_info .bet_odds:after {
		content: ')';
	}

	.profile_betting_history table {
		background-color: #BFD8DF;
		width: 100%;
		margin-left: 0;
	}

	.profile_betting_history table + table {
		border-top: 5px solid #fff;
	}

	.profile_betting_history table td:first-child {
		width: 80px;
		padding-left: 10px;
		text-align: right;
		vertical-align: top;
	}

	.profile_betting_history table tr:first-child td {
		padding-top: 10px;
	}

	.profile_betting_history table td:last-child {
		padding-right: 10px;
	}

	.profile_betting_history table tr:last-child td {
		padding-bottom: 10px;
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
				<span class="bet_status status_<?=$bet->status?>"><?=$bet->status?></span>
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
						<td class="selection_status status_<?=$betSelection->status?>"><?=$betSelection->status?></td>
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