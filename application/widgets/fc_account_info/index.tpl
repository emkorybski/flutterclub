<?php
	if ( !empty($_REQUEST['balance']) ) {
		echo $this->userBalance->balance;
		die();
	}
?>

<?php if ($this->user) { ?>

<style type="text/css">
	#account_info {
		float: right;
		width: 300px;
		overflow: hidden;
		position: relative;
		top: 30px;
	}
	#account_info .account_info_left, #account_info .account_info_right, #account_info .account_info_user_points {
		float: left;
		font-family: fc_bebas;
		font-size: 14px;
		font-weight: normal;
		word-spacing: .4em;
		line-height: 28px;
	}
	#account_info .account_info_left {
		width: 55%;
		background-color: #000;
		color: #ffffff;
	}
	#account_info .account_info_right {
		width: 40%;
		background-color: #ffffff;
		color: #000;
		text-indent: 5px;
		text-overflow: ellipsis;
		overflow: hidden;
		white-space: nowrap;
	}

	.aim1 {
		border: 2px solid #000;
		border-top-left-radius: 10px;
		border-right: none;
		border-bottom: none;
		background-image: url(/application/themes/clean/images/chip_new.png);
		background-repeat: no-repeat;
		background-position: 5px center;
		text-indent: 30px;
	}
	.aim2 {
		border: 2px solid #000;
		border-top-right-radius: 10px;
		border-left: none;
		border-bottom: none;
	}
	.aim3 {
		border: 2px solid #000;
		border-bottom-left-radius: 10px;
		border-right: none;
		border-top: none;
		background-image: url(/application/themes/clean/images/clock.png);
		background-repeat: no-repeat;
		background-position: 6px center;
		text-indent: 30px;
	}
	.aim4 {
		border: 2px solid #000;
		border-bottom-right-radius: 10px;
		border-left: none;
		border-top: none;
	}
</style>

<div class="account_info" id="account_info">
	<div>
		<div class="account_info_left aim1">Account balance:</div>
		<div class="account_info_right aim2">
			<span class="account_info_user_points">FB$ <?=$this->userBalance->balance?></span>
		</div>
		<div class="account_info_left aim3">Competition ends:</div>
		<div class="account_info_right aim4"><?=$this->competitionCountdown?></div>
		<div class="clear"></div>
	</div>
</div>

<?php } ?>

