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

	.fc_betting_category {
		cursor: pointer;
		padding: 5px;
	}

	.fc_betting_category:hover {
		background-color: #e5e5e5;
	}

	.fc_betting_categories {
		background-color: #ffffff;
	}

	.fc_betting_category {
		font-family: fc_pts;
		font-weight: bold;
	}

	.fc_betting_category {
		color: #0291d5;
	}
</style>

<div class="fc_betting_categories">
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
	j('.fc_betting_category').live('click', function (event) {
		event.preventDefault();

		var idSport = j(this).attr('attr-idsport');
		var idEvent = j(this).attr('attr-idevent');

		var bettingCategories = j('.fc_betting_categories');
		bettingCategories.css({opacity: 0.5}).addClass('betting_upcoming_loading');

		j.ajax(WEB_ROOT + 'widget?name=fc_betting_categories&format=html', {
			data:{ "idsport":idSport, "idevent":idEvent },
			dataType:'html',
			success:function (text) {
				var content = j('.fc_betting_categories', text);
				bettingCategories.html(content)
			},
			complete:function () {
				bettingCategories.css({opacity: 1}).removeClass('betting_upcoming_loading');
			},
			error:function () {
				alert('Internal error. Try again.');
			}
		});
		fc.user.updateBettingMarkets({"idsport":idSport, "idevent":idEvent});
	});
</script>
