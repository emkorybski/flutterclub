<?php if (isset($_REQUEST['format']) && ($_REQUEST['format'] == 'html')) { ?>
<div class="generic_layout_container fc_betting_recent tabs_box" id="fc_betting_recent">
<?php } ?>

	<style type="text/css">
		.layout_right {
			margin-right: 10px;
		}
		.layout_fc_betting_recent {
			box-shadow: 5px 5px 15px 0 #cccccc;
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
		.recent_item .box_bet_amount {
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
		<ul>
		<?php
		foreach ($this->recent_bets as $bet) {
			$betSelections = $bet->getSelections();
			$isAccumulator = count($betSelections) > 1;
		?>
			<li>
				<?php echo $isAccumulator ? 'accumulator' : 'single' ?>
				&nbsp;
				<?=$bet->amount?>
				&nbsp;
				<?=$bet->odds?>
				<?php
					if ($bet->status == 'won') {
						echo '<span style="color: green; font-size: 14px;"><strong>' . '+'.number_format($bet->amount * ($bet->odds - 1), 2, '.', '') . '</strong></span>';
					}
					else {
						echo '<span style="color: red; font-size: 14px"><strong>' . '-'.$bet->amount . '</strong></span>';
					}
				?>
				<ul>
				<?php
				foreach ($betSelections as $selection) {
				?>
					<li>&nbsp;&nbsp;&nbsp;->&nbsp;<?=$selection->name?>, <?=$selection->odds?>, <?=$selection->status?><br/></li>
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