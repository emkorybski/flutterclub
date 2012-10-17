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
  <?php 
    $article_title = $this->article->getTitle();
    if ($this->max_title_length) {
      $article_title = $this->radcodes()->text()->truncate($article_title, $this->max_title_length);
    }
  ?>
  <h3 class="article_sponsored_<?php echo $this->article->sponsored ? 'yes' : 'no'?>">
    <span class="article_featured_<?php echo $this->article->featured ? 'yes' : 'no'?>">
      <?php echo $this->htmlLink($this->article->getHref(), $article_title); ?>
    </span>
  </h3>

