<style type="text/css">
		/* styles go in here */
</style>

<div class="fc_competition_leaderboard">
	<?php
if ( count($this->leaderboardUsers) ) :
	?>
	<table style="border-collapse: separate;" cellspacing="15">
		<tr>
			<th>Rank</th>
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
			<td>
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