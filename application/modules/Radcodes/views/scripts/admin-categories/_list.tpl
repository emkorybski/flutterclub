<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>
<div class="radcodes_categories_lists">
<?php if (count($this->categories) > 0): ?>
	<ul class="radcodes_categories_list" id="admin_category_parent_0">
	<?php foreach ($this->categories as $category): ?>
	  <li id="admin_category_item_<?php echo $category->category_id; ?>">
	    <?php echo $this->partial('admin-categories/_list_item_category.tpl', array('category'=>$category)); ?>
	      <?php $subcategories = $category->getChildrenCategory(); ?>
	      <?php if (count($subcategories)): ?>
	        <ul class="radcodes_categories_sublist" id="admin_category_parent_<?php echo $category->category_id?>">
	          <?php foreach ($subcategories as $subcategory): ?>
	            <li id="admin_category_item_<?php echo $subcategory->category_id; ?>">
	              <?php echo $this->partial('admin-categories/_list_item_category.tpl', array('category'=>$subcategory)); ?>
	            </li>
	          <?php endforeach; ?>
	        </ul>
	      <?php endif; ?>
	  </li>
	<?php endforeach; ?>
	</ul>

<?php else: ?>
  <br/>
  <div class="tip">
    <span><?php echo $this->translate("There are currently no categories.") ?></span>
  </div>
<?php endif; ?>
</div>