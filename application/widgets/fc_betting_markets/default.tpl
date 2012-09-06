<style type="text/css">
	.fc_betting_markets {
		background-color: #ffffff;
	}

	.fc_betting_markets .market_title {
		margin-left: 10px;
		padding-top: 5px;
		font-family: fc_pts;
	}

	.fc_betting_markets .market_type {
		margin-left: 10px;
		font-family: fc_pts;
	}

	.fc_betting_markets .market_header {
		font-size: 15px;
		line-height: 25px;
		font-weight: bold;
	}

	.fc_betting_markets .market_header .market_odds {
		margin-left: 466px;
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

	.fc_selection:hover {
		background-color: #e5e5e5;
	}

	.fc_betting_markets .selection_name {
		float: left;
		width: 40%;
		margin-left: 10px;
		margin-top: 10px;
	}

	.fc_betting_markets button {
		float: right;
		margin: 4px;
		width: 80px;
	}

	.fc_betting_markets button[disabled] {
		background-color: #eee;
		border-color: #aaa;
		color: #aaa;
		text-shadow: none;
		cursor: inherit;
	}

	.share_selection {
		float: right;
		margin-top: 10px;
		margin-right: 10px;
	}
</style>

<div class="fc_betting_markets">
<?php
foreach ( $this->upcomingEvents as $event) :
?>
	<p class="market_title">Sport: <?=$event->getSport()->name?></p>
	<p class="market_title">Event: <?=$event->getPath(true)?></p>
	<p class="market_title">Market: <?=$event->name?></p>
<?
	$eventSelections = $event->getSelections(3);
	foreach ( $eventSelections as $selection) :
		$userSelection = bets\UserSelection::getWhere(array('idselection=' => $selection->id, 'iduser=' => $this->user->id));
		$disabled = "";
		if ($userSelection) {
			$disabled = "disabled='disabled'";
		}
?>
	<div class="selection_name"><?=$selection->name?></div>
	<?php if ($selection->odds > 1) : ?>
	<button <?=$disabled?> data-idselection="<?=$selection->id?>" class="submit_selection"><?=\bets\fc::formatOdds($selection->odds)?></button>
	<?php else  : ?>
	<button disabled="disabled" data-idselection="<?=$selection->id?>">-</button>
	<?php endif; ?>
	<!-- <a href="#" class="share_selection">Share</a> -->
	<div class="clear"></div>
	</a>
	<hr class="line"/>
<?php
	endforeach;
?>
<?php
endforeach;
?>
</div>

<script type="text/javascript">
	j('.submit_selection').live("click", function (evt) {
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
	j('.share_selection').live("click", function (evt) {
		evt.preventDefault();
		alert('Share');
	});
</script>
