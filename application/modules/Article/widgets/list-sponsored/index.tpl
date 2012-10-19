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

  <?php $this->headScript()->appendFile('application/modules/Article/externals/scripts/ticker.js') ?>
  
  <div class="article_sponsored_articles">
    <ul id="<?php echo $this->widget_name?>" class="article_sponsored_slides">
      <?php foreach ($this->paginator as $article): ?>
        <li>
          <?php if ($this->showphoto): ?>
            <div class="article_photo">
              <?php echo $this->htmlLink($article->getHref(), $this->itemPhoto($article, 'thumb.normal'));?>
            </div>   
          <?php endif; ?>         
          <div class="article_content">
            <div class="article_title">
              <?php echo $this->partial('index/_title.tpl', 'article', array('article' => $article, 'max_title_length' => 46))?>
            </div>
            <?php if ($this->showdescription && $article->getDescription()): ?>
              <div class="article_description">
                <?php echo $this->radcodes()->text()->truncate($article->getDescription(), 128); ?>
              </div>
            <?php endif; ?>  
            <?php if ($this->showmeta): ?>
              <div class="article_meta">
                <?php echo $this->partial('index/_meta.tpl', 'article', array('article' => $article, 'show_comments' => false, 'show_likes' => false, 'show_views' => false))?>
              </div>
            <?php endif; ?>
          </div>
        </li>  
      <?php endforeach; ?>  
    </ul>
    
  </div>

  <?php if ($this->use_slideshow): ?>
    <script type="text/javascript">
    en4.core.runonce.add(function(){
    	<?php echo $this->widget_name?>Ticker = new radcodesArticleNewsTicker('<?php echo $this->widget_name?>', {speed:1000,delay:15000,direction:'vertical'});
    });
    </script>
    <div class="article_sponsored_articles_action">
      <a href="javascript: void(0);" onclick="<?php echo $this->widget_name?>Ticker.next(); return false;"><?php echo $this->translate("Next &raquo;")?></a>
    </div>    
  <?php endif; ?>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no sponsored articles.');?>
    </span>
  </div>
<?php endif; ?>