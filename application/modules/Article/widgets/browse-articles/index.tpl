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

  <?php if( $this->tag || $this->user || $this->keyword):?>
    <div class="articles_result_filter_details">
      <?php echo $this->translate('Showing articles posted'); ?>
      <?php if ($this->user): ?>
        <?php echo $this->translate('by user %s', $this->htmlLink(
          array('route'=>'article_browse','user'=>$this->user),
          $this->userObject instanceof User_Model_User ? $this->userObject->getTitle() : '#'.$this->user 
        ));?>
      <?php endif; ?>
      <?php if ($this->tag): ?>
        <?php echo $this->translate('using tag #%s', $this->htmlLink(
          array('route'=>'article_browse','tag'=>$this->tag),
          $this->tagObject ? $this->tagObject->text : $this->tag
        ));?>
      <?php endif; ?>
      <?php if ($this->keyword): ?>
        <?php echo $this->translate('with keyword %s', $this->htmlLink(
          $this->url(array('keyword'=>$this->keyword), 'article_browse', true),
          $this->keyword
        ));?>
      <?php endif; ?>   
      <?php echo $this->htmlLink(array('route'=>'article_browse'), $this->translate('(x)'));?>
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
<?php elseif ( $this->tag || $this->keyword || $this->user || $this->category): ?>       
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has posted an article with that criteria.');?>
    </span>
  </div>
<?php else: ?>    
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has posted an article yet.');?>
    </span>
  </div>
<?php endif; ?>
