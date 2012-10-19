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
<?php $article = $this->article; ?>

<div class="article_profile_icon_featured">
  <?php if ($this->image): ?>
    <div class="article_profile_icon_featured_image">
      <?php echo $this->htmlImage($this->image, $this->text); ?>
    </div>
  <?php endif;?>
  <?php if ($this->text): ?>
    <div class="article_profile_icon_featured_text">
      <span><?php echo $this->translate($this->text)?></span>
    </div>
  <?php endif; ?>
</div>
