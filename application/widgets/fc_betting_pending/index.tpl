<?php if (isset($_REQUEST['format']) && ($_REQUEST['format'] == 'html')) { ?>
<div class="generic_layout_container fc_betting_pending tabs_box" id="fc_betting_pending">
<?php } ?>

	<style type="text/css">
		.layout_right {
			margin-right: 10px;
		}
		.layout_fc_betting_pending {
			box-shadow: 5px 5px 15px 0 #cccccc;
		}
		
		.fc_betting_pending {
			background-color: #ffffff;
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
		<ul>
		<?php
		foreach ($this->pending_bets as $bet) {
			$betSelections = $bet->getSelections();
			$isAccumulator = count($betSelections) > 1;
		?>
			<li>
				<?php echo $isAccumulator ? 'accumulator' : 'single' ?>
				&nbsp;
				<?=$bet->stake?>
				&nbsp;
				<?=\bets\fc::formatOdds($bet->odds)?>
				<ul>
				<?php
				foreach ($betSelections as $selection) {
				?>
				<li>&nbsp;&nbsp;&nbsp;->&nbsp;<?=$selection->name?>, <?=\bets\fc::formatOdds($selection->odds)?>, <?=$selection->status?><br/></li>
				<?php
				}
				?>
				</ul>
				<br/>
			</li>
		<?php
			}
		?>
		</ul>
	</div>

<?php if (isset($_REQUEST['format']) && ($_REQUEST['format'] == 'html')) { ?>
</div>
<?php } ?>

