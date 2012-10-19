<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _hecore_items.tpl 2010-07-02 17:53 idris $
 * @author     Idris
 */
?>

<?php if (isset($this->items) && $this->items->getCurrentItemCount() > 0): ?>
<?php foreach ($this->items as $item): ?>
  <?php $itemDisabled = in_array($item->getIdentity(), $this->disabledItems); ?>
  <?php $itemChecked = in_array($item->getIdentity(), $this->checkedItems); ?>
  <a href='javascript:void(0)' <?php if ($itemDisabled && $this->disabled_label): ?>title = "<?php echo $this->disabled_label; ?>"<?php endif; ?>  class="item <?php if ($itemDisabled) echo "disabled" ?> <?php if ($itemChecked) echo "active" ?>" id="contact_<?php echo $item->getIdentity(); ?>">
    <span class='photo' style='background-image: url()'>
      <?php echo $this->itemPhoto($item, 'thumb.icon'); ?>
      <span class="inner"></span>
    </span>
    <span class="name"><?php echo $item->getTitle(); ?></span>
    <div class="clr"></div>
  </a>
<?php endforeach; ?>
<?php endif; ?>