<?php if (empty($_REQUEST['format']) || ($_REQUEST['format'] != 'html')) { ?>
<div class="box_shadow widget_body">
<?php } ?>
	
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
	<?php
		global $indent;
		global $category;
		$indent = 0;
		foreach ($this->categories as $categ) {
			$category = $categ;
			require(dirname(__FILE__) . '/category.tpl');
		}
	?>
</div>

<script type="text/javascript">
	(function () {
	
	var j = jQuery;
		
	function loadCategories(hrefCategories, hrefSelections) {
		if (loadCategories.busy) {
			return;
		}
		loadCategories.busy += 2;
		var fillCategories = j('.layout_categories .widget_body');
		var fillSelections = j('.layout_upcoming .widget_body');
		fillCategories.css({opacity: 0.5}).addClass('betting_upcoming_loading');
		j.ajax(hrefCategories, {
			dataType: 'html',
			success: function (text) { fillCategories.html(text); },
			error: function () { alert('Internal error. Try again.'); },
			complete: function () { fillCategories.css({opacity: 1}).removeClass('betting_upcoming_loading'); --loadCategories.busy; }
		});
		fc.user.upcomingUrl = hrefSelections;
		fc.user.updateBettingUpcoming();
	}
	loadCategories.busy = 0;

	j('.betting_category').click(function (event) {
		event.preventDefault();
		loadCategories(this.href, j(this).find('input').val());
	});
	
	})();
</script>

	
<?php if (empty($_REQUEST['format']) || ($_REQUEST['format'] != 'html')) { ?>
</div>
<?php } ?>

