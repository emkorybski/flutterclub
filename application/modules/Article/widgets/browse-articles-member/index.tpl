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

<div class='article_browse_articles_member'>
  <?php echo $this->htmlLink($this->userObject->getHref(),
    $this->itemPhoto($this->userObject, 'thumb.icon'),
    array('class' => 'article_browse_articles_member_photo')
  )?>
  <?php echo $this->htmlLink($this->userObject->getHref(), $this->userObject->getTitle(), array('class' => 'article_browse_articles_member_user')) ?>
</div>