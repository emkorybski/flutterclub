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

	<ul>
	  <?php if ($this->show_owner !== false): ?>
	    <li class="article_meta_owner"><?php echo $this->translate($this->display_style == 'narrow' ? 'by %s' : 'Posted by %s', $this->article->getOwner()->toString()); ?></li>
	  <?php endif; ?>
	  <?php if ($this->show_date !== false): ?>
	    <li class="article_meta_date"><?php echo $this->timestamp($this->article->creation_date); ?></li>
	  <?php endif; ?>
	  <?php if ($this->show_comments !== false): ?>
	    <li class="article_meta_comments"><?php echo $this->translate(array("%s comment", "%s comments", $this->article->comment_count), $this->locale()->toNumber($this->article->comment_count)); ?></li>
	  <?php endif; ?>
	  <?php if ($this->show_likes !== false): ?>
	    <li class="article_meta_likes"><?php echo $this->translate(array('%1$s like', '%1$s likes', $this->article->like_count), $this->locale()->toNumber($this->article->like_count)); ?></li>
	  <?php endif; ?>
	  <?php if ($this->show_views !== false): ?>
	    <li class="article_meta_views"><?php echo $this->translate(array('%s view', '%s views', $this->article->view_count), $this->locale()->toNumber($this->article->view_count)); ?></li>
	  <?php endif; ?>
	</ul>
