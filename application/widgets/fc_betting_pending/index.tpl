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
		.pending_item .box_bet_amount {
			width: 20%;
		}
		.pending_item .box_action {
			width: 10%;
		}
		
		.pending_actions {
			padding: 10px 10px 0 0;
		}
		.pending_actions .action {
			float: left;
			text-decoration: none;
			border: 1px solid #5f93b4;
			margin-left: 10px;
			margin-bottom: 10px;
			width: 93px;
			text-align: center;
			border-radius: 10px;
		}
		.pending_actions .action:hover {
			background-color: #eee;
		}
		.pending_actions a {
			padding: 5px;
			display: inline-block;
		}
		.pending_actions a:hover {
			text-decoration: none;
		}
	</style>
	
	<div class="fc_betting_pending">
		
		<?php foreach ($this->pending as $userSel) {
			$sel = $userSel->getSelection();
		?>
		<a href="#" class="pending_item">
			<div class="box box_name box_update" title="<?=htmlentities($sel->name)?>"><?=htmlentities($sel->name)?></div>
			<div class="box box_odds box_update" title="Odds 1:<?=round($userSel->odds, 2)?>">1:<?=round($userSel->odds, 2)?></div>
			<div class="box box_bet_amount box_update" title="<?=round($userSel->bet_amount)?> points"><?=round($userSel->bet_amount)?></div>
			<div class="box box_action" title="Select several bets and an choose action from below"><input type="checkbox" value="<?=$userSel->id?>" /></div>
			<div class="clear"></div>
		</a>
		<hr class="line" />
		<?php } ?>
		<div class="pending_actions">
			<div class="action"><a href="#" class="action_approve">Confirm bet</a></div>
			<div class="action"><a href="#" class="action_remove">Remove bet</a></div>
			<div class="clear"></div>
		</div>
	</div>

	<script type="text/javascript">
		(function () {
			j('.pending_item').click(function (e) { e.preventDefault(); });
			j('.pending_item .box_action').click(function (e) {
				e.preventDefault();
				e.stopPropagation();
				var inp = j(this).find('input');
				var c = inp.prop('checked');
				setTimeout(function () {
					inp.prop('checked', c);
				});
			});
			
			var getSelection = function () {
				return j('.fc_betting_pending .pending_item .box_action input:checked');
			}

			j('.pending_actions a').click(function (e) {
				e.preventDefault();
				var action = j(this);
								
				if (action.hasClass('action_approve')) {
					var sel = getSelection();
					if (sel.length < 1) {
						alert('Please select at least one bet, before you confirm!');
						return;
					}
					if (!confirm((sel.length >= 1) ? ('Do you want to confirm and set these ' + sel.length + ' bets?') : '')) {
						return;
					}
					j.ajax('/fc/widget/index/name/fc_betting_pending?format=html', {
						data: { action: 'approve', iduserselection: sel.map(function() { return j(this).val() }).get() },
						dataType: 'html',
						success: function () { fc.user.updateBettingSlip(); fc.user.updateBettingPending(); fc.user.updateBettingRecent(); fc.user.updateBettingUpcoming(); }
					});
				}
				
				
				if (action.hasClass('action_remove')) {
					var sel = getSelection();
					if (sel.length < 1) {
						alert('Select the bets you want to remove.');
						return;
					}
					if (!confirm((sel.length > 1) ? ('Do you want to remove these ' + sel.length + ' bets?') : 'Do you want to remove this bet?')) {
						return;
					}
					j.ajax('/fc/widget/index/name/fc_betting_pending?format=html', {
						data: { action: 'remove', iduserselection: sel.map(function() { return j(this).val() }).get() },
						dataType: 'html',
						success: function () { fc.user.updateBettingSlip(); fc.user.updateBettingPending(); fc.user.updateBettingUpcoming(); fc.user.updateBettingRecent();}
					});
				}
			});
		})();
	</script>
	
<?php if (isset($_REQUEST['format']) && ($_REQUEST['format'] == 'html')) { ?>
</div>
<?php } ?>

