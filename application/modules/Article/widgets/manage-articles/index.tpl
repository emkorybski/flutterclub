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

  <?php if( $this->tag || $this->keyword):?>
    <div class="articles_result_filter_details">
      <?php echo $this->translate('Showing articles posted'); ?>
      <?php if ($this->tag): ?>
        <?php echo $this->translate('using tag #%s', $this->htmlLink(
          array('route'=>'article_manage','tag'=>$this->tag),
          $this->tagObject ? $this->tagObject->text : $this->tag
        ));?>
      <?php endif; ?>
      <?php if ($this->keyword): ?>
        <?php echo $this->translate('with keyword %s', $this->htmlLink(
          $this->url(array('keyword'=>$this->keyword), 'article_manage', true),
          $this->keyword
        ));?>
      <?php endif; ?>   
      <?php echo $this->htmlLink(array('route'=>'article_manage'), $this->translate('(x)'));?>
    </div>
  <?php endif; ?>

    <ul class='articles_rows'>
      <?php foreach ($this->paginator as $article): ?>
        <li>
          <?php if ($this->showphoto): ?>
            <div class="article_photo">
              <?php echo $this->htmlLink($article->getHref(), $this->itemPhoto($article, 'thumb.normal'));?>
            </div>
          <?php endif; ?>
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
            <?php if ($this->showmeta): ?>
              <div class="article_meta">
                <?php echo $this->partial('index/_meta.tpl', 'article', array('article' => $article))?>
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
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues
    )); ?>  
<?php elseif ( $this->tag || $this->keyword || $this->category): ?>       
  <div class="tip">
    <span>
      <?php echo $this->translate('You do not have any article that match your search criteria.');?>
    </span>
  </div>
<?php else: ?>    
  <div class="tip">
    <span>
      <?php echo $this->translate('You do not have any articles.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('Get started by <a href=\'%1$s\'>posting</a> a new article.', $this->url(array(), 'article_create', true));?>
        <?php endif; ?>        
    </span>  
  </div>
<?php endif; ?>
