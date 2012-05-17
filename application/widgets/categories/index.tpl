<style type="text/css">
	.layout_categories > div {
		text-align: left;
	}

	.layout_categories h3 {
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
	<div class="betting_category">
		<input type="hidden" value="0" />
		All
	</div>
	<hr style="border-bottom: 1px solid #cccccc" />
	<?php foreach ($this->categories as $sport) { ?>
	<div class="betting_category">
		<input type="hidden" value="<?=$sport->id?>" />
		<?=$sport->name?>
	</div>
	<hr style="border-bottom: 1px solid #cccccc" />
	<?php } ?>
</div>

<script type="text/javascript">
	function loadCateg(idCateg) {
		var fill = jQuery('.layout_upcoming')[0];
		jQuery('.betting_upcoming').css({opacity: 0.5}).addClass('betting_upcoming_loading');
		jQuery.ajax('/fc/widget/index/name/upcoming?format=html&idsport=' + encodeURIComponent(idCateg), {
			success: function (text) {
				jQuery(fill).html(text);
			},
			complete: function (text) {
				jQuery('.betting_upcoming').css({opacity: 1}).removeClass('betting_upcoming_loading');
			},
			dataType: 'html'
		});
	}
	jQuery('.betting_category').each(function (nr, categ) {
		jQuery(categ).click(function () {
			loadCateg(jQuery(categ).find('input')[0].value);
		});
	});
</script>

