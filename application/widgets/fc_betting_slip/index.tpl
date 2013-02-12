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

    .fc_betting_slip .slip_actions button {
        float: right;
        min-width: 80px;
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

    .fc_betting_slip .slip_actions a {
        float: left;
        position: absolute;
        bottom: 0;
    }

    .fc_betting_slip .action_place_bet {
        float: right;
    }

    .fc_betting_slip ul.form-errors li {
        height: auto !important;
        margin: 0px !important;
        width: 243px;
    }

    ul.form-errors > li > ul > li {
        font-size: 1em;
        font-weight: normal;
        width: 225px;
    }

</style>

<div class="fc_betting_slip">
  <div class="fc_betting_slip" id="fc_betting_slip_selections">
  <?php
  if ( count($this->betSlipSelections) ) :
  ?>
    <?php
    if ( $this->maxPayoutAlert ) :
    ?>
    <ul class="form-errors">
      <li>
        <ul class="errors">
          <li>The maximum payout per bet is <?=\bets\fc::formatOdds(30)?>.</li>
        </ul>
      </li>
    </ul>
    <?php
    endif;
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
    if ( count($this->betSlipSelections) > 1 && $this->accumulatorBetAvailable ) :
    ?>
    <label for="accumulator">Accumulator</label>
    <input id="accumulator" type="text" class="box_accumulator"/>
    <?php
    elseif ( !$this->accumulatorBetAvailable ) :
    ?>
    <p>You cannot place accumulator on selections within same event or on the same selection more than once.</p>
    <?php
    endif;
    ?>
    <ul id="fc_betting_slip_errors" class="form-errors" style="display: none">
      <li><ul class="errors"><li id="fc_betting_slip_error_msg"></li></ul></li>
    </ul>
    <div class="slip_actions">
      <a href="#" class="action_remove_all">Remove all</a>
      <button class="action_place_bet">Place Bet</button>
      <div class="clear"></div>
    </div>
  <?php
  else :
  ?>
    <p href="#" class="slip_item">You don't have any bets in slip!</p>
  <?php
  endif;
  ?>
  </div>
  <div class="fc_betting_slip" id="fc_betting_slip_confirm" style="display: none">
    <p id="fc_betting_slip_confirm_msg"></p>
    <div class="slip_actions">
      <a href="#" class="action_cancel_place_bet">Cancel</a>
      <button class="action_confirm_place_bet">Yes</button>
      <div class="clear"></div>
    </div>
  </div>
  <div class="fc_betting_slip" id="fc_betting_slip_bet_placed" style="display: none">
    <p>Your bet has been placed, good luck!</p>
    <div class="slip_actions">
      <button class="action_return_to_betting_slip">OK</button>
      <div class="clear"></div>
    </div>
  </div>
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

  var showBetSlip = function(msg) {
    j('#fc_betting_slip_confirm').hide();
    j('#fc_betting_slip_bet_placed').hide();
    j('#fc_betting_slip_selections').show();
  }

  var showConfirm = function(msg) {
    j("#fc_betting_slip_confirm_msg").html(msg);

    j('#fc_betting_slip_selections').hide();
    j('#fc_betting_slip_bet_placed').hide();
    j('#fc_betting_slip_confirm').show();
  }

  var showBetPlaced = function() {
    j('#fc_betting_slip_selections').hide();
    j('#fc_betting_slip_confirm').hide();
    j('#fc_betting_slip_bet_placed').show();
  }

  var showError = function(msg) {
    if (msg) {
      j("#fc_betting_slip_error_msg").html(msg);
      j("#fc_betting_slip_errors").show();
      showBetSlip();
    }
    else {
      j("#fc_betting_slip_error_msg").html('');
      j("#fc_betting_slip_errors").hide();
    }
  }

  var validateAndGetBets = function() {

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
        showError('Maximum bet is FB$500!');
        return null;
      }
    }

    if (bets.length <= 0) {
      showError('Please insert stakes!');
      return null;
    }

    return bets;
  }

	j('.action_place_bet').live("click", function (evt) {
		evt.preventDefault();

    var bets = validateAndGetBets();
    if (!bets) {
      return;
    }

		j.ajax(WEB_ROOT + 'widget?name=fc_betting_slip&format=html', {
			data:{ action:'validate_bet', bets:bets },
			dataType:'json',
			success:function (response) {
				if (response.result == 'invalid_selection_timestamp') {
          showError('Invalid selections timestamp. Betting slip will be refreshed');
					fc.user.updateBettingSlip();
				}
				else if (response.result == 'balance_exceeded') {
          showError("Unfortunately you don't have enough FB$ to place this bet.");
				}
				else if (response.result == 'max_stake_exceeded') {
          showError('Maximum bet is FB$500!');
				}
				else {
          showError('');
          showConfirm(response.result);
				}
			}
		});
	});

  j('.action_cancel_place_bet').live("click", function (evt) {
    evt.preventDefault();

    showBetSlip();
  });

  j('.action_confirm_place_bet').live("click", function (evt) {
    evt.preventDefault();

    var bets = validateAndGetBets();
    if (!bets) {
      return;
    }

    j.ajax(WEB_ROOT + 'widget?name=fc_betting_slip&format=html', {
      data:{ action:'place_bet', bets:bets },
      dataType:'json',
      success:function (response) {
        if (response.result == 'invalid_selection_timestamp') {
          showError('Invalid selections timestamp. Betting slip will be refreshed');
          fc.user.updateBettingSlip();
        }
        else if (response.result == 'balance_exceeded') {
          showError("Unfortunately you don't have enough FB$ to place this bet.");
        }
        else if (response.result == 'max_stake_exceeded') {
          showError('Maximum bet is FB$500!');
        }
        else if (response.result == 'success') {
          showBetPlaced();
          fc.user.updateAccountBalance();
          fc.user.updateBettingMarkets();
          //fc.user.updateBettingSlip();
          fc.user.updateBettingPending();
          fc.user.updateBettingRecent();
        }
        else {
          showError('An unknown error has occurred');
        }
      }
    });
  });

  j('.action_return_to_betting_slip').live("click", function (evt) {
    evt.preventDefault();

    fc.user.updateBettingSlip();
  });

</script>