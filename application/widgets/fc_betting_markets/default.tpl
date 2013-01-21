<style type="text/css">

	.fc_betting_market .market_title {
		font-family: fc_pts;
	}

	.fc_betting_market .market_type {
		margin-left: 10px;
		font-family: fc_pts;
		color:#333;
	}

	.fc_betting_market .market_header {
		font-size: 15px;
		line-height: 25px;
		font-weight: bold;
		overflow: auto;
	}

	.fc_betting_market .market_header .market_odds {
		float: right;
		margin-right: 35px;
	}

	.fc_selection {
		display: block;
		font-family: fc_pts;
		padding: 7px 10px;
		text-decoration: none;
	}

	.fc_selection * {
		color: #990000;
		font-weight: bold;
	}

	.fc_selection .selection_name {
		width: 55%;
		float: left;
	}

	.fc_selection:hover {
		background-color: #e5e5e5;
	}

	.fc_betting_market .selection_name {
		float: left;
		width: 40%;
		margin-left: 10px;
		margin-top: 10px;
	}

	.fc_betting_market button {
		float: right;
		margin: 4px;
		width: 80px;
	}

	.fc_betting_market button[disabled] {
		background-color: #eee;
		border-color: #aaa;
		color: #aaa;
		text-shadow: none;
		cursor: inherit;
	}

	.share_selection {
		float: right;
		margin-top: 12px;
		margin-right: 10px;
	}
	.fc_betting_markets hr.line + .market_title {
		margin-top: 50px;
	}

	.fc_betting_market {
		background-color: #dbe2e3;
		margin-bottom: 10px;
		overflow: auto;
		padding-bottom: 10px;
	}

	.fc_betting_market h1.market_title {
		background-color: #990000;
		color: #fff;
		margin-left: 0;
		padding: 5px 10px;
		margin-bottom: 10px;
		-webkit-box-shadow: 0px 5px 0px rgba(50, 50, 50, 0.5);
		-moz-box-shadow: 0px 5px 0px rgba(50, 50, 50, 0.5);
		box-shadow: 0px 3px 2px rgba(50, 50, 50, 0.5);
		position: relative;
	}

	.fc_betting_market p.market_title {
		font-size: 16px;
		margin-left: 0;
		padding: 5px 10px;
	}

	.fc_betting_market .link_to_market {
		float: right;
		margin: 10px;
		font-weight: bold;
		color: #990000;
		margin-bottom: 0;
	}

	.fc_betting_market .link_to_betfair {
		float: left;
		margin: 10px;
		font-weight: bold;
		color: #000;
		margin-bottom: 0;
		font-style: italic;
		padding-right: 5px;
		background-color: transparent;
		vertical-align: top;
		padding-left: 5px;
		padding-top: 2px;
	}

	.fc_betting_market .selections {
		background-color: #fff;
		margin: 10px;
		margin-bottom: 0;
	}
	
	.form_guide{
	           display:block;line-height:25px;margin-top:8px;margin-left:20px;height:35px;float:left;font-weight:bold}
		   
	.bet4real{color:#990000;margin-left:20px;font-weight:bold}	   

</style>

<div class="fc_betting_markets upcoming_events">
<?php
foreach ( $this->upcomingEvents as $event) :
?>
	<div class="fc_betting_market">
		<h1 class="market_title"><?=$event->getSport()->name?></h1>
		<p class="market_title"><?=$event->getPath(true)?></p>
		<p class="market_title"><?=$event->name?></p>
		<div class="selections">
		<?php
		$eventSelections = $event->getSelections(3);
		foreach ( $eventSelections as $selection) :
			$userSelection = bets\UserSelection::getWhere(array(
				'idselection=' => $selection->id,
				'iduser=' => $this->user->id));
			$disabled = "";
			if ($userSelection) {
				$disabled = "disabled='disabled'";
			}
			$goToMarketUrl = WEB_HOST . WEB_ROOT;
			$goToMarketUrl .= \bets\fc::isMobileVersion() ? "pages/mbetting" : "pages/betting";
			$goToMarketUrl .= "?event=" .$event->id;
		?>
			<div class="selection_name"><?=$selection->name?></div>
			<?php if ($selection->odds > 1) : ?>
				<button <?=$disabled?> data-idselection="<?=$selection->id?>" class="submit_selection"><?=\bets\fc::formatOdds($selection->odds)?></button>
			<?php else  : ?>
				<button disabled="disabled" data-idselection="<?=$selection->id?>">-</button>
			<?php endif; ?>
			<a href="/fc/widget?name=fc_betting_share&format=html&id=<?=$selection->id?>" class="share_selection smoothbox">Share</a>
			<div class="clear"></div>
			<hr class="line"/>
		<?php
		endforeach;
		?>
		</div>
		
		<!--<iframe class="link_to_betfair" allowtransparency="true" src="http://ads.betfair.com/ad.aspx?bid=4839&pid=75008" width="93" height="41" marginwidth="0" marginheight="0" hspace="0" vspace="0" frameborder="0" scrolling="no"></iframe>-->
		<p class="bet4real">
		Bet for real with:
		</p>
		<iframe class="link_to_betfair" allowtransparency="true" src="http://ads.betfair.com/ad.aspx?bid=2781&pid=75008" width="120" height="60" marginwidth="0" marginheight="0" hspace="0" vspace="0" frameborder="0" scrolling="no">
		
		</iframe>
		
		<?php if($event->getSport()->name == 'Horse Racing') : ?>
		<a class="form_guide" href="http://www.attheraces.com/index.aspx?ref=splash" target="_blank" title="Betfair">
		
		Racing form guide
		</a>
		<?php endif; ?>
		
		<!--
		<a class="link_to_betfair" href="http://sports.betfair.com/?mi=<?=$event->betfairMarketId?>&ex=1" target="_blank" title="Betfair">
		<img src="/fc/custom/images/betfair.png" alt="Betfair">
		See live on Betfair
		</a> -->
		<a class="link_to_market" href="<?=$goToMarketUrl?>">Go to market</a>
	</div>
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
			},
			complete:function () {
        if (j('.action_place_bet')[0]) {
				  j('.action_place_bet')[0].scrollIntoView(false);
        }
			}
		});
	});
</script>
