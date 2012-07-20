
<style type="text/css">
	.layout_middle {
		box-shadow: 5px 5px 15px 0 #cccccc;
	}
	.fc_betting_markets {
		background-color: #ffffff;
	}
	.fc_selection {
		display: block;
		font-family: fc_pts;
		padding: 7px 10px;
		text-decoration: none;
	}
	.fc_selection * {
		color: #5f93b4;
		font-weight: bold;
	}
	.fc_selection .selection_name {
		width: 55%;
		float: left;
	}
	.fc_selection .selection_odds {
		width: 14%;
		float: left;
	}
	.fc_selection .selection_bet {
		width: 30%;
		float: left;
	}
	.fc_selection:hover {
		background-color: #e5e5e5;
	}

	/* Added 20/07 after selection update */
	.fc_betting_markets .selection_name, .fc_betting_markets .selection_odds {
		float: left;
		margin-left: 10px;
		margin-top: 10px;
	}

	.fc_betting_markets .selection_name {
		width: 40%;
	}

	.fc_betting_markets button {
		float: right;
		margin: 4px;
	}

	.share_selection {
		float: right;
		margin-top: 10px;
		margin-right: 10px;
	}

	/* --- */

</style>

<div class="fc_betting_markets">
<?php
foreach ( $this->selections as $selection) :
	$userSelection = bets\UserSelection::getWhere(array('idselection=' => $selection->id, 'iduser=' => $this->user->id));
	$disabled = "";
	if ($userSelection) {
		$disabled = "disabled='disabled'";
	}
?>
	<div class="selection_name"><?=$selection->name?></div>
	<div class="selection_odds"><?=\bets\fc::formatOdds($selection->odds)?></div>
	<button <?=$disabled?> data-idselection="<?=$selection->id?>" class="submit_selection">Add to Betslip</button>
	<a href="#" class="share_selection">Share</a>
	<div class="clear"></div>
</a>
<hr class="line"/>
<?php
endforeach;
?>
</div>

<script type="text/javascript">
	j('.submit_selection').live("click", function(evt){
		evt.preventDefault();

		var idSelection = parseInt(j(this).attr('data-idselection'));
		j.ajax(WEB_ROOT + 'widget?name=fc_betting_markets&action=submitSelection&format=html', {
			data:{ 'idselection':idSelection },
			success:function () {
				fc.user.updateBettingSlip();
				fc.user.updateBettingMarkets();
			},
			error:function () {
				alert('Internal error, try again');
			}
		});
	});
	j('.share_selection').live("click", function(evt){
		evt.preventDefault();
		alert('Share');
	});
</script>
