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

  <?php $this->headScript()->appendFile('application/modules/Article/externals/scripts/slideshow.js') ?>
  
  <div class="article_featured_articles">
    <div class="article_featured_mask">
      <div id="<?php echo $this->widget_name?>" class="article_featured_slides">
        <?php foreach ($this->paginator as $article): ?>
          <div class="article_featured_slide article_record_<?php echo $article->getIdentity();?>">
            <?php if ($this->showphoto && $article->photo_id): ?>
              <div class="article_photo">
                <?php echo $this->htmlLink($article->getHref(), $this->itemPhoto($article, 'thumb.normal'));?>
              </div>   
            <?php endif; ?>         
            <div class="article_content">
              <div class="article_title">
                <?php echo $this->partial('index/_title.tpl', 'article', array('article' => $article, 'max_title_length' => 42))?>
              </div>
              <?php if ($this->showdescription && $article->getDescription()): ?>
                <div class="article_description">
                  <?php echo $this->radcodes()->text()->truncate($article->getDescription(), 255); ?>
                  <?php //echo $this->partial('index/_description.tpl', 'article', array('article' => $article))?>
                </div>
              <?php endif; ?>  
              <?php if ($this->showmeta): ?>
                <div class="article_meta">
                  <?php echo $this->partial('index/_meta.tpl', 'article', array('article' => $article))?>
                </div>
              <?php endif; ?>
            </div>
          </div>  
        <?php endforeach; ?>  
      </div>
    </div>
    <?php if ($this->use_slideshow): ?>
      <p class="article_featured_slideshow_buttons" id="<?php echo $this->widget_name?>_buttons">
        <span id="<?php echo $this->widget_name?>_prev" class="article_slideshow_button_prev"><span><?php echo $this->translate('&lt;&lt; Previous'); ?></span></span>
        <span id="<?php echo $this->widget_name?>_play" class="article_slideshow_button_play"><span><?php echo $this->translate('Play &gt;'); ?></span></span>
        <span id="<?php echo $this->widget_name?>_stop" class="article_slideshow_button_stop"><span><?php echo $this->translate('Stop'); ?></span></span>
        <span id="<?php echo $this->widget_name?>_next" class="article_slideshow_button_next"><span><?php echo $this->translate('Next &gt;&gt;'); ?></span></span>
      </p>
    <?php endif; ?>
  </div>
  <?php if ($this->use_slideshow): ?>
    <script type="text/javascript">
    en4.core.runonce.add(function(){
      var <?php echo $this->widget_name?>_width = $('<?php echo $this->widget_name?>').getSize().x;
      
      $$('#<?php echo $this->widget_name?> div.article_featured_slide').each(function(el){
        el.setStyle('width', <?php echo $this->widget_name?>_width - 30);
      });
    	var <?php echo $this->widget_name?> = new radcodesArticleNoobSlide({
    		box: $('<?php echo $this->widget_name?>'),
    		items: $$('#<?php echo $this->widget_name?> div.article_featured_slide'),
    		size: <?php echo $this->widget_name?>_width,
    		autoPlay: true,
    		interval: 8000,
    		addButtons: {
    			previous: $('<?php echo $this->widget_name?>_prev'),
    			play: $('<?php echo $this->widget_name?>_play'),
    			stop: $('<?php echo $this->widget_name?>_stop'),
    			next: $('<?php echo $this->widget_name?>_next')
    		},
    		onWalk: function(currentItem,currentHandle){
    		}
    	});
    });
    </script>
  <?php endif; ?>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no featured articles.');?>
    </span>
  </div>
<?php endif; ?>