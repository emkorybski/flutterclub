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
<?php if ($this->article->owner_id != $this->viewer->getIdentity() && !$this->article->isPublished()): ?>
<div id="global_content">   
    <div class="tip">
      <span>
        <?php echo $this->translate('This article has not been published yet.'); ?>
      </span>
    </div>
  <a onclick="history.go(-1);" href="javascript:void(0);" class="buttonlink icon_back"><?php echo $this->translate('Go Back'); ?></a>
</div>

<?php return; ?>
<?php endif;?>

<script type="text/javascript">
  var categoryAction =function(category){
    $('category').value = category;
    $('filter_form').submit();
  }
  var tagAction =function(tag){
    $('tag').value = tag;
    $('filter_form').submit();
  }
  var dateAction =function(start_date, end_date){
    $('start_date').value = start_date;
    $('end_date').value = end_date;
    $('filter_form').submit();
  }
</script>

<div class='layout_right'>
  <div class='articles_gutter'>
  
    <div class='articles_gutter_owner'>
	    <?php echo $this->htmlLink($this->user($this->article->owner_id)->getHref(), $this->itemPhoto($this->user($this->article->owner_id), 'thumb.profile'), array('class'=>'articles_gutter_owner_photo')) ?>
      <div class='articles_gutter_owner_name'>
        <?php echo $this->htmlLink($this->user($this->article->owner_id)->getHref(), $this->user($this->article->owner_id)->getTitle(), array('class'=>'articles_gutter_name')); ?>      
	      <span class='articles_gutter_owner_date'><?php echo $this->translate('updated'); ?> <?php echo $this->timestamp($this->article->modified_date) ?></span>
	    </div>
    </div>  
  
    <ul class='articles_gutter_options'>
      <?php if($this->paginator->getTotalItemCount() && false): ?>
      <li>
          <?php echo $this->htmlLink(array(
              'route' => 'article_extended',
              'controller' => 'photo',
              'action' => 'list',
              'subject' => $this->article->getGuid(),
            ), $this->translate('View Article Photos'), array(
              'class' => 'buttonlink icon_article_photo_view'
          )) ?>
      </li>
      <?php endif;?>
      
      <?php if ($this->canPublish && !$this->approval):?>
      <li>
        <a href='<?php echo $this->url(array('article_id' => $this->article->article_id), 'article_publish', true) ?>' class='buttonlink icon_article_publish'><?php echo $this->translate('Publish Article');?></a>
      </li>
      <?php endif; ?>
      
      <?php if( $this->canUpload ): ?>
      <li>
          <?php echo $this->htmlLink(array(
              'route' => 'article_extended',
              'controller' => 'photo',
              'action' => 'upload',
              'subject' => $this->article->getGuid(),
            ), $this->translate('Add Photos'), array(
              'class' => 'buttonlink icon_article_photo_new'
          )) ?>
      </li>
      <?php endif; ?>
      <?php if( $this->canDelete ): ?>
        <li>
          <a href='<?php echo $this->url(array('article_id' => $this->article->article_id), 'article_delete', true) ?>' class='buttonlink icon_article_delete'><?php echo $this->translate('Delete Article');?></a>
        </li>
      <?php endif; ?>

    </ul>

    <form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array('user'=>$this->article->owner_id), 'article_browse', true) ?>' style='display: none;'>
      <input type="hidden" id="tag" name="tag" value=""/>
      <input type="hidden" id="category" name="category" value=""/>
      <input type="hidden" id="start_date" name="start_date" value="<?php if ($this->start_date) echo $this->start_date;?>"/>
      <input type="hidden" id="end_date" name="end_date" value="<?php if ($this->end_date) echo $this->end_date;?>"/>
    </form>

    <?php if (count($this->userCategories )):?>
      <h4><?php echo $this->translate('Categories');?></h4>
      <ul>
          <li><a href='<?php echo $this->url(array('user' => $this->article->owner_id), 'article_browse', true); ?>'><?php echo $this->translate('All Categories');?></a></li>
          <?php foreach ($this->userCategories as $category): ?>
            <li> <a href='javascript:void(0);' onclick='javascript:categoryAction(<?php echo $category->category_id?>);'><?php echo $this->translate($category->category_name) ?></a></li>
          <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <?php
    $this->tagstring = "";
    if (count($this->userTags )){
      foreach ($this->userTags as $tag){
        if (!empty($tag->text)){
          $this->tagstring .= " <a href='javascript:void(0);'onclick='javascript:tagAction({$tag->tag_id})' >#$tag->text</a> ";
        }
      }
    }
    ?>

    <?php if ($this->tagstring ):?>
      <h4><?php echo $this->translate('%1$s\'s Tags', $this->user($this->article->owner_id)->getTitle())?></h4>
      <ul>
        <?php echo $this->tagstring;?>
      </ul>
    <?php endif; ?>

    <?php if (count($this->archive_list )):?>
      <h4><?php echo $this->translate('Archives');?></h4>
      <ul>
        <?php foreach ($this->archive_list as $archive): ?>
        <li>
          <a href='javascript:void(0);' onclick='javascript:dateAction(<?php echo $archive['date_start']?>, <?php echo $archive['date_end']?>);' <?php if ($this->start_date==$archive['date_start']) echo " style='font-weight: bold;'";?>><?php echo $this->translate($archive['label'])?></a>
          <?php //echo $archive['count']?>
        </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

  </div>
</div>

<div class='layout_middle'>

  <?php if (!$this->article->isPublished()): ?>
    <div class="tip">
      <span>
      	<?php if ($this->approval): ?>
          <?php echo $this->translate('This article is in Draft mode. Administrator will review and manually publish it.'); ?>
      	<?php else: ?>
          <?php echo $this->translate('No one will be able to view this article until you <a href=\'%1$s\'>publish</a> it.', $this->url(array('article_id' => $this->article->article_id), 'article_publish', true)); ?>
      	<?php endif; ?>
      </span>
    </div>
  <?php endif; ?>
  <h2>
    <?php echo $this->htmlLink($this->url(array(),'article_browse',true), $this->translate('Browse Articles')); ?>
    <?php if ($this->category):?>
      &raquo; <?php echo $this->htmlLink($this->url(array('category' => $this->category->category_id), 'article_browse'), $this->translate($this->category->category_name)); ?>
    <?php endif; ?>
    <?php // echo $this->translate('%1$s\'s Article', $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()))?>
  </h2>
  <ul class='articles_entrylist<?php if ($this->article->featured):?> articles_entrylist_featured<?php endif;?><?php if ($this->article->sponsored):?> articles_entrylist_sponsored<?php endif;?>'>
    <li>
      <h3>
        <?php echo $this->article->getTitle() ?>
        <?php if( $this->article->featured ): ?>
          <img src='application/modules/Article/externals/images/featured.png' class='article_title_icon_featured' />
        <?php endif;?>
        <?php if( $this->article->sponsored ): ?>
          <img src='application/modules/Article/externals/images/sponsored.png' class='article_title_icon_sponsored' />
        <?php endif;?>
      </h3>

      <div class="article_entrylist_entry_date">
        <?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($this->article->getParent(), $this->article->getParent()->getTitle()) ?>
        <?php echo $this->timestamp($this->article->creation_date) ?>
        - <?php echo $this->translate(array("%s view", "%s views", $this->article->view_count), $this->article->view_count); ?>
        <?php if ($this->category):?>- <?php echo $this->translate('Filed in');?> <a href='javascript:void(0);' onclick='javascript:categoryAction(<?php echo $this->category->category_id?>);'><?php echo $this->translate($this->category->category_name) ?></a> <?php endif; ?>
        <?php if (count($this->articleTags )):?>
        -
          <?php foreach ($this->articleTags as $tag): ?>
          <?php if (!empty($tag->getTag()->text)):?>
            <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text?></a>&nbsp;
          <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      

      <?php if ($this->settings('article.showmainphoto', 0) && $this->main_photo): ?>
      <div class="article_entrylist_entry_photo">
      	<a href="<?php echo $this->main_photo->getHref(); ?>"><?php echo $this->itemPhoto($this->article, 'thumb.profile') ?></a>
      </div>
      <?php endif; ?>
      
      <div class="article_entrylist_entry_body">
        <?php echo $this->article->body ?>
      </div>
      
      <div style="clear: both"></div>
      
      <?php if ($article_field_values = $this->fieldValueLoop($this->article, $this->fieldStructure)): ?>
      <div class="profile_fields">
        <h4>
          <span><?php echo $this->translate('Article Details');?></span>
        </h4>
      	<?php echo $article_field_values; ?>
      </div>
      <?php endif; ?>
      
      <?php $photoCount = $this->paginator->getTotalItemCount(); ?>
      <?php if ($photoCount): ?>
      <div class="article_entrylist_entry_photos">
        <h4>
          <span><?php echo $this->translate('Article Album'); ?>
          (<?php echo $this->htmlLink(array(
              'route' => 'article_extended',
              'controller' => 'photo',
              'action' => 'list',
              'subject' => $this->article->getGuid(),
            ), $this->translate(array("%s photo", "%s photos", $photoCount), $photoCount), array(
          )) ?>)
          </span> 
        </h4>
			  <ul class="thumbs thumbs_nocaptions">
			    <?php foreach( $this->paginator as $photo ): ?>
			      <?php // if($this->article->photo_id != $photo->file_id):?>
				      <li>
				        <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
				          <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
				        </a>
				      </li>
			      <?php // endif; ?>
			    <?php endforeach;?>
			  </ul>
	    </div>
	    <?php endif; ?>
     
      <?php if (!empty($this->relatedArticles)): ?>
        <div class="article_related_articles">
          <h4>
            <span>
              <?php echo $this->translate('Related Articles')?>
            </span> 
          </h4>
          <ul>
            <?php foreach ($this->relatedArticles as $article): ?>
              <li>
                <?php echo $this->htmlLink($article->getHref(), $this->itemPhoto($article, 'thumb.normal'), array('class'=>'article_photo'))?>
                <?php echo $article->__toString(); ?>
              </li>
            <?php endforeach;?>
          </ul>
        </div>
      <?php endif; ?>
      
      <div class="article_tool_links">
        <?php echo $this->htmlLink(Array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'article', 'id' => $this->article->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox')); ?>
              &nbsp;|&nbsp;                           
        <?php echo $this->htmlLink(Array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' =>  $this->article->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox')); ?>
      </div>
     	  
    </li>
  </ul>
  <?php echo $this->action("list", "comment", "core", array("type"=>"article", "id"=>$this->article->getIdentity())) ?>
</div>