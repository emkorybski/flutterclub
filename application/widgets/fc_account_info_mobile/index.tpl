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
		padding: 5px;
		border-radius: 5px;
		position: absolute;
		right: 5px;
		top: 10px;
		letter-spacing: 2px;
		font-size: 10px;
	}
</style>

<div class="mobile_account_info">
	<span class="balance"><?=$this->userBalance->balance?></span> | 
    <span class="remaining"><?=$this->competitionCountdown?></span>
</div>

<?php } ?>

