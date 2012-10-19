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

<?php echo $this->partial('index/_js_fields_search.tpl', 'article', array())?>

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

<div class='layout_right'>
  <?php echo $this->form->render($this) ?>
  <?php if( count($this->quickNavigation) > 0 ): ?>
    <div class="quicklinks">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->quickNavigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>

<div class='layout_middle article_layout_middle'>

  <?php if( $this->tag || $this->start_date  || $this->keyword):?>
    <div class="articles_result_filter_details">
      <?php echo $this->translate('Showing articles posted'); ?>
      <?php if ($this->tag): ?>
        <?php echo $this->translate('using tag #%s', $this->htmlLink(
          $this->url(array('tag'=>$this->tag), 'article_manage', true),
          $this->tagObject ? $this->tagObject->text : $this->tag
        ));?>
      <?php endif; ?>
      <?php if ($this->keyword): ?>
        <?php echo $this->translate('with keyword %s', $this->htmlLink(
          $this->url(array('keyword'=>$this->keyword), 'article_manage', true),
          $this->keyword
        ));?>
      <?php endif; ?> 
      <?php if ($this->start_date): $archive_date = Radcodes_Lib_Helper_Date::archive($this->start_date); ?>
        <?php echo $this->translate('on %s', $this->htmlLink(
          $this->url(array('start_date'=>$archive_date['date_start'],'end_date'=>$archive_date['date_end']), 'article_manage', true),
          $archive_date['label']
        ));?> 
      <?php endif; ?>    
      <a href="<?php echo $this->url(array(), 'article_manage', true) ?>">(x)</a>
    </div>
  <?php endif; ?>
  
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  
      <h3 class="sep">
        <span>
          <?php if ($this->categoryObject): ?>
            <?php echo $this->translate('My %s Articles', $this->categoryObject->getTitle())?>
          <?php else: ?>  
            <?php echo $this->translate('All Categories'); ?>
          <?php endif; ?>
        </span>
      </h3>    
  
    <ul class="articles_rows">
      <?php foreach( $this->paginator as $article ): ?>
        <li>
          <div class='article_photo'>
            <?php echo $this->htmlLink($article->getHref(), $this->itemPhoto($article, 'thumb.normal')) ?>
          </div>
          <div class='article_options'>
            <a href='<?php echo $this->url(array('action'=>'edit', 'article_id' => $article->article_id), 'article_specific', true) ?>' class='buttonlink icon_article_edit'><?php echo $this->translate('Edit Article');?></a>
            <?php if( $this->allowed_upload ): ?>
              <?php echo $this->htmlLink(array(
                  'route' => 'article_extended',
                  'controller' => 'photo',
                  'action' => 'upload',
                  'subject' => $article->getGuid(),
                ), $this->translate('Add Photos'), array(
                  'class' => 'buttonlink icon_article_photo_new'
              )) ?>
            <?php endif; ?>
            <a href='<?php echo $this->url(array('action'=>'delete', 'article_id' => $article->article_id), 'article_specific', true) ?>' class='buttonlink icon_article_delete'><?php echo $this->translate('Delete Article');?></a>
            <?php if( !$article->published ): ?>
            	<?php if ($this->approval): ?>
            	  <a href="javascript:void(0)" onclick="alert('<?php echo $this->translate('Administrator will manually review and publish this article.');?>'); return false;" class='buttonlink icon_article_publish'><?php echo $this->translate('Status: Draft'); ?></a>
            	<?php else: ?>
            	  <a href='<?php echo $this->url(array('article_id' => $article->article_id), 'article_publish', true) ?>' class='buttonlink icon_article_publish'><?php echo $this->translate('Publish Article');?></a>
            	<?php endif; ?>
            <?php endif; ?>
          </div>
          
          <div class="article_content">
            <div class="article_title">
              <?php echo $this->partial('index/_title.tpl', 'article', array('article' => $article))?>
            </div>
            <div class="article_meta">
              <?php echo $this->partial('index/_meta.tpl', 'article', array('article' => $article, 'show_owner' => false))?>
            </div>
            <?php if ($article->getDescription()): ?>
            <div class="article_description">
              <?php echo $this->partial('index/_description.tpl', 'article', array('article' => $article))?>
            </div>
            <?php endif; ?>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>

  <?php elseif($this->category || $this->keyword || $this->tag || $this->start_date): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any article that match your search criteria.');?>
      </span>
    </div>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any articles.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('Get started by <a href=\'%1$s\'>posting</a> a new article.', $this->url(array(), 'article_create', true));?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>

    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues
    )); ?>    
</div>
