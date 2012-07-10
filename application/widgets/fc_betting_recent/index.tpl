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
		
		<?php 
			if (count($this->recent)){
			foreach ($this->recent as $userSel) {
			$sel = $userSel->getSelection();
		?>
		<div href="#" class="recent_item">
			<div class="box box_name box_update" title="<?=htmlentities($sel->name)?>"><?=htmlentities($sel->name)?></div>
			<div class="box box_odds box_update" title="Odds 1:<?=round($userSel->odds, 2)?>">1:<?=round($userSel->odds, 2)?></div>
			<div class="box box_bet_amount box_update" title="<?=round($userSel->bet_amount)?> points"><?=round($userSel->bet_amount)?></div>
			<?php
				if ($userSel->status != 'settled'){
			?>
				<div class="box box_bet_status box_update"><?=strtoupper($userSel->status)?></div>
			<?php
				}
			?>
			<div class="clear"></div>
		</div>
		<hr class="line" />
		<?php } } else { ?>
		<div class="recent_item">You don't have any settled bets</div>
		<?php } ?>
	</div>	
<?php if (isset($_REQUEST['format']) && ($_REQUEST['format'] == 'html')) { ?>
</div>
<?php } ?>

