<style type="text/css">
	.layout_fc_betting_pending {
		padding: 10px;
		background-color: #fff;
	}

	.fc_betting_pending ul li {
		margin-bottom: 10px;
	}

	.fc_betting_pending ul li:last-child {
		margin-bottom: 0;
	}

	.fc_betting_pending ul li + li {
		border-top: 1px solid #eaeaea;
		padding-top: 10px;
	}

	.fc_betting_pending .bet_info {
		font-weight: bold;
		background-color: #990000;
		color: white;
		padding: 10px 10px;
		margin-bottom: 0px;
		-webkit-box-shadow: 0px 5px 0px rgba(50, 50, 50, 0.5);
		-moz-box-shadow: 0px 5px 0px rgba(50, 50, 50, 0.5);
		box-shadow: 0px 3px 2px rgba(50, 50, 50, 0.5);
		position: relative;
	}

	.fc_betting_pending .bet_info span + span {
		margin-left: 10px;
	}

	.fc_betting_pending .bet_info .bet_stake:before {
		content: 'FB$ ';
	}

	.fc_betting_pending .bet_info .bet_status,
	.fc_betting_pending .selection_status {
		text-transform: capitalize;
	}

	.fc_betting_pending .bet_info .bet_odds:before {
		content: '(';
	}

	.fc_betting_pending .bet_info .bet_odds:after {
		content: ')';
	}

	.fc_betting_pending table {
		background-color: #dbe2e3;
		width: 100%;
		margin-left: 0;
	}

	.fc_betting_pending table + table {
		border-top: 5px solid #fff;
	}

	.fc_betting_pending table td:first-child {
		width: 80px;
		padding-left: 10px;
		text-align: right;
		vertical-align: top;
		font-weight: bold;
		padding-right: 10px;
	}

	.fc_betting_pending table tr:first-child td {
		padding-top: 10px;
	}

	.fc_betting_pending table td:last-child {
		padding-right: 10px;
	}

	.fc_betting_pending table tr:last-child td {
		padding-bottom: 10px;
	}
</style>

<div class="fc_betting_pending">
<?php
if ( count($this->pending_bets) > 0 ) :
?>
	<ul>
		<?php
		foreach ($this->pending_bets as $bet) :
			$betSelections = $bet->getSelections();
			$isAccumulator = count($betSelections) > 1;
		?>
		<li>
			<div class="bet_info">
				<span class="bet_type"><?=($isAccumulator ? 'Accumulator' : 'Single')?></span>
				<span class="bet_odds"><?=\bets\fc::formatOdds($bet->odds)?></span>
				<span class="bet_stake"><?=$bet->stake?></span>
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
					<td class="selection_event_date"><?=\bets\fc::formatTimestamp($event->ts)?></td>
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
	You have no pending bets.
<?php
endif;
?>
</div>