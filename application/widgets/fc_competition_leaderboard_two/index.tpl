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
	
	.fc_competition_leaderboard p{
		margin-left:10px;color:#333;background:#e6e6e6;padding:4px
	}
	
	.fc_competition_leaderboard p > span{
		color:#990000;
	}
	
</style>

<div class="fc_competition_leaderboard">


<?php
if ( count($this->leaderboardUsers) > 0 ) :
?>

<p><span>REMEMBER!</span> You need to have at least 1 bet settled to appear on the Leaderboard.</p>
	
			
		<?php
		
		$curUserId = Engine_Api::_()->user()->getViewer()->getIdentity();
		$curUserName = Engine_Api::_()->user()->getViewer();
		$getCurFcUser = $this->getCurFcUser;
		//echo $getCurFcUser[0]['id'];
		
		
		?>
		</tr>
		<?php
	foreach($this->leaderboardUsers as $user) :
	
	
	
	$seUser = $user['user'];
	$fcUser = $user['fcuser']->id;
	//echo $fcUser;
	//$user_pos = $user['profit'];
		
	?>

	
<?php
	if($seUser == $curUserName) :
?>
	
	<table>
		<tr>
			<th></th>
			<th>User</th>
			<th>FB$ Profit</th>
			<th>Success Rate</th>
		<tr>
		
			<td><?=$user['position']?></td>
			<td class="leaderboard_username">
				<?=$this->htmlLink($seUser->getHref(), $this->itemPhoto($seUser, 'thumb.icon'))?>
				<?=$this->htmlLink($seUser->getHref(), $seUser->getTitle())?>
			</td>
			
	<td>
               
	<?php
		if( count($this->blackjackUsers) > 0) :
	?>
	<?php 
                                
		foreach($this->blackjackUsers as $b_user) :
		echo $b_user['blackjack_profit'];
	?>	
	
		<?php
			if($b_user['user'] == $getCurFcUser[0]['id']) :
                                    
			$total_profit = floatval($user['profit']) + floatval($b_user['blackjack_profit']);   
                ?>
		
<?=$total_profit?>
                                  
<?php
	endif;
?>

<?php

                   endforeach;
?>
	<?php
             else :	
	?>	
	        <?=$user['profit']?>	
<?php
       endif;
?>       
</td>
	
			
			<!-- <td><?=$user['profit']?></td> -->
			<td><span><?=$user['successRate']?></span> <span>(<?=$user['won_count']?>/<?=$user['bet_count']?>)</span></td>
		</tr>
		
		
		
		<?php
		endif;
		?>
		
		
		
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