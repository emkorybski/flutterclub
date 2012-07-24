<div class="generic_layout_container layout_betting_history">

	<style type="text/css">
		.layout_betting_history {
			background-color: #fff;
			padding: 5px;
		}

		.betting_history_field_left {
			clear: left;
			float: left;
			width: 70px;
			text-align: right;
			margin-right: 10px;
			color: #5f93b4;
			font-weight: 700;
			border: none;
		}

		.betting_history_field_right {
			float: left;
			width: auto;
			border: none;
		}

		.betting_history > div + div {
			padding-top: 10px;
			border-top-width: 1px;
		}
	</style>

	<div class="betting_history">
		<?php
		    //print_r($this->userSelections);
		?>
		
		<?php foreach ($this->userSelections as $sel) { ?>
		<div>
			<!-- DATE -->
			<div class="betting_history_field_left">Date</div>
			<div class="betting_history_field_right"><?=$sel->ts?></div>
			<!-- CATEGORY -->
			<div class="betting_history_field_left">Category</div>
			<div class="betting_history_field_right"><?=$sel->getSelection()->getEvent()->topEvent()->name?></div>
			<!-- EVENT -->
			<div class="betting_history_field_left">Event</div>
			<div class="betting_history_field_right"><?=$sel->getSelection()->getEvent()->name?></div>
			<!-- BET -->
			<div class="betting_history_field_left">Bet</div>
			<div class="betting_history_field_right" title="<?=\bets\fc::formatOdds($sel->odds, 'decimal')?>"><?=$sel->getSelection()->name?> <?=\bets\fc::formatOdds($sel->odds)?></div>
			<!-- STAKE -->
			<div class="betting_history_field_left">Stake</div>
			<div class="betting_history_field_right"><?=round($sel->bet_amount)?> points</div>
			<!-- RESULT -->
			<div class="betting_history_field_left">Result</div>
			<div class="betting_history_field_right betting_history_<?=$sel->status?>"><?=$sel->status?></div>
			<div class="clear"></div>
			<br />
		</div>
		<?php } ?>
	</div>

	<script type="text/javascript">
	</script>

</div>

