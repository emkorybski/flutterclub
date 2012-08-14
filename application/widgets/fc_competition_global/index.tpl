<?php if (empty($_REQUEST['format']) || ($_REQUEST['format'] != 'html')) { 
?>
<div class="box_shadow widget_body">
<?php } ?>
	
<style type="text/css">
	.clear{
		clear: both !important;
	}
	
	.your_psition{
		background: #FFFBE5 !important;
		font-weight: bold;
		border: 1px solid #FFEB70;
		padding: 5px 10px;
		margin:0px 0px 10px 0px;
	}
	
	.layout{
		background: #ffffff !important;
		display: block !important;
		padding: 10px;
	}
	
	.layout  span{
		float: left;
		margin: 0px 10px 0px 0px;
		padding: 10px;
	}
	
	.layout .pos {
		width: 10%;
	}
	
	.layout .pic {
		width: 30% !important;
	}
	
	.layout .dat {
		width: 50%;
	}
	
	
	.layout .big{
		font-weight: bold;
		font-size: 14px;
	}
	
	.layout .position {
		font-size: 28px;
		font-weight: normal
	}
	
	.layout .position_small {
		font-size: 22px;
		font-weight: normal
	}
	
	.layout .layout_first_place{
		border: 1px solid #ccc;
		background: #f0f0f0;
	}
	
	
	.layout .layout_podium{
		width: 49%;
		border: 1px solid #efefef;
		background: #f9f9f9;
		margin: 10px 0px 0px 0px !important;
	}
	
	.layout .left{
		float: left;
	}
	.layout .right{
		float: right;
	}
	
	.width100{
		width: 100px !important;
	}
	
	.line{
		border-top: 1px solid #ccc !important;
		margin: 10px 0px;
	}
	
</style>

<div class="layout">
		<?php if($this->position) {?>
		<div class="your_psition">Your current position in competition is <?=$this->position?></div>
		<?php
		}
		foreach($this->winners as $obj){
			if ($obj->position == 1){
		?>
			<div class="layout_first_place">
				<span class="big position pos"><?=$obj->position?></span>
				<span class="big pic"><?=$obj->userdata->name?></span>
				<span class="dat">
					Success rate: <?=$obj->successrate?>
					<br>
					Earnings: <?=$obj->earnings?>
				</span>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
		<?php
			}
			else
			if ($obj->position > 1 && $obj->position <= 3)
			{
				$class = ($obj->position == 2 ? 'left' : 'right');
		?>
				<div class="layout_podium <?=$class?>">
					<span class="big position pos"><?=$obj->position?></span>
					<span class="big pic"><?=$obj->userdata->name?></span>
					<span class="dat">
						Success rate: <?=$obj->successrate?>
						<br>
						Earnings: <?=$obj->earnings?>
					</span>
					<div class="clear"></div>
				</div>
		<?php
			}
			else
			{
				if ($obj->position == 4)
				{
				?>
					<div class="clear"></div>
					<div class="line"></div>
					<div class="layout_all">
						<span class="big right width100">Earnings</span>
						<span class="big right width100">Success rate</span>
						<div class="clear"></div>
					</div>
				<?
				}
		?>
			
			<div class="layout_all">
				<span class="position_small"><?=$obj->position?></span>
				<span class="pic"><?=$obj->userdata->name?></span>
				<span class="width100 right"><?=$obj->earnings?></span>
				<span class="width100 right"><?=$obj->successrate?></span>
				<div class="clear"></div>
			</div>
		
	<?php } }
 ?>
	<div class="clear"></div>
</div>	
<?php if (empty($_REQUEST['format']) || ($_REQUEST['format'] != 'html')) { ?>
</div>
<?php } ?>

