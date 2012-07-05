<?php if (isset($_REQUEST['format']) && ($_REQUEST['format'] == 'html')) { ?>
<div class="generic_layout_container fc_betting_slip tabs_box" id="fc_betting_slip">
<?php } ?>

	<style type="text/css">
		.layout_right {
			margin-right: 10px;
		}
		.layout_fc_betting_slip {
			box-shadow: 5px 5px 15px 0 #cccccc;
		}
		
		.fc_betting_slip {
			background-color: #ffffff;
		}
		.slip_item {
			display: block;
			padding: 5px 10px;
			text-decoration: none;
		}
		.slip_item:hover {
			background-color: #e5e5e5;
		}
		.slip_item .box {
			float: left;
			font-family: fc_pts;
			color: #5f93b4;
			font-weight: bold;
			overflow: hidden;
			white-space: nowrap;
			text-overflow: ellipsis;
		}
		.slip_item .box_name {
			width: 50%;
		}
		.slip_item .box_odds {
			width: 20%;
		}
		.slip_item .box_bet_amount {
			width: 20%;
		}
		.slip_item .box_action {
			width: 10%;
		}
		
		.slip_actions {
			padding: 10px 10px 0 0;
		}
		.slip_actions .action {
			float: left;
			text-decoration: none;
			border: 1px solid #5f93b4;
			margin-left: 10px;
			margin-bottom: 10px;
			width: 93px;
			text-align: center;
			border-radius: 10px;
		}
		.slip_actions .action:hover {
			background-color: #eee;
		}
		.slip_actions a {
			padding: 5px;
			display: inline-block;
		}
		.slip_actions a:hover {
			text-decoration: none;
		}
	</style>
	
	<div class="fc_betting_slip">
		
		<?php foreach ($this->slip as $userSel) {
			$sel = $userSel->getSelection();
		?>
		<a href="#" class="slip_item">
			<div class="box box_name box_update" title="<?=htmlentities($sel->name)?>"><?=htmlentities($sel->name)?></div>
			<div class="box box_odds box_update" title="Odds 1:<?=round($userSel->odds, 2)?>">1:<?=round($userSel->odds, 2)?></div>
			<div class="box box_bet_amount box_update" title="<?=round($userSel->bet_amount)?> points"><?=round($userSel->bet_amount)?></div>
			<div class="box box_action" title="Select several bets and an choose action from below"><input type="checkbox" value="<?=$userSel->id?>" /></div>
			<div class="clear"></div>
		</a>
		<hr class="line" />
		<?php } ?>
		<div class="slip_actions">
			<div class="action"><a href="#" class="action_approve">Approve</a></div>
			<div class="action"><a href="#" class="action_accumulate">Accumulate</a></div>
			<div class="action"><a href="#" class="action_remove">Remove</a></div>
			<div class="action"><a href="#" class="action_cancel">Cancel</a></div>
			<div class="clear"></div>
		</div>
	</div>

	<script type="text/javascript">
		(function () {
			j('.slip_item').click(function (e) { e.preventDefault(); });
			j('.slip_item .box_action').click(function (e) {
				e.preventDefault();
				e.stopPropagation();
				var inp = j(this).find('input');
				var c = inp.prop('checked');
				setTimeout(function () {
					inp.prop('checked', c);
				});
			});
			j('.slip_item .box_update').click(function () {
				var item = j(j(this).parent());
				var name = item.find('.box_name').html();
				var amount = parseInt(item.find('.box_bet_amount').html()) || 0;
				var idusersel = item.find('.box_action input').val();
				
				var newAmount = prompt('How many points do you want to bet on ' + name + ' ?', amount || '');
				if (newAmount == null) {
					return;
				}
				j.ajax('/fc/widget/index/name/fc_betting_slip?format=html', {
					data: { action: 'update', iduserselection: idusersel, amount: Math.max(parseInt(newAmount) || 0, 0) },
					dataType: 'html',
					success: function () { fc.user.updateBettingSlip(); fc.user.updateBettingUpcoming(); }
				});
			});

			var getSelection = function () {
				return j('.fc_betting_slip .slip_item .box_action input:checked');
			}

			j('.slip_actions a').click(function (e) {
				e.preventDefault();
				var action = j(this);
				if (action.hasClass('action_approve')) {
					if (!confirm('Do you want to approve this betting slip for this competition?')) {
						return;
					}
					alert('Not implemented');
				}
				if (action.hasClass('action_accumulate')) {
					var sel = getSelection();
					if (sel.length < 2) {
						alert('Select at least two bets to accumulate.');
						return;
					}
					if (!confirm('Do you want to accumulate these ' + sel.length + ' bets?')) {
						return;
					}
					alert('Not implemented');
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
					j.ajax('/fc/widget/index/name/fc_betting_slip?format=html', {
						data: { action: 'remove', iduserselection: sel.map(function() { return j(this).val() }).get() },
						dataType: 'html',
						success: function () { fc.user.updateBettingSlip(); fc.user.updateBettingUpcoming(); }
					});
				}
				if (action.hasClass('action_cancel')) {
					if (!confirm('Do you want to erase the entire betting slip? This action cannot be undone!')) {
						return;
					}
					j.ajax('/fc/widget/index/name/fc_betting_slip?format=html', {
						data: { action: 'cancel' },
						dataType: 'html',
						success: function (text) { fc.user.updateBettingSlip(); fc.user.updateBettingUpcoming(); }
					});
				}
			});
		})();
	</script>
	
<?php if (isset($_REQUEST['format']) && ($_REQUEST['format'] == 'html')) { ?>
</div>
<?php } ?>

