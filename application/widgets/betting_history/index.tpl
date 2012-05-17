<div class="generic_layout_container layout_betting_history">

	<style type="text/css">
		.betting_history_field_left {
			clear: left;
			float: left;
			width: 200px;
			border: 1px solid blue;
		}
		.betting_history_field_right {
			float: left;
			width: 300px;
			border: 1px solid green;
		}
	</style>

	<div class="betting_history">
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
			<div class="betting_history_field_right"><?=$sel->getSelection()->name?> (<?=$sel->odds?>:1)</div>
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

