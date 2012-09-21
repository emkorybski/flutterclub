<style type="text/css">

	.fc_betting_market .market_title {
		font-family: fc_pts;
	}

	.fc_betting_market .market_type {
		margin-left: 10px;
		font-family: fc_pts;
	}

	.fc_betting_market .market_header {
		font-size: 15px;
		line-height: 25px;
		font-weight: bold;
	}

	.fc_betting_market .market_header .market_odds {
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
		background-color: #bfd8df;
		margin-bottom: 10px;
		overflow: auto;
		padding-bottom: 10px;
	}

	.fc_betting_market h1.market_title {
		background-color: #cd4849;
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
		color: #5F93B4;
		margin-bottom: 0;
	}

	.fc_betting_market .selections {
		background-color: #fff;
		margin: 10px;
		margin-bottom: 0;
	}

</style>

<div class="fc_betting_markets">
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
		<a class="link_to_market" href="<?=WEB_HOST.WEB_ROOT?>pages/betting?event=<?=$event->id?>">Go to market</a>
		<a href="<?=WEB_HOST . WEB_ROOT?>pages/betting?event=<?=$event->id?>">market url</a>
		<p>ID: <?=$event->id?></p>
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
			}
		});
	});
</script>
