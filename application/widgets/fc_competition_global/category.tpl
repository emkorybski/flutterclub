<a
	href="/fc/widget/index/name/categories?format=html&idsport=<?=$category['idsport']?>&idevent=<?=$category['idevent']?>"
	class="betting_category"
	style="display: block; padding-left: <?=10 + $indent * 20?>px; text-decoration: none">
		<input type="hidden" value="/fc/widget/index/name/upcoming?format=html&idsport=<?=$category['idsport']?>&idevent=<?=$category['idevent']?>" />
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

