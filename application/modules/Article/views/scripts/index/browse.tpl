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
$this->headTitle('Browse Articles');
?>

<script type="text/javascript">
  var dateAction =function(start_date, end_date){
    $('start_date').value = start_date;
    $('end_date').value = end_date;
    $('filter_form').submit();
  }
</script>

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
  
	<?php if (count($this->archive_list )):?>
		<h4><?php echo $this->translate('Archives');?></h4>
		<ul>
			<?php foreach ($this->archive_list as $archive): ?>
				<li>
				  <a href='javascript:void(0);' onclick='javascript:dateAction(<?php echo $archive['date_start']?>, <?php echo $archive['date_end']?>);' <?php if ($this->start_date==$archive['date_start']) echo " style='font-weight: bold;'";?>>
				    <?php echo $this->translate($archive['label'])?>
          </a>
          <?php // echo $archive['count']?>
  			</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?> 
  </div>
</div>

<div class='layout_middle article_layout_middle'>

  <?php if( $this->tag || $this->start_date || ($this->user_id && $this->user($this->user_id)->getIdentity()) || $this->search):?>
    <div class="articles_result_filter_details">
      <?php echo $this->translate('Showing articles posted'); ?>
      <?php if( $this->user_id && $this->user($this->user_id)->getIdentity()): ?>
        <?php echo $this->translate('by user %s', $this->htmlLink(
          $this->url(array('user'=>$this->user_id), 'article_browse', true),
          $this->user($this->user_id)->getTitle()
        ));?>
        <?php $this->headTitle($this->user($this->user_id)->getTitle()); ?>
      <?php endif; ?>
      <?php if ($this->tag): ?>
        <?php echo $this->translate('using tag #%s', $this->htmlLink(
          $this->url(array('tag'=>$this->tag), 'article_browse', true),
          $this->tagObject ? $this->tagObject->text : $this->tag
        ));?>
      <?php endif; ?>
      <?php if ($this->search): ?>
        <?php echo $this->translate('with keyword %s', $this->htmlLink(
          $this->url(array('search'=>$this->search), 'article_browse', true),
          $this->search
        ));?>
      <?php endif; ?> 
      <?php if ($this->start_date): $archive_date = Radcodes_Lib_Helper_Date::archive($this->start_date); ?>
        <?php echo $this->translate('on %s', $this->htmlLink(
          $this->url(array('start_date'=>$archive_date['date_start'],'end_date'=>$archive_date['date_end']), 'article_browse', true),
          $this->translate($archive_date['label'])
        ));?> 
      <?php endif; ?>    
      <a href="<?php echo $this->url(array(), 'article_browse', true) ?>">(x)</a>
    </div>
  <?php endif; ?>
  
  
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  
      <h3 class="sep">
        <span>
          <?php if ($this->categoryObject): ?>
            <?php echo $this->translate($this->categoryObject->getTitle()); ?>
          <?php else: ?>  
            <?php echo $this->translate('All Categories'); ?>
          <?php endif; ?>
        </span>
      </h3>  
  
    <ul class="articles_browse">
      <?php foreach( $this->paginator as $item ): ?>
        <li class="<?php if ($item->featured) echo 'articles_browse_entry_featured'; ?> <?php if ($item->sponsored) echo 'articles_browse_entry_sponsored'; ?>">
          <div class='articles_browse_photo'>
            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
          </div>
          <div class='articles_browse_info'>
            <div class='articles_browse_info_title'>
              <h3>
              <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
              <?php if( $item->featured ): ?>
                <img src='application/modules/Article/externals/images/featured.png' class='article_title_icon_featured' />
              <?php endif;?>
              <?php if( $item->sponsored ): ?>
                <img src='application/modules/Article/externals/images/sponsored.png' class='article_title_icon_sponsored' />
              <?php endif;?>
              </h3>
            </div>
            <div class='articles_browse_info_date'>
              <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
              - <?php echo $this->translate('posted by %s', $item->getOwner()->__toString());?>
                - <?php echo $this->translate(array("%s view", "%s views", $item->view_count), $this->locale()->toNumber($item->view_count)); ?>
                - <?php echo $this->translate(array("%s comment", "%s comments", $item->comment_count), $this->locale()->toNumber($item->comment_count)); ?>
                - <?php echo $this->translate(array('%1$s like', '%1$s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>
            </div>
            <div class='articles_browse_info_blurb'>
              <?php // $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($item)?>
              <?php // echo $this->fieldValueLoop($item, $fieldStructure) ?>
              <?php echo $item->getExcerpt(256) ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues
    )); ?>  
    <?php $articleCount = $this->paginator->getTotalItemCount() ?>
    <?php // echo $this->translate(array("%s article found", "%s articles found", $articleCount), ($articleCount)) ?>
    
  <?php elseif( $this->category || $this->show == 1 || $this->search || $this->user_id || $this->tag || $this->start_date):?>
    <div class="tip">
      <span>
        <?php echo $this->translate('Nobody has posted an article with that criteria.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('Be the first to <a href=\'%s\'>post</a> one!', $this->url(array(), 'article_create')); ?>
        <?php endif; ?>
      </span>
    </div>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('Nobody has posted an article yet.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('Be the first to <a href=\'%s\'>post</a> one!', $this->url(array(), 'article_create')); ?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>
  
</div>