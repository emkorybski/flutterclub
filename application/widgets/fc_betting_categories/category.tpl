<style type="text/css">

	#form_guide{display:block;border-bottom:1px solid #ccc;padding-left:14px;padding-top:3px;padding-bottom:3px;font-size:12px;font-weight:bold;background:#e6e6e6}

</style>

<?php $class = $category['isLeaf'] ? ' category_leaf' : ' category_branch'; ?>
<a href="" attr-idsport="<?=$category['idsport']?>"
   attr-idevent="<?=$category['idevent']?>"
   class="fc_betting_category<?=$class?>"
   style="display: block; padding-left: <?=10 + $indent * 20?>px; text-decoration: none">
	<?=$category['name']?>
</a>

<?php if($category['name'] == 'Horse Racing') : ?>
	         
		<a id="form_guide" target="_blank" href="http://www.attheraces.com/index.aspx?ref=splash">> Horse Racing Form Guide</button>
	
	<?php endif; ?>

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