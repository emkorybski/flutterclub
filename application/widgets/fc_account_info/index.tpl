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
		top: 80px;
	}
	#account_info .account_info_left, #account_info .account_info_right, #account_info .account_info_user_points {
		float: left;
		font-family: fc_bebas;
		font-size: 14px;
		font-weight: normal;
		word-spacing: .4em;
		text-indent: 5px;
		line-height: 22px;
	}
	#account_info .account_info_left {
		width: 55%;
		background-color: #70b8c7;
		color: #ffffff;
	}
	#account_info .account_info_right {
		width: 40%;
		background-color: #ffffff;
		color: #666666;
	}

	.aim1 {
		border: 2px solid #39abcd;
		border-top-left-radius: 10px;
		border-right: none;
		border-bottom: none;
	}
	.aim2 {
		border: 2px solid #39abcd;
		border-top-right-radius: 10px;
		border-left: none;
		border-bottom: none;
	}
	.aim3 {
		border: 2px solid #39abcd;
		border-bottom-left-radius: 10px;
		border-right: none;
		border-top: none;
	}
	.aim4 {
		border: 2px solid #39abcd;
		border-bottom-right-radius: 10px;
		border-left: none;
		border-top: none;
	}
</style>

<div class="account_info" id="account_info">
	<div>
		<div class="account_info_left aim1">Account balance:</div>
		<div class="account_info_right aim2">
			<span class="account_info_user_points"><?=$this->userBalance->balance?></span> points
		</div>
		<div class="account_info_left aim3">Competition ends:</div>
		<div class="account_info_right aim4">9 days 7 hrs</div>
		<div class="clear"></div>
	</div>
</div>

<?php } ?>

