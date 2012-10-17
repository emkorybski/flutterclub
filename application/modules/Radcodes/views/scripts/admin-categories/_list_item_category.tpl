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

<?php $category = $this->category; $used_count = $category->getUsedCount(); ?>
  <div class="radcodes_category_item">
    <img src="application/modules/Core/externals/images/admin/sortable.png" width="16" height="16" class='move-me' /> 
    <span class="category_name">
      <?php echo $this->htmlLink($category->getHref(), $category->getTitle(), array('target'=>'_blank'))?>
    </span>
      <?php // echo $category->getIdentity(); ?>
    <span class="category_used_count">(<?php echo $this->translate(array("%s entry", "%s entries", $used_count), $this->locale()->toNumber($used_count)); ?>)</span>
    
    <?php if ($category->supportProfileType()): ?>
    <span class="category_profile_type">
      <?php // echo $category->getProfileTypeLabel(); ?>
 
      <?php if ($category->hasParentCategory()): ?>
        <span class="category_profile_type_inherited"><?php echo $this->translate('--inherited--') ?></span>
      <?php else: ?>
        <?php if ($category->hasProfileType()): ?>
          <span class="category_profile_type_custom"><?php echo $this->translate($category->getProfileTypeLabel())?></span>
        <?php else: ?>  
          <span class="category_profile_type_default"><?php echo 'Default::' . $this->translate($category->getModuleApi()->profile()->getLabel($category->getModuleApi()->profile()->getDefaultTypeId())); ?></span>
        <?php endif; ?>
        (<?php echo $this->htmlLink($this->url(array('action'=>'profile-type', 'category_id'=>$category->category_id)), $this->translate('change'), array('class'=>'smoothbox'))?>)
        
      <?php endif; ?>

    </span>
    <?php endif; ?>
    
    <span class="category_options">
      <?php if ($category->photo_id): ?>
        <?php echo $this->htmlLink($this->url(array('action'=>'icon', 'category_id'=>$category->category_id)), $this->translate('icon'), array('class'=>'smoothbox'))?>
        |
      <?php endif; ?>    
      <?php echo $this->htmlLink($this->url(array('action'=>'edit', 'category_id'=>$category->category_id)), $this->translate('edit'), array('class'=>'smoothbox'))?>
      |
      <?php echo $this->htmlLink($this->url(array('action'=>'delete', 'category_id'=>$category->category_id)), $this->translate('delete'), array('class'=>'smoothbox'))?>

    </span>
  </div>