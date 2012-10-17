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
<?php if ($this->paginator->getTotalItemCount()): ?>
  
  <?php if ($this->display_style == 'narrow'): ?>

    <ul class='articles_list'>
      <?php foreach ($this->paginator as $article): ?>
        <li>
          <?php if ($this->showphoto): ?>
            <div class="article_photo">
              <?php echo $this->htmlLink($article->getHref(), $this->itemPhoto($article, 'thumb.icon'));?>
            </div>
          <?php endif; ?>
          <div class="article_content">
            <div class="article_title">
              <?php echo $this->htmlLink($article->getHref(), $this->radcodes()->text()->truncate($article->getTitle(), 28)) ?>
            </div>
            <?php if ($this->showmeta): ?>
                <?php 
                  $meta_options = array('article' => $article,
                    'display_style' => 'narrow',
                    'show_owner' => false,
                    'show_comments' => $this->order == 'mostcommented' ? true : false,
                    'show_views' => $this->order == 'mostviewed' ? true : false,
                    'show_likes' => $this->order == 'mostliked' ? true : false,
                  );
                ?>
              <div class="article_meta">  
                <?php echo $this->partial('index/_meta.tpl', 'article', $meta_options)?>
              </div>
            <?php endif; ?>
            <?php if ($this->showdescription && $article->getDescription()): ?>
              <div class="article_description">
                <?php echo $this->viewMore($article->getDescription()); ?>
              </div>
            <?php endif; ?>
          </div>
        </li>
      <?php endforeach; ?>  
    </ul>

  <?php else: ?>
    <ul class='articles_rows'>
      <?php foreach ($this->paginator as $article): ?>
        <li>
          <?php if ($this->showphoto): ?>
            <div class="article_photo">
              <?php echo $this->htmlLink($article->getHref(), $this->itemPhoto($article, 'thumb.normal'));?>
            </div>
          <?php endif; ?>
          <div class="article_content">
            <div class="article_title">
              <?php echo $this->partial('index/_title.tpl', 'article', array('article' => $article))?>
            </div>
            <?php if ($this->showmeta): ?>
              <div class="article_meta">
                <?php echo $this->partial('index/_meta.tpl', 'article', array('article' => $article, 'show_owner' => false))?>
              </div>  
            <?php endif; ?>
            <?php if ($this->showdescription && $article->getDescription()): ?>
              <div class="article_description">
                <?php echo $this->viewMore($article->getDescription()); ?>
              </div>
            <?php endif; ?>
          </div>
        </li>
      <?php endforeach; ?>  
    </ul>

  <?php endif;?>
  
  <?php if ($this->showmemberarticleslink): ?>
    <div class="article_profile_articles_link article_profile_articles_link_<?php echo $this->display_style; ?>">
      <?php echo $this->htmlLink(array('route'=>'article_general', 'action'=>'browse', 'user'=>$this->user->getIdentity()),
        $this->translate('View %s\'s Articles', $this->user->getTitle()),
        array('class'=>'buttonlink item_icon_article')
      )?>
    </div>    
  <?php endif; ?>  
<?php endif; ?>
 