<a
	href="<?=WEB_ROOT?>widget/index/name/fc_betting_categories?format=html&idsport=<?=$category['idsport']?>&idevent=<?=$category['idevent']?>"
	class="betting_category"
	style="display: block; padding-left: <?=10 + $indent * 20?>px; text-decoration: none">
		<input type="hidden" value="<?=WEB_ROOT?>widget?name=fc_betting_markets&format=html&idsport=<?=$category['idsport']?>&idevent=<?=$category['idevent']?>" />
		<?=$category['name']?>
</a>
<hr class="line" />

<?php
	if (!empty($category['children'])) {
		++$indent;
		foreach ($category['children'] as $child) {
			$category = $child;
			require(__FILE__);
		}
		--$indent;
	}
?>

