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

<div class='article_profile_submitter'>
  <?php echo $this->htmlLink($this->owner->getHref(),
    $this->itemPhoto($this->owner, 'thumb.icon'),
    array('class' => 'article_profile_submitter_photo')
  )?>
  <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle(), array('class' => 'article_profile_submitter_user')) ?>
  <span><?php echo $this->translate(array('%d article','%s articles', $this->totalArticles), $this->totalArticles); ?></span>
</div>