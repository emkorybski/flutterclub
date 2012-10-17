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
/*
$this->category->getType() = article_category
$this->category->getType(true) = ArticleCategory
$this->category->getShortType() = Category
$this->category->getShortType(true) = Category
$this->category->getModuleName() = Article

echo '<br>$this->category->getType() = ' .$this->category->getType();
echo '<br>$this->category->getType(true) = ' .$this->category->getType(true);
echo '<br>$this->category->getShortType() = ' .$this->category->getShortType(tue);
echo '<br>$this->category->getShortType(true) = ' .$this->category->getShortType(true);
echo '<br>$this->category->getModuleName() = ' . $this->category->getModuleName();
echo '<br>$this->category->getModuleItemType() = ' . $this->category->getModuleItemType();
 */
?>
<div class="global_form_popup">
<p>thumb.mini</p>
<?php echo $this->itemPhoto($this->category, 'thumb.mini') ?>
<p>thumb.icon</p>
<?php echo $this->itemPhoto($this->category, 'thumb.icon') ?>
<p>thumb.normal</p>
<?php echo $this->itemPhoto($this->category, 'thumb.normal') ?>
<p>thumb.profile</p>
<?php echo $this->itemPhoto($this->category, 'thumb.profile') ?>
<br />
<p><?php echo $this->translate('Sample PHP Usage:')?>
<br />
<code style="white-space: nowrap;">&lt;?php echo $this-&gt;itemPhoto($category, 'thumb.normal'); ?&gt;</code>
</div>

<?php echo $this->form->setAction($this->url(array('action'=>'delete-photo')))->render($this) ?>