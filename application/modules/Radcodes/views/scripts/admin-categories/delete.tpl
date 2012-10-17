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
<?php 
$usedCount = $this->category->getUsedCount();
$hasChildrend = $this->category->hasChildrenCategory();
?>
<?php if ($usedCount or $hasChildrend): ?>
<div class="global_form_popup">
  <ul class="form-errors">
    <li>
      <?php if ($usedCount): ?>
        <?php echo $this->translate('This category has %s entries associated with it. To delete this category, you must move these entries to a different category first.', $this->category->getUsedCount()); ?>
      <?php elseif ($hasChildrend): ?>
        <?php echo $this->translate('This category has sub-categories associated with it. To delete this category, you must delete sub-categories first or move them to a different parent category.'); ?>
      <?php endif; ?>
    </li>
  </ul>
</div>
<?php else: ?>
  <?php echo $this->form->render($this) ?>
<?php endif; ?>
