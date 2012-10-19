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
  $this->headScript()
    ->appendFile($this->baseUrl() . '/externals/moolasso/Lasso.js')
    ->appendFile($this->baseUrl() . '/externals/moolasso/Lasso.Crop.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Observer.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Request.js')
    ->appendFile($this->baseUrl() . '/externals/tagger/tagger.js');
?>

<script type="text/javascript">
  var taggerInstance;
  en4.core.runonce.add(function() {
    taggerInstance = new Tagger('media_photo_next', {
      'title' : '<?php echo $this->translate('ADD TAG');?>',
      'description' : '<?php echo $this->translate('Type a tag or select a name from the list.');?>',
      'createRequestOptions' : {
        'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'add'), 'default', true) ?>',
        'data' : {
          'subject' : '<?php echo $this->subject()->getGuid() ?>'
        }
      },
      'deleteRequestOptions' : {
        'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'remove'), 'default', true) ?>',
        'data' : {
          'subject' : '<?php echo $this->subject()->getGuid() ?>'
        }
      },
      'cropOptions' : {
        'container' : $('media_photo_next')
      },
      'tagListElement' : 'media_tags',
      'existingTags' : <?php echo $this->action('retrieve', 'tag', 'core', array('sendNow' => false)) ?>,
      'suggestParam' : <?php echo $this->action('suggest', 'friends', 'user', array('sendNow' => false, 'includeSelf' => true)) ?>,
      'guid' : <?php echo ( $this->viewer()->getIdentity() ? "'".$this->viewer()->getGuid()."'" : 'false' ) ?>,
      'enableCreate' : <?php echo ( $this->canUpload ? 'true' : 'false') ?>,
      'enableDelete' : <?php echo ( $this->canUpload ? 'true' : 'false') ?>
    });

    // Remove the href attrib while tagging
    var nextHref = $('media_photo_next').get('href');
    taggerInstance.addEvents({
      'onBegin' : function() {
        $('media_photo_next').erase('href');
      },
      'onEnd' : function() {
        $('media_photo_next').set('href', nextHref);
      }
    });
    
  });
</script>

<h2>
  <?php echo $this->article->__toString() ?>
  <?php echo $this->translate('&#187; Photos');?>
</h2>

<div class="article_photos_list_options">
    <?php echo $this->htmlLink(array(
        'route' => 'article_extended',
        'controller' => 'photo',
        'action' => 'list',
        'subject' => $this->article->getGuid(),
      ), $this->translate('View Photos'), array(
        'class' => 'buttonlink icon_article_photo_view'
    )) ?>   
  <?php if( $this->canUpload ): ?>
    
    <?php echo $this->htmlLink(array(
        'route' => 'article_extended',
        'controller' => 'photo',
        'action' => 'manage',
        'subject' => $this->article->getGuid(),
      ), $this->translate('Manage Photos'), array(
        'class' => 'buttonlink icon_article_photo_manage'
    )) ?>  
    <?php echo $this->htmlLink(array(
        'route' => 'article_extended',
        'controller' => 'photo',
        'action' => 'upload',
        'subject' => $this->article->getGuid(),
      ), $this->translate('Upload Photos'), array(
        'class' => 'buttonlink icon_article_photo_new'
    )) ?>
  <?php endif; ?>
</div>


<div class='layout_middle'>
<div class='article_photo_view'>
  <div class="article_photo_nav">
    <div>
      <?php echo $this->translate('Photo');?> <?php echo $this->photo->getCollectionIndex() + 1 ?>
      <?php echo $this->translate('of');?> <?php echo $this->album->count() ?>
    </div>
    <div>
      <?php if ($this->album->count() > 1): ?>
        <?php echo $this->htmlLink($this->photo->getPrevCollectible()->getHref(), $this->translate('Prev')) ?>
        <?php echo $this->htmlLink($this->photo->getNextCollectible()->getHref(), $this->translate('Next')) ?>
      <?php else: ?>
        &nbsp;
      <?php endif; ?>
    </div>
  </div>
  <div class='article_photo_info'>
    <div class='article_photo_container' id='media_photo_div'>
      <a id='media_photo_next'  href='<?php echo $this->photo->getNextCollectible()->getHref() ?>'>
        <?php echo $this->htmlImage($this->photo->getPhotoUrl(), $this->photo->getTitle(), array(
          'id' => 'media_photo'
        )); ?>
      </a>
    </div>
    <br />
    <a></a>
    <?php if( $this->photo->getTitle() ): ?>
      <div class="article_photo_title">
        <?php echo $this->photo->getTitle(); ?>
      </div>
    <?php endif; ?>
    <?php if( $this->photo->getDescription() ): ?>
      <div class="article_photo_description">
        <?php echo nl2br($this->photo->getDescription()); ?>
      </div>
    <?php endif; ?>
    <div class="article_photo_owner">
      <?php echo $this->translate('By')?> <?php echo $this->htmlLink($this->photo->getOwner()->getHref(), $this->photo->getOwner()->getTitle()) ?>
    </div>
    <div class="article_photo_tags" id="media_tags" class="tag_list" style="display: none;">
      <?php echo $this->translate('Tagged:')?>
    </div>
    <div class="article_photo_date">
      <?php echo $this->translate('Added');?> <?php echo $this->timestamp($this->photo->creation_date) ?>
      <?php if( $this->canUpload ): ?>
        - <a href='javascript:void(0);' onclick='taggerInstance.begin();'><?php echo $this->translate('Add Tag');?></a>
        - <?php echo $this->htmlLink(array('route' => 'article_extended', 'controller' => 'photo', 'action' => 'edit', 'photo_id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), $this->translate('Edit'), array('class' => 'smoothbox')) ?>
        - <?php echo $this->htmlLink(array('route' => 'article_extended', 'controller' => 'photo', 'action' => 'delete', 'photo_id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete'), array('class' => 'smoothbox')) ?>
      <?php endif; ?>

      - <?php echo $this->htmlLink(Array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>$this->photo->getType(), 'id'=>$this->photo->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox')); ?>
      - <?php echo $this->htmlLink(Array('module'=>'core', 'controller'=>'report', 'action'=>'create', 'route'=>'default', 'subject'=>$this->photo->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox')); ?>
      - <?php echo $this->htmlLink(array('route' => 'user_extended', 'module' => 'user', 'controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), $this->translate('Make Profile Photo'), array('class' => 'smoothbox')) ?>
    </div>
  </div>

  <?php echo $this->action("list", "comment", "core", array("type"=>"article_photo", "id"=>$this->photo->getIdentity())); ?>
</div>
</div>