<style type="text/css">
	.layout_right {
		margin-right: 10px;
	}
	.layout_fc_betting_pending {
		box-shadow: none;
		border: 2px solid #CD4849;
		border-top: 0;
		margin-bottom: 1em;
	}
	.fc_betting_pending {
		background-color: #ffffff;
	}
	.fc_betting_pending > ul > li {
		padding-left: 10px;
		padding-top: 10px;
	}
	.fc_betting_pending li + li {
		border-top-width: 1px;
	}
	.fc_betting_pending .selection_name {
		font-weight: bold;
	}
	.fc_betting_pending .selection_status {
		display: block;
		text-transform: capitalize;
	}
	.pending_item {
		display: block;
		padding: 5px 10px;
		text-decoration: none;
	}
	.pending_item:hover {
		background-color: #e5e5e5;
	}
	.pending_item .box {
		float: left;
		font-family: fc_pts;
		color: #5f93b4;
		font-weight: bold;
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	}
	.pending_item .box_name {
		width: 50%;
	}
	.pending_item .box_odds {
		width: 20%;
	}
	.pending_item .box_action {
		width: 10%;
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
			<span class="bet_type"><?=($isAccumulator ? 'Accumulator' : 'Single')?></span>
			<span class="bet_stake"><?=$bet->stake?></span>
			<span class="bet_odds">(<?=\bets\fc::formatOdds($bet->odds)?>)</span>
			<ul>
<?php
				foreach ($betSelections as $selection) :
?>
				<li>
					<span class="selection_name"><?=$selection->name?></span>
					<span class="selection_odds">(<?=\bets\fc::formatOdds($selection->odds)?>)</span>
					<span class="selection_status"><?=$selection->status?></span>
				</li>
<?php
				endforeach;
?>
			</ul>
			<br/>
		</li>
<?php
		endforeach;
?>
	</ul>
<?php
	endif;
?>
</div>