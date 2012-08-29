<style type="text/css">
	.fc_competition_leaderboard td {
		vertical-align: bottom;
	}
	.fc_competition_leaderboard table {
		width: 100%;
	}

	.fc_competition_leaderboard th, .fc_competition_leaderboard td {
		padding: 10px;
	}

	.fc_competition_leaderboard td {
		border-top: 1px solid #eaeaea;
	}

	.fc_competition_leaderboard td.leaderboard_username {
		font-weight: bold;
	}

	.fc_competition_leaderboard td.leaderboard_username img {
		margin-right: 5px
	}

	.fc_competition_leaderboard td:first-child {
		max-width: 25px;
		font-size: 30px;
		text-align: right;
		font-family: fc_bebas;
		vertical-align: middle;
		border: 0;
	}
</style>

<div class="fc_competition_leaderboard">
<?php
if ( count($this->leaderboardUsers) ) :
?>
	<table>
		<tr>
			<th></th>
			<th>User</th>
			<th>Profit</th>
			<th>Success Rate</th>
		</tr>
		<?php
	foreach($this->leaderboardUsers as $user) :
		$seUser = $user['user'];
	?>
		<tr>
			<td><?=$user['position']?></td>
			<td class="leaderboard_username">
				<?=$this->htmlLink($seUser->getHref(), $this->itemPhoto($seUser, 'thumb.icon'))?>
				<?=$this->htmlLink($seUser->getHref(), $seUser->getTitle())?>
			</td>
			<td>FB$ <?=$user['profit']?></td>
			<td><?=$user['successRate']?> (<?=$user['won_count']?>/<?=$user['bet_count']?>)</td>
		</tr>
		<?php
	endforeach;
	?>
	</table>
<?php
else :
?>
	No data just yet.
<?php
endif;
?>
</div>