<?php $class = $category['isLeaf'] ? ' category_lead' : ' category_branch'; ?>
<a href="" attr-idsport="<?=$category['idsport']?>"
   attr-idevent="<?=$category['idevent']?>"
   class="fc_betting_category<?=$class?>"
   style="display: block; padding-left: <?=10 + $indent * 20?>px; text-decoration: none">
	<?=$category['name']?>
</a>
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