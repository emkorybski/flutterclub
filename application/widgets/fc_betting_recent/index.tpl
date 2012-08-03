<style type="text/css">
	.layout_right {
		margin-right: 10px;
	}
	.layout_fc_betting_recent {
		box-shadow: none;
		border: 2px solid #CD4849;
		border-top: 0;
		margin-bottom: 1em;
	}
	.fc_betting_recent {
		background-color: #ffffff;
	}
	.recent_item {
		display: block;
		padding: 5px 10px;
		text-decoration: none;
	}
	.recent_item:hover {
		background-color: #e5e5e5;
	}
	.recent_item .box {
		float: left;
		font-family: fc_pts;
		color: #5f93b4;
		font-weight: bold;
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	}
	.recent_item .box_name {
		width: 50%;
	}
	.recent_item .box_odds {
		width: 20%;
	}
	.recent_item .box_action {
		width: 10%;
	}
	.recent_actions {
		padding: 10px 10px 0 0;
	}
	.recent_actions .action {
		float: left;
		text-decoration: none;
		border: 1px solid #5f93b4;
		margin-left: 10px;
		margin-bottom: 10px;
		width: 93px;
		text-align: center;
		border-radius: 10px;
	}
	.recent_actions .action:hover {
		background-color: #eee;
	}
	.recent_actions a {
		padding: 5px;
		display: inline-block;
	}
	.recent_actions a:hover {
		text-decoration: none;
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
			<span class="bet_type"><?=($isAccumulator ? 'accumulator' : 'single')?></span>
			<span class="bet_stake"><?=$bet->stake?></span>
			<span class="bet_odds"><?=\bets\fc::formatOdds($bet->odds)?></span>
			<span class="bet_status"><?=$bet->status?></span>
<?php
			if ($bet->status == 'won') :
?>
			<span class="bet_status_won">+ <?=number_format($bet->stake * ($bet->odds - 1), 2, '.', '')?></span>
<?php
			elseif ($bet->status == 'lost') :
?>
			<span class="bet_status_lost">- <?=$bet->stake?></span>
<?php
			elseif ($bet->status == 'void') :
?>
			<span class="bet_status_void">0</span>
<?php
			endif;
?>
			<ul>
<?php
				foreach ($betSelections as $selection) :
?>
				<li>
					<span class="selection_name"><?=$selection->name?></span>
					<span class="selection_odds"><?=\bets\fc::formatOdds($selection->odds)?></span>
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
