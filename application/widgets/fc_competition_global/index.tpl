<?php if (empty($_REQUEST['format']) || ($_REQUEST['format'] != 'html')) { ?>
<div class="box_shadow widget_body">
<?php } ?>
	
<style type="text/css">
	.layout_fc_betting_categories > div {
		text-align: left;
	}

	.layout_fc_betting_categories h3 {
		background-color: #ff6600;
		color: white;
		border-radius: 0;
		border-top-left-radius: 5px;
		border-top-right-radius: 5px;
	}

	.betting_category {
		cursor: pointer;
		padding: 5px;
	}
	.betting_category:hover {
		background-color: #e5e5e5;
	}
	.betting_categories {
		background-color: #ffffff;
	}

	.betting_category {
		font-family: fc_pts;
		font-weight: bold;
	}
	.betting_category {
		color: #0291d5;
	}
</style>

<div class="betting_categories">
	<pre>
	<?php
		print_r($this->winners);
		
	?>
	</pre>
</div>


	
<?php if (empty($_REQUEST['format']) || ($_REQUEST['format'] != 'html')) { ?>
</div>
<?php } ?>

