<style type="text/css">
	.layout_fc_betting_recent {
		padding: 10px;
		background-color: #fff;
	}

	.fc_betting_recent ul li {
		margin-bottom: 10px;
	}

	.fc_betting_recent ul li:last-child {
		margin-bottom: 0;
	}

	.fc_betting_recent ul li + li {
		border-top: 1px solid #eaeaea;
		padding-top: 10px;
	}

	.fc_betting_recent .bet_info {
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

	.fc_betting_recent .bet_info span + span {
		margin-left: 10px;
	}

	.fc_betting_recent .bet_info .bet_stake:before,
	.fc_betting_recent .bet_info .bet_earnings:before {
		content: 'FB$ ';
	}

	.fc_betting_recent .bet_info .bet_status,
	.fc_betting_recent .selection_status {
		text-transform: capitalize;
	}

	.fc_betting_recent .bet_info .bet_odds:before {
		content: '(';
	}

	.fc_betting_recent .bet_info .bet_odds:after {
		content: ')';
	}

	.fc_betting_recent table {
		background-color: #BFD8DF;
		width: 100%;
		margin-left: 0;
	}

	.fc_betting_recent table + table {
		border-top: 5px solid #fff;
	}

	.fc_betting_recent table td:first-child {
		width: 80px;
		padding-left: 10px;
		text-align: right;
		vertical-align: top;
		font-weight: bold;
		padding-right: 10px;
	}

	.fc_betting_recent table tr:first-child td {
		padding-top: 10px;
	}

	.fc_betting_recent table td:last-child {
		padding-right: 10px;
	}

	.fc_betting_recent table tr:last-child td {
		padding-bottom: 10px;
	}
</style>

<div class="fc_betting_recent">
<?php
if ( count($this->recent_bets) > 0 ) :
?>
	<ul>
		<?php
		foreach ($this->recent_bets as $bet) :
			$betSelections = $bet->getSelections();
			$isAccumulator = count($betSelections) > 1;
		?>
		<li>
			<div class="bet_info">
				<span class="bet_type"><?=($isAccumulator ? 'Accumulator' : 'Single')?></span>
				<span class="bet_odds"><?=\bets\fc::formatOdds($bet->odds)?></span>
				<span class="bet_stake"><?=$bet->stake?></span>
				<span class="bet_status status_<?=$bet->status?>"><?=$bet->status?></span>
				<?php
				if ($bet->status == 'won') :
				$betEarnings = $bet->stake * ($bet->odds - 1);
				?>
				<span class="bet_earnings"><?=number_format($betEarnings, 2, '.', ' ')?></span>
				<?php
				endif;
				?>
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
	You have no settled bets.
<?php
endif;
?>
</div>
