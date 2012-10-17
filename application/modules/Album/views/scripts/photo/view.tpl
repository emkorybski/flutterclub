<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John Boehr <j@webligo.com>
 */
?>

<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/tagger/tagger.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Album/externals/scripts/core.js');
  $this->headTranslate(array(
    'Save', 'Cancel', 'delete',
  ));
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    var descEls = $$('.albums_viewmedia_info_caption');
    if( descEls.length > 0 ) {
      descEls[0].enableLinks();
    }

    var taggerInstance = window.taggerInstance = new Tagger('media_photo_next', {
      'title' : '<?php echo $this->string()->escapeJavascript($this->translate('ADD TAG'));?>',
      'description' : '<?php echo $this->string()->escapeJavascript($this->translate('Type a tag or select a name from the list.'));?>',
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
      'existingTags' : <?php echo Zend_Json::encode($this->tags) ?>,
      'suggestProto' : 'request.json',
      'suggestParam' : "<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest', 'includeSelf' => true), 'default', true) ?>",
      'guid' : <?php echo ( $this->viewer()->getIdentity() ? "'".$this->viewer()->getGuid()."'" : 'false' ) ?>,
      'enableCreate' : <?php echo ( $this->canTag ? 'true' : 'false') ?>,
      'enableDelete' : <?php echo ( $this->canUntagGlobal ? 'true' : 'false') ?>
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
    
    var keyupEvent = function(e) {
      if( e.target.get('tag') == 'html' ||
          e.target.get('tag') == 'body' ) {
        if( e.key == 'right' ) {
          $('photo_next').fireEvent('click', e);
          //window.location.href = "<?php echo ( $this->nextPhoto ? $this->nextPhoto->getHref() : 'window.location.href' ) ?>";
        } else if( e.key == 'left' ) {
          $('photo_prev').fireEvent('click', e);
          //window.location.href = "<?php echo ( $this->previousPhoto ? $this->previousPhoto->getHref() : 'window.location.href' ) ?>";
        }
      }
    }
    window.addEvent('keyup', keyupEvent);
    
    // Add shutdown handler
    en4.core.shutdown.add(function() {
      window.removeEvent('keyup', keyupEvent);
    });
  });
</script>



<h2>
  <?php echo $this->translate('%1$s\'s Album: %2$s', $this->album->getOwner()->__toString(), $this->htmlLink($this->album, $this->album->getTitle())); ?>
</h2>

<?php if (""!=$this->album->getDescription()): ?>
  <p class="photo-description">
    <?php echo $this->album->getDescription() ?>
  </p>
<?php endif ?>

<div class="layout_middle">
<div class='albums_viewmedia'>
  <?php if( !$this->message_view): ?>
  <div class="albums_viewmedia_nav">
    <div>
      <?php echo $this->translate('Photo %1$s of %2$s in %3$s',
          $this->locale()->toNumber($this->photo->getPhotoIndex() + 1),
          $this->locale()->toNumber($this->album->count()),
          (string) $this->album->getTitle()) ?>
    </div>
    <?php if( $this->album->count() > 1 ): ?>
    <div>
      <?php echo $this->htmlLink(( $this->previousPhoto ? $this->previousPhoto->getHref() : null ), $this->translate('Prev'), array('id' => 'photo_prev')) ?>
      <?php echo $this->htmlLink(( $this->nextPhoto ? $this->nextPhoto->getHref() : null ), $this->translate('Next'), array('id' => 'photo_next')) ?>
    </div>
    <?php endif ?>
  </div>
  <?php endif ?>
  <div class='albums_viewmedia_info'>
    <div class='album_viewmedia_container' id='media_photo_div'>
      <a id='media_photo_next'  href='<?php echo $this->nextPhoto ? $this->escape($this->nextPhoto->getHref()) : '#' ?>'>
        <?php echo $this->htmlImage($this->photo->getPhotoUrl(), $this->photo->getTitle(), array(
          'id' => 'media_photo'
        )); ?>
      </a>
    </div>
    <br />
    <a></a>
    <?php if( $this->photo->getTitle() ): ?>
      <div class="albums_viewmedia_info_title">
        <?php echo $this->photo->getTitle(); ?>
      </div>
    <?php endif; ?>
    <?php if( $this->photo->getDescription() ): ?>
      <div class="albums_viewmedia_info_caption">
        <?php echo nl2br($this->photo->getDescription()) ?>
      </div>
    <?php endif; ?>
    <div class="albums_viewmedia_info_tags" id="media_tags" style="display: none;">
      <?php echo $this->translate('Tagged:') ?>
    </div>
    
    <div class="albums_viewmedia_info_footer">
      <div class="albums_viewmedia_info_date">
        <?php echo $this->translate('Added %1$s', $this->timestamp($this->photo->modified_date)) ?>
        <?php if ($this->viewer()->getIdentity()):?>
        <?php if( $this->canTag ): ?>
          - <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Add Tag'), array('onclick'=>'taggerInstance.begin();')) ?>
        <?php endif; ?>
        <?php if( $this->canEdit ): ?>
          - <?php echo $this->htmlLink(array('reset' => false, 'action' => 'edit'), $this->translate('Edit'), array('class' => 'smoothbox')) ?>
        <?php endif; ?>
        <?php if( $this->canDelete ): ?>
          - <?php echo $this->htmlLink(array('reset' => false, 'action' => 'delete'), $this->translate('Delete'), array('class' => 'smoothbox')) ?>
        <?php endif; ?>
        <?php if( !$this->message_view ):?>
        - <?php echo $this->htmlLink(Array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'album_photo', 'id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox')); ?>
        - <?php echo $this->htmlLink(Array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox')); ?>
        - <?php echo $this->htmlLink(array('route' => 'user_extended', 'controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), $this->translate('Make Profile Photo'), array('class' => 'smoothbox')) ?>
        <?php endif;?>
        <?php endif ?>
      </div>
      <?php if( $this->canEdit ): ?>
      <div class="albums_viewmedia_info_actions">
        <a class="buttonlink icon_photos_rotate_ccw" href="javascript:void(0)" onclick="$(this).set('class', 'buttonlink icon_loading');en4.album.rotate(<?php echo $this->photo->getIdentity() ?>, 90).addEvent('complete', function(){ this.set('class', 'buttonlink icon_photos_rotate_ccw') }.bind(this));">&nbsp;</a>
        <a class="buttonlink icon_photos_rotate_cw" href="javascript:void(0)" onclick="$(this).set('class', 'buttonlink icon_loading');en4.album.rotate(<?php echo $this->photo->getIdentity() ?>, 270).addEvent('complete', function(){ this.set('class', 'buttonlink icon_photos_rotate_cw') }.bind(this));">&nbsp;</a>
        <a class="buttonlink icon_photos_flip_horizontal" href="javascript:void(0)" onclick="$(this).set('class', 'buttonlink icon_loading');en4.album.flip(<?php echo $this->photo->getIdentity() ?>, 'horizontal').addEvent('complete', function(){ this.set('class', 'buttonlink icon_photos_flip_horizontal') }.bind(this));">&nbsp;</a>
        <a class="buttonlink icon_photos_flip_vertical" href="javascript:void(0)" onclick="$(this).set('class', 'buttonlink icon_loading');en4.album.flip(<?php echo $this->photo->getIdentity() ?>, 'vertical').addEvent('complete', function(){ this.set('class', 'buttonlink icon_photos_flip_vertical') }.bind(this));">&nbsp;</a>
      </div>
      <?php endif ?>
    </div>
  </div>

</div>
</div>