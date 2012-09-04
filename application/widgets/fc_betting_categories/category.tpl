<a href="" attr-idsport="<?=$category['idsport']?>"
   attr-idevent="<?=$category['idevent']?>"
   class="fc_betting_category"
   style="display: block; padding-left: <?=10 + $indent * 20?>px; text-decoration: none">
	<?=$category['name']?>
</a>
<hr class="line"/>
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