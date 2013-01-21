<style type="text/css">
      .layout_user_pos{
	background:#fff;
      }
      
      .pos_fields{
           margin:10px
      }
</style>

<div class="layout_user_pos">
	<div class="pos_fields">
		<h4><span>Current competition</span></h4>
		
		<?php echo $this->test; ?>
		<?php echo $this->test2; ?>
		
		<ul>
			<li>
				<span>Leaderboard position</span>
				<span><?=$this->ranking?></span>
			</li>
			<li>
				<span>Number of bets placed</span>
				<span><?=$this->num_bets?></span>
			</li>
			<li>
				<span>Winning bets</span>
				<span><?=$this->num_winning_bets?></span>
			</li>
			<li>
				<span>Losing bets</span>
				<span><?=$this->num_losing_bets?></span>
			</li>
			<li>
				<span>Success rate (%)</span>
				<span><?=$this->success_rate?></span>
			</li>
			<li>
				<span>Profit earned</span>
				<span><?=$this->profit?></span>
			</li>
		</ul>
	</div>
</div>