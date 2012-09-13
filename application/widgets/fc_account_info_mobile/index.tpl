<?php
	if ( !empty($_REQUEST['balance']) ) {
		echo $this->userBalance->balance;
		die();
	}
?>

<?php if ($this->user) { ?>

<style type="text/css">
	.mobile_account_info {
		float: right; 
		background-color: #70B8C7;
		border: 2px solid #39abcd;
		color: #fff;
		font-family: fc_bebas;
		padding: 3px;
		border-radius: 5px;
		position: absolute;
		right: 5px;
		top: 10px;
		letter-spacing: 1px;
		font-size: 14px;
		line-height: 14px;
	}
	.mobile_account_info .balance:before {
		content: 'FB$ ';
	}
	.mobile_account_info .balance:after {
		content: ' | ';
	}
</style>

<div class="mobile_account_info">
	<span class="balance"><?=$this->userBalance->balance?></span>
    <span class="remaining"><?=$this->competitionCountdown?></span>
</div>

<?php } ?>

