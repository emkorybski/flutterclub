<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 9242 2011-09-07 01:54:44Z john $
 * @author		 John
 */
?>

<h3><?php echo $this->translate("Group Info") ?></h3>

<ul>
  <li class="group_stats_title">
    <span>
      <?php echo $this->group->getTitle() ?>
    </span>
    <?php if( !empty($this->group->category_id) && 
        ($category = $this->group->getCategory()) instanceof Core_Model_Item_Abstract &&
        !empty($category->title)): ?>
      <?php echo $this->htmlLink(array('route' => 'group_general', 'action' => 'browse', 'category_id' => $this->group->category_id), $this->translate((string)$category->title)) ?>
    <?php endif; ?>
  </li>
  <?php if( '' !== ($description = $this->group->description) ): ?>
    <li class="group_stats_description">
      <?php echo $this->viewMore($description, null, null, null, true) ?>
    </li>
  <?php endif; ?>
  <li class="group_stats_staff">
    <ul>
      <?php foreach( $this->staff as $info ): ?>
        <li>
          <?php echo $info['user']->__toString() ?>
          <?php if( $this->group->isOwner($info['user']) ): ?>
            (<?php echo ( !empty($info['membership']) && $info['membership']->title ? $info['membership']->title : $this->translate('owner') ) ?>)
          <?php else: ?>
            (<?php echo ( !empty($info['membership']) && $info['membership']->title ? $info['membership']->title : $this->translate('officer') ) ?>)
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </li>
  <li class="group_stats_info">
    <ul>
      <li><?php echo $this->translate(array('%s total view', '%s total views', $this->group->view_count), $this->locale()->toNumber($this->group->view_count)) ?></li>
      <li><?php echo $this->translate(array('%s total member', '%s total members', $this->group->member_count), $this->locale()->toNumber($this->group->member_count)) ?></li>
      <li><?php echo $this->translate('Last updated %s', $this->timestamp($this->group->modified_date)) ?></li>
    </ul>
  </li>
</ul>