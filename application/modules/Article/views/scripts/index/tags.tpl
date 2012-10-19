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
$this->headTitle('Article Tags');
?>

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

<div class='layout_right article_layout_right'>
  <div class="articles_gutter">
  <?php echo $this->form->render($this) ?>
    
  <?php if( $this->can_create): ?>
    <div class="quicklinks">
      <ul>
        <li>
          <a href='<?php echo $this->url(array(), 'article_create', true) ?>' class='buttonlink icon_article_new'><?php echo $this->translate('Post New Article');?></a>
        </li>
      </ul>
    </div>
  <?php endif; ?>
  
  </div>
</div>

<div class='layout_middle article_layout_middle'>

  <?php if (!empty($this->tags)): ?>
  
      <h3 class="sep">
        <span><?php echo $this->translate('Article Tags'); ?></span>
      </h3>    
  
      <div class="radcodes_popular_tags articles_popular_tags">
        <ul>
        <?php foreach ($this->tags as $k => $tag): ?>
          <li><?php echo $this->htmlLink(array(
                      'route' => 'article_browse',
                      'tag' => $tag->tag_id),
            $tag->text, 
            array('class'=> "tag_x tag_$k")
          )?>
          <sup><?php echo $tag->total; ?></sup>
          </li>
        <?php endforeach; ?>
        </ul>
      </div>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('Nobody has posted a article yet.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('Be the first to <a href=\'%s\'>post</a> one!', $this->url(array(), 'article_create')); ?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>
  
</div>

