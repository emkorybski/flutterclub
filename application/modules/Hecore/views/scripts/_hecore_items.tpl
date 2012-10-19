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

<?php if ($this->items->getCurrentItemCount() > 0): ?>

<?php if ($this->items->getCurrentPageNumber() > 1): ?>
  <a class="pagination" href="javascript:he_list.set_page(<?php echo ($this->items->getCurrentPageNumber()-1); ?>);"><?php echo $this->translate("Previous"); ?></a>
<?php endif; ?>

<?php foreach ($this->items as $item): ?>
  <a href="<?php echo $item->getHref(); ?>" target="_blank" class="item" id="contact_<?php echo $item->getIdentity(); ?>">
    <span class='photo' style='background-image: url()'><?php echo $this->itemPhoto($item, 'thumb.icon'); ?></span>
    <span class="name"><?php echo $item->getTitle(); ?></span>
    <div class="clr"></div>
  </a>
<?php endforeach; ?>

<?php if ($this->items->count() > $this->items->getCurrentPageNumber()): ?>
  <a class="pagination" href="javascript:he_list.set_page(<?php echo ($this->items->getCurrentPageNumber()+1); ?>);"><?php echo $this->translate("Next"); ?></a>
<?php endif; ?>

<?php else: ?>
  <div class="no_result"><?php echo $this->translate("There are no members"); ?></div>
<?php endif; ?>

<div id="no_result" class="hidden"><?php echo $this->translate("There are no members"); ?></div>
<div class="clr" id="he_contacts_end_line"></div>