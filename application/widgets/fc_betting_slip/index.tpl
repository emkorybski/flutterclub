<style type="text/css">
	.layout_right {
		margin-right: 10px;
	}
	.layout_fc_betting_slip {
		box-shadow: none;
		border: 2px solid #CD4849;
		border-top: 0;
		margin-bottom: 1em;
	}
	.fc_betting_slip {
		background-color: #ffffff;
		text-align: right;
	}
		/* Position the accumulator input (probably temporary) */
	.fc_betting_slip .box_accumulator {
		margin-right: 20px;
		margin-top: 10px;
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
		width: 40%;
	}
	.slip_item .box_odds {
		width: 20%;
	}
	.slip_item .box_bet_stake {
		width: 30%;
	}
	.slip_item .box_bet_stake input {
		width: 90%;
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
		margin-left: 10px;
		margin-bottom: 10px;
		width: auto;
		text-align: center;
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
	<?php
	if ( count($this->betting_slip) ) :
		foreach ($this->betting_slip as $userSel) :
			$sel = $userSel->getSelection();
	?>
	<a href="#" class="slip_item">
		<div class="box box_name" title="<?=htmlentities($sel->name)?>"><?=htmlentities($sel->name)?></div>
		<div class="box box_odds"
			 title="<?=\bets\fc::formatOdds($sel->odds, 'decimal')?>"><?=\bets\fc::formatOdds($sel->odds)?>
		</div>
		<div class="box box_bet_stake"><input type="text" data-userselectionid="<?=$userSel->id?>"/></div>
		<div class="box box_action" title="Select several bets and an choose action from below"><input type="checkbox"
																									   value="<?=$userSel->id?>"/>
		</div>
		<div class="clear"></div>
	</a>
	<hr class="line"/>
	<?php
		endforeach;
		if ( count($this->betting_slip) > 1 ) :
	?>
	<input type="text" class="box_accumulator"/>
	<?php
		endif;
	?>
	<div class="slip_actions">
		<div class="action"><a href="#" class="action_remove_all">Remove all</a></div>
		<div class="action"><a href="#" class="action_place_bet">Place bet</a></div>
		<div class="action"><a href="#" class="action_remove_selected">Remove selected</a></div>
		<div class="clear"></div>
	</div>
	<?php
	else :
	?>
	<div href="#" class="slip_item">You don't have any bets in slip!</div>
	<?php
	endif;
	?>
</div>

<script type="text/javascript">
	(function () {
		j('.slip_item').live("click", function(evt){
			evt.preventDefault();
		});

		j('.slip_item .box_action').live("click", function(evt){
			evt.preventDefault();
			evt.stopPropagation();
			var inp = j(this).find('input');
			var c = inp.prop('checked');
			setTimeout(function () {
				inp.prop('checked', c);
			});
		});

		var getSelection = function () {
			return j('.fc_betting_slip .slip_item .box_action input:checked');
		}

		var getBets = function () {
			var bets = [];

			var betStake = j('.fc_betting_slip .slip_item .box_bet_stake input');
			betStake.each(function (index) {
				var stake = j(this).val();
				if (stake && !isNaN(stake)) {
					bets.push({
						user_selection_id:j(this).attr('data-userselectionid'),
						stake:stake
					})
				}
			});
			return bets;
		}

		j('.slip_actions a').live("click", function(evt){
			evt.preventDefault();
			var action = j(this);

			if (action.hasClass('action_place_bet')) {
				if (!confirm('Do you want to approve this betting slip for this competition?')) {
					return;
				}

				var bets = getBets();
				var accumulator = j('.fc_betting_slip .box_accumulator');
				var accStake = j(accumulator).val();
				if (accStake && !isNaN(accStake)) {
					bets.push({
						user_selection_id:'accumulator',
						stake:accStake
					})
				}

				if (bets.length <= 0) {
					alert('Please insert stakes!');
					return;
				}

				j.ajax(WEB_ROOT + 'widget?name=fc_betting_slip&format=html', {
					data:{ action:'place_bet', bets:bets },
					dataType:'html',
					success:function (text) {
						fc.user.updateAccountBalance();
						fc.user.updateBettingMarkets();
						fc.user.updateBettingSlip();
						fc.user.updateBettingPending();
						fc.user.updateBettingRecent();
					}
				});
			}

			if (action.hasClass('action_remove_selected')) {
				var sel = getSelection();
				if (sel.length < 1) {
					alert('Please select at least one bet.');
					return;
				}
				if (!confirm('Do you really want to remove the selected bets?')) {
					return;
				}

				var userSelectionIds = sel.map(function () {
					return j(this).val()
				}).get();
				j.ajax(WEB_ROOT + 'widget?name=fc_betting_slip&format=html', {
					data:{ action:'remove_selected', user_selection_ids:userSelectionIds },
					dataType:'html',
					success:function () {
						fc.user.updateAccountBalance();
						fc.user.updateBettingMarkets();
						fc.user.updateBettingSlip();
						fc.user.updateBettingPending();
						fc.user.updateBettingRecent();
					}
				});
			}

			if (action.hasClass('action_remove_all')) {
				if (!confirm('Are you sure you want to remove all the bets?')) {
					return;
				}

				j.ajax(WEB_ROOT + 'widget?name=fc_betting_slip&format=html', {
					data:{ action:'remove_all' },
					dataType:'html',
					success:function () {
						fc.user.updateAccountBalance();
						fc.user.updateBettingMarkets();
						fc.user.updateBettingSlip();
						fc.user.updateBettingPending();
						fc.user.updateBettingRecent();
					}
				});
			}
		});
	})();
</script>