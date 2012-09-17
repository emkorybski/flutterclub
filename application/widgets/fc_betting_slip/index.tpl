<style type="text/css">
	.layout_fc_betting_slip {
		padding: 10px;
		background-color: #fff;
		margin-bottom: 10px;
	}

	.fc_betting_slip {
		text-align: right;
		background-color: #fff;
	}

	.fc_betting_slip table {
		width: 100%;
		margin-bottom: 10px;
	}

	.fc_betting_slip tr {
		background-color: #bfd8df;
	}

	.fc_betting_slip td {
		padding: 5px;
		border-bottom: 5px solid #fff;
	}

	.fc_betting_slip td.selection_stake input,
	.box_accumulator {
		width: 40px !important;
	}

	.fc_betting_slip .slip_actions {
		margin-top: 10px;
		position: relative;
	}

	.fc_betting_slip .action_remove_selection {
		cursor: pointer;
		overflow: hidden;
		background-image: url(/fc/custom/images/delete.png);
		background-repeat: no-repeat;
		background-position: 0px center;
		text-indent: -999px;
		width: 15px;
		padding: 0;
		padding-right: 5px;
	}

	.fc_betting_slip .action_remove_selection:hover {
		background-position: -20px center;
	}

	.fc_betting_slip .action_remove_all {
		float: left;
		position: absolute;
		bottom: 0;
	}

	.fc_betting_slip .action_place_bet {
		float: right;
	}
</style>

<div class="fc_betting_slip">
<?php
if ( count($this->betSlipSelections) ) :
?>
	<table>
	<?php
	foreach ($this->betSlipSelections as $userSel) :
		$sel = $userSel->getSelection();
	?>
		<tr>
			<td class="selection_name"><?=htmlentities($sel->name)?></td>
			<td class="selection_odds"><?=\bets\fc::formatOdds($sel->odds)?></td>
			<td class="selection_stake">FB$ <input type="text" data-userselectionid="<?=$userSel->id?>"/></td>
			<td class="action_remove_selection" data-userselectionid="<?=$userSel->id?>">X</td>
		</tr>
	<?php
	endforeach;
	?>
	</table>
	<?php
	if ( $this->accumulatorBetAvailable ) :
	?>
	<label for="accumulator">Accumulator</label>
	<input id="accumulator" type="text" class="box_accumulator"/>
	<?php
	else :
	?>
	<p>You cannot place accumulator on selections within same event or on the same selection more than once.</p>
	<?php
	endif;
	?>
	<div class="slip_actions">
		<a href="#" class="action_remove_all">Remove all</a>
		<button class="action_place_bet">Place Bet</button>
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
	var getBets = function () {
		var bets = [];

		var betStake = j('.fc_betting_slip table tr td.selection_stake input');
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

	j('.action_remove_selection').live("click", function (evt) {
		evt.preventDefault();

		if (!confirm('Do you really want to remove this selection?')) {
			return;
		}

		var userSelectionId = j(this).attr('data-userselectionid');
		j.ajax(WEB_ROOT + 'widget?name=fc_betting_slip&format=html', {
			data:{ action:'remove_selection', user_selection_id:userSelectionId },
			dataType:'html',
			success:function () {
				fc.user.updateBettingMarkets();
				fc.user.updateBettingSlip();
			}
		});
	});

	j('.action_remove_all').live("click", function (evt) {
		evt.preventDefault();

		if (!confirm('Are you sure you want to remove all the bets?')) {
			return;
		}

		j.ajax(WEB_ROOT + 'widget?name=fc_betting_slip&format=html', {
			data:{ action:'remove_all' },
			dataType:'html',
			success:function () {
				fc.user.updateBettingMarkets();
				fc.user.updateBettingSlip();
			}
		});
	});

	j('.action_place_bet').live("click", function (evt) {
		evt.preventDefault();

		var bets = getBets();

		var accumulator = j('.fc_betting_slip .box_accumulator');
		var accStake = j(accumulator).val();
		if (accStake && !isNaN(accStake)) {
			bets.push({
				user_selection_id:'accumulator',
				stake:accStake
			})
		}

		for (i = 0; i < bets.length; i++) {
			if ((accStake && accStake > 500) || (bets[i].stake && bets[i].stake > 500)) {
				alert('Maximum bet is FB$500!');
				return;
			}
		}

		if (bets.length <= 0) {
			alert('Please insert stakes!');
			return;
		}

		if (!confirm('Do you want to approve this betting slip for this competition?')) {
			return;
		}

		j.ajax(WEB_ROOT + 'widget?name=fc_betting_slip&format=html', {
			data:{ action:'place_bet', bets:bets },
			dataType:'json',
			success:function (response) {
				if (response.success) {
					fc.user.updateAccountBalance();
					fc.user.updateBettingMarkets();
					fc.user.updateBettingSlip();
					fc.user.updateBettingPending();
					fc.user.updateBettingRecent();
				}
				else {
					alert('Maximum bet is FB$500!');
				}
			}
		});
	});
</script>