<?php if (empty($_REQUEST['format']) || ($_REQUEST['format'] != 'html')) { ?>
<div class="fc_upcoming widget_body">
<?php } ?>
	
	<style type="text/css">
		.layout_middle {
			box-shadow: 5px 5px 15px 0 #cccccc;
		}
		
		.fc_upcoming {
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
			text-shadow: #dddddd 2px 1px 1px;
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
	</style>

	<div class="overlay"></div>
	<?php
		$user = bets\User::getCurrentUser();
		foreach ($this->sel as $sel) {
			$userSel = bets\UserSelection::getWhere(array('idselection=' => $sel->id, 'iduser=' => $user->id));
	?>
	<a href="/fc/widget/index/name/upcoming?format=html&idsport=<?=$this->idsport?>&idevent=<?=$this->idevent?>&vote_selection_id=<?=$sel->id?>" class="fc_selection">
		<input type="hidden" class="selection_id" value="<?=$userSel ? 0 : $sel->id?>" />
		<div class="selection_name"><?=$sel->name?></div>
		<div class="selection_odds"><?=round($sel->odds)?>:1</div>
		<div class="selection_bet">
			<?php
				if ($userSel) {
					if ($userSel->bet_amount) {
						echo 'In betting slip';
					}
					else {
						echo 'You bet ' . round($userSel->bet_amount) . ' points';
					}
				}
				else {
					echo '&nbsp;';
				}
			?>
		</div>
		<div class="selection_share">Share</div>
		<div class="clear"></div>
	</a>
	<hr class="line" />
	<?php } ?>

	<script type="text/javascript">
		(function () {
			j('.layout_upcoming h3').html('Competition: ' + <?=($this->comp ? json_encode($this->comp->name) . " + ' (ends on " . substr($this->comp->ts_end, 0, 10) . ")'" : "'None selected'")?>);
			j('.fc_selection').click(function (event) {
				event.preventDefault();
				var selection_id = parseInt(j(this).find('.selection_id').val());
				if (!selection_id) {
					alert('This selection is already on your betting slip.');
					return;
				}
				j.ajax('/fc/widget/index/name/upcoming?format=html', {
					data: { vote_selection_id: selection_id },
					success: function () { fc.user.updateBettingSlip(); fc.user.updateBettingUpcoming(); },
					error: function () { alert('Internal error, try again'); }
				});
			});
		})();
	</script>

<?php if (empty($_REQUEST['format']) || ($_REQUEST['format'] != 'html')) { ?>
</div>
<?php } ?>

