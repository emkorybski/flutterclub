<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: blogpagination.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Jung
 */
?>
 <style type="text/css">
 .paginationControl{
     padding-right:5px;
     padding-left:5px;
     
 }
 
 </style>
<?php if ($this->pageCount > 1): ?>
  <div class="paginationControl" align="center">

    <?php /* Previous page link */ ?>
    <?php if (isset($this->previous)): ?>
      <a href="javascript:void(0)" onclick="javascript:pageAction(<?php echo $this->previous;?>)"><?php echo $this->translate('&#171; Previous');?></a>
      <?php if (isset($this->previous)): ?>
      &nbsp;|
      <?php endif; ?>
    <?php endif; ?>
	<?php $length = count($this->pagesInRange);$count= 0;?>
    <?php foreach ($this->pagesInRange as $page): ?>
      <?php $count++; ?>
      <?php if ($page != $this->current): ?>
        <a href="javascript:void(0)" onclick="javascript:pageAction(<?php echo $page;?>)"><?php echo $page;?></a> |
      <?php else: ?>
        
        <?php echo $page; ?> <?php if ($count <$length):?>|<?php endif;?>
      <?php endif; ?>
    <?php endforeach; ?>

    <?php /* Next page link */ ?>
    <?php if (isset($this->next)): ?>
        <a href="javascript:void(0)" onclick="javascript:pageAction(<?php echo $this->next;?>)"><?php echo $this->translate('Next &#187;');?></a>
    <?php endif; ?>

  </div>
<?php endif; ?>