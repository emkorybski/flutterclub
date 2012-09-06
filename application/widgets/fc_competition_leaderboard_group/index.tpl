<style type="text/css">
		/* styles go in here */
</style>

<div class="fc_competition_leaderboard">
	<?php
if ( count($this->leaderboardUsers) ) :
	?>
	<table>
		<tr>
			<th></th>
			<th>User</th>
			<th>FB$ Profit</th>
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
			<td><?=$user['profit']?></td>
			<td><span><?=$user['successRate']?></span> <span>(<?=$user['won_count']?>/<?=$user['bet_count']?>)</span></td>
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