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
<div class='article_profile_related_articles'>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
      <ul class='articles_list'>
        <?php foreach ($this->paginator as $article): ?>
          <li>
            <?php if ($this->showphoto && $article->photo_id): ?>
              <div class="article_photo">
                <?php echo $this->htmlLink($article->getHref(), $this->itemPhoto($article, 'thumb.icon'));?>
              </div>
            <?php endif;?>            
            <div class="article_content">
              <div class="article_title">
                <?php $title = $this->radcodes()->text()->truncate($article->getTitle(), 36); ?>
                <?php echo $this->htmlLink($article->getHref(), $title); ?>
              </div>
              <div class="article_details">
                <?php echo $this->translate('by %s', $article->getOwner()->toString()); ?>
              </div>
            </div>
          </li>
        <?php endforeach; ?>  
      </ul>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There are no tagging related articles for this article.');?>
      </span>
    </div>
  <?php endif; ?>    
</div>