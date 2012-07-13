<?php global $event; ?>

<link rel="stylesheet" type="text/css" href="/fc/application/css.php?request=application/themes/clean/theme.css&c=1" />

<style type="text/css">
	
	html { overflow: auto; }
	body { margin: 0; }
	.clear { clear: both; }
	#main { height: 500px; overflow: auto; padding: 0 10px; position: relative; top: 10px }

	@font-face {
		font-family: fc_bebas;
		src: url('/fc/custom/fonts/bebas.ttf');
	}

	@font-face {
		font-family: fc_pts;
		src: url('/fc/custom/fonts/pts75f.ttf');
	}

	.event .title {
		font-family: fc_bebas;
		word-spacing: 0.4em;
		background-color: #3aaacf;
		color: white;
		font-size: 10pt;
		padding: .5em;
		border-radius: 5px;
		margin: 0 10px;
		text-align: center;
	}
	
	.subevent {
		font-size: 13px;
		font-family: fc_pts;
		margin: 10px 0;
	}
	
	.subevent .title {
		font-family: fc_bebas;
		word-spacing: 0.4em;
		border-top-left-radius: 5px;
		border-top-right-radius: 5px;
		background-color: #cd4849;
		color: #ffffff;
		padding: .4em .7em;
	}
	
	.subevent .selections {
		border: 1px solid #cccccc;
		border-bottom-left-radius: 5px;
		border-bottom-right-radius: 5px;
	}

	.subevent .selection {
		border-top: 1px solid #cccccc;
		padding: .5em;
	}
	.subevent .selection:first-child {
		border-top: none;
	}
	
	.selection .selection_name {
		width: 55%;
		float: left;
	}
	.selection .selection_odds {
		width: 14%;
		float: left;
	}
	.selection .selection_bet {
		width: 30%;
		float: left;
	}
		
</style>

<div id="main">

<div class="event">
	<div class="title">
		<?=$event->name?><br />
		(<?=$event->ts?>)
	</div>
</div>

<?php foreach ($event->getChildEvents() as $subevent) { ?>
<div class="subevent">
	<div class="title"><?=htmlentities($subevent->name)?> (<?=$subevent->ts?>)</div>
	<div class="selections">
		<?php foreach ($subevent->getChildSelections() as $sel) { ?>
		<div class="selection">
			<input class="selection_id" type="hidden" value="<?=$sel->id?>" />
			<div class="selection_name"><?=$sel->name?></div>
			<div class="selection_odds" title="<?=\bets\fc::formatOdds($sel->odds, 'decimal')?>"><?=\bets\fc::formatOdds($sel->odds)?></div>
			<div class="selection_bet">
				<?php
					$userSel = bets\UserSelection::getWhere(array('idselection=' => $sel->id, 'iduser=' => $user->id));
					if ($userSel) {
				?>
					placed <?=round($userSel->bet_amount)?> points
				<?php } else { ?>
					<a class="place_bet" href="#">Place bet</a>
				<?php } ?>
			</div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
		<?php } ?>
	</div>
</div>
<?php } ?>

<br />

<div style="text-align: center">
	<button onclick="parent.Smoothbox.close();">Close</button>
</div>

</div>

<script type="text/javascript" src="/fc/custom/js/jquery.js"></script>

<script type="text/javascript">
	(function () {
		var j = jQuery;
		j('.place_bet').click(function (e) {
			e.stopPropagation();
			e.preventDefault();
			var sel = j(this).parents('.selection');
			var amount = parseInt(prompt('How many points do you want to bet on ' + sel.find('.selection_name').html() + '?'));
			if (!amount) {
				return;
			}
			if (amount < 1) {
				alert('You must bet at least 1 point');
				return;
			}
			j('#main').css({opacity: 0.5});
			j.ajax('/fc/widget/index/name/fc_betting_markets', {
				type: 'get',
				data: {
					format: 'html',
					vote_selection_id: parseInt(sel.find('.selection_id').val()),
					vote_amount: amount
				},
				success: function (text) {
					if (!text.length) {
						alert('Internal error');
						return;
					}
					fc.user.updateAccountBalance(text);
					parent.location.reload();
				},
				failure: function () {
					alert('Internal error');
				},
				complete: function () { j('#main').css({opacity: 1}); }
			});
		});
	})();
</script>

