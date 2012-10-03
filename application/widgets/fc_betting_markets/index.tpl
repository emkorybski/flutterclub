
<style type="text/css">
	.fc_betting_markets {
		overflow: auto;
	}
	.fc_betting_market h1.market_title {
		background-color: #cd4849;
		color: #fff;
		margin-left: 0;
		padding: 5px 10px;
		font-family: fc_pts;
		-webkit-box-shadow: 0px 5px 0px rgba(50, 50, 50, 0.5);
		-moz-box-shadow: 0px 5px 0px rgba(50, 50, 50, 0.5);
		box-shadow: 0px 3px 2px rgba(50, 50, 50, 0.5);
		position: relative;
	}
	.fc_betting_market .market_type {
		margin-left: 0;
		padding-left: 10px;
		padding-top: 5px;
		padding-bottom: 0;
		font-size: 22px;
		font-family: fc_pts;
	}
	.fc_betting_market .market_header {
		font-size: 14px;
		line-height: 25px;
		font-weight: bold;
		overflow: auto;
	}
	.fc_betting_market .selections {
		background-color: #fff;
		margin: 10px;
		margin-bottom: 0;
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
	.fc_betting_market hr.line + .market_title {
    	margin-top: 50px;
	}
	.fc_betting_market {
		background-color: #bfd8df;
		margin-bottom: 10px;
		padding-bottom: 10px;
		overflow: auto;
	}

	.fc_betting_market h1.market_title {
		background-color: #cd4849;
		color: #fff;
		margin-left: 0;
		padding: 5px 10px;
		margin-bottom: 10px;
	}

	.fc_betting_market p.market_title {
		font-size: 16px;
		font-weight: bold;
		margin-left: 0;
		padding: 5px 10px;
	}

	.fc_betting_market .link_to_market {
		float: right;
		margin: 10px;
		font-weight: bold;
		color: #5F93B4;
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
		background-color: #fff;
		vertical-align: top;
		padding-left: 5px;
		padding-top: 2px;
	}
</style>

<div class="fc_betting_markets">
	<div class="fc_betting_market">
	<?php
	if (count($this->selections) > 0) :
	?>
		<h1 class="market_title"><?=$this->parentEvent->name?></h1>
		<h2 class="market_type"><?=$this->event->name?></h2>
		<div class="market_header">
			<span class="market_odds">Odds</span>
		</div>
		<div class="selections">
		<?php
		foreach ( $this->selections as $selection) :
			$userSelection = bets\UserSelection::getWhere(array(
				'idselection=' => $selection->id,
				'iduser=' => $this->user->id));
			$disabled = "";
			if ($userSelection) {
			$disabled = "disabled='disabled'";
			}
			$goToMarketUrl = WEB_HOST . WEB_ROOT;
			$goToMarketUrl .= \bets\fc::isMobileVersion() ? "pages/mbetting" : "pages/betting";
			$goToMarketUrl .= "?event=" .$this->event->id;
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
		<!-- <a class="link_to_betfair" href="http://sports.betfair.com/?mi=<?=$this->event->betfairMarketId?>&ex=1" target="_blank" title="Bet for real with Betfair">Bet for real with <img src="/fc/custom/images/betfair.jpg" alt="Betfair"></a> -->
		<a class="link_to_market" href="<?=$goToMarketUrl?>">Market ID: <?=$this->event->id?></a>
	<?php
	endif;
	?>
	</div>
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
				j('.action_place_bet')[0].scrollIntoView(false);
			}
		});
	});
</script>
