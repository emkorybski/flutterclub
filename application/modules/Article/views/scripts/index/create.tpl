<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Article
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>

<?php echo $this->partial('index/_js_fields.tpl', 'article', array())?>

<div class="headline">
  <h2>
    <?php echo $this->translate('Articles');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<?php if ($this->current_count >= $this->quota && $this->quota > 0):?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You have already created the maximum number of articles allowed.');?>
      <?php echo $this->translate('If you would like to create a new article, please <a href="%1$s">delete</a> an old one first.', $this->url(array(), 'article_manage'));?>
    </span>
  </div>
  <br/>
<?php else:?>

  <?php echo $this->form->render($this);?>
<?php endif; ?>
