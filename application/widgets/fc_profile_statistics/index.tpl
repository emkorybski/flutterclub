<div class="layout_user_profile_statistics">
	<div class="profile_fields">
		<h4><span>Current competition</span></h4>
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