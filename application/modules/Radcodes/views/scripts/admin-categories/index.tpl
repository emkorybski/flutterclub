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
//return;
  // Render the sorting js
  echo $this->render('admin-categories/_jsSort.tpl');
?>


<h2><?php echo $this->translate("$this->moduleName Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p class="description">
  <?php echo $this->translate("Manage categories on this page.")?>
</p>

<?php if (count($this->categories) > 0): ?>
  <div class="radcodes_categories_lists">
    <?php echo $this->render('admin-categories/_list.tpl'); ?>
    <?php foreach ($this->categories as $category): ?>
      <li><?php echo $category->getTitle()?></li>
      
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <br/>
  <div class="tip">
    <span><?php echo $this->translate("There are currently no categories.") ?></span>
  </div>
<?php endif; ?>



<?php return; ?>
  <div class='clear'>

        <div class="radcodes_categories_options">
          <?php echo $this->render('admin-categories/_options.tpl'); ?>
        </div>
      <?php if(count($this->categories)>0):?>
        <div class="radcodes_categories_lists">
          <?php echo $this->render('admin-categories/_list.tpl'); ?>
        </div>
      <?php else:?>
      <br/>
      <div class="tip">
        <span><?php echo $this->translate("There are currently no categories.") ?></span>
      </div>
      <?php endif;?>


  </div>
     