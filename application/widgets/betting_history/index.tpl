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

		.betting_history > div + div{
			padding-top: 10px;
			border-top-width: 1px;
		}
		
		.betting_history .acc{
			background: #f0f0f0;
		}
	</style>

	<div class="betting_history">
		<pre>
		<?php
		    //print_r($this->history);
		?>
		</pre>
		<?php foreach ($this->history as $sel) { 
					$isAcumulator = count($sel->betselection) > 1 ? true : false;
		?>
		<div>
			<!-- DATE -->
			<div class="betting_history_field_left">Date</div>
			<div class="betting_history_field_right"><?=$sel->bet->ts?></div>
			<!-- SPORT -->
			<div class="betting_history_field_left">Sport</div>
			<div class="betting_history_field_right"><?=$sel->selection->getEvent()->getSport()->name?></div>
			<!-- CATEGORY -->
			<div class="betting_history_field_left">Category</div>
			<div class="betting_history_field_right"><?=$sel->selection->getEvent()->topEvent()->name?></div>
			<!-- EVENT -->
			<div class="betting_history_field_left">Event</div>
			<div class="betting_history_field_right"><?=$sel->selection->getEvent()->name?></div>
			
			<?php
				
				if ($isAcumulator){
				?>
				<div class="acc betting_history_field_left">Bet type</div>
				<div class="acc betting_history_field_right">ACCUMULATOR</div>
				<?php
					foreach($sel->betselection as $bets){
					
			?>
			<!-- BETS -->
			<div class="line">
				<div class="betting_history_field_left acc">Bet</div>
				<div class="betting_history_field_right acc" title="<?=\bets\fc::formatOdds($bets->odds, 'decimal')?>"><?=$bets->name?> (<?=\bets\fc::formatOdds($bets->odds)?>)</div>
				<div class="clear"></div>
			</div>
			<?php
					}
				}
				else
				{
			?>
			<!-- BET -->
			<div class="betting_history_field_left">Bet</div>
			<div class="betting_history_field_right" title="<?=\bets\fc::formatOdds($sel->bet->odds, 'decimal')?>"><?=$sel->selection->name?> (<?=\bets\fc::formatOdds($sel->odds)?>)</div>
			<?php
				}
			?>
			<!-- STAKE -->
			<div class="betting_history_field_left">Stake</div>
			<div class="betting_history_field_right">FB$ <?=round($sel->bet->stake)?></div>
			<?php
			if ($sel->status == 'won' || $sel->status == 'lost'){
			?>
			<!-- RESULT -->
			<div class="acc betting_history_field_left">Result</div>
			<div class="acc betting_history_field_right betting_history_<?=$sel->getEarnings()?>"><?php echo $sel->getEarnings() ? 'WON: FB$ '.$sel->getEarnings() : 'LOST'; ?></div>
			<?php
			}
			?>
			<div class="clear"></div>
			<br />
		</div>
		<?php } ?>
	</div>

	<script type="text/javascript">
	</script>

</div>

