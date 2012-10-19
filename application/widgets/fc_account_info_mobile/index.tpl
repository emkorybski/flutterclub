<?php
	if ( !empty($_REQUEST['balance']) ) {
		echo $this->userBalance->balance;
		die();
	}
?>

<?php if ($this->user) { ?>

<style type="text/css">

@import "~/application/custom/fonts/nunito/stylesheet.css";

	.mobile_account_info {
		width:70%; 
		margin-top:0px;
		margin-bottom:8px;
		margin-left:auto;
		margin-right:auto;
		background-color: #dbe2e3;
		border: 5px solid #000;
		color: #000;
		font-family: "nunito";
		padding: 8px 5px;
		border-radius: 5px;
		text-align:center;
		
		
		letter-spacing: 1px;
		font-size: 14px;
		line-height: 10px;
	}
	
	.mobile_account_info .balance {
		
		color:#aa0088;
		font-weight:bold;
	}
	
	.mobile_account_info .remaining{
		
		color:#aa0088;
		font-weight:bold;
	}
	
	.mobile_account_info .balance:before {
		content: 'FB$ ';
		
		font-weight:bold;
	}
	.mobile_account_info .balance:after {
		content: ' | ';
		
		font-weight:bold;
	}
</style>
</style>

<div class="mobile_account_info">
	<span class="balance"><?=$this->userBalance->balance?></span>
    <span class="remaining"><?=$this->competitionCountdown?></span>
</div>

<?php } ?>

