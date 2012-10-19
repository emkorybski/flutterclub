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

<h2>
  <?php echo $this->article->__toString() ?>
  <?php echo $this->translate('&#187; Photos');?>
</h2>


<?php // $this->article->owner_id == $this->viewer->getIdentity() && ?>
<?php if( $this->canUpload ): ?>
  <div class="article_photos_list_options">
    <?php echo $this->htmlLink(array(
        'route' => 'article_extended',
        'controller' => 'photo',
        'action' => 'list',
        'subject' => $this->subject()->getGuid(),
      ), $this->translate('View Photos'), array(
        'class' => 'buttonlink icon_article_photo_view'
    )) ?>   
    <?php echo $this->htmlLink(array(
        'route' => 'article_extended',
        'controller' => 'photo',
        'action' => 'manage',
        'subject' => $this->subject()->getGuid(),
      ), $this->translate('Manage Photos'), array(
        'class' => 'buttonlink icon_article_photo_manage'
    )) ?>  
    <?php echo $this->htmlLink(array(
        'route' => 'article_extended',
        'controller' => 'photo',
        'action' => 'upload',
        'subject' => $this->subject()->getGuid(),
      ), $this->translate('Upload Photos'), array(
        'class' => 'buttonlink icon_article_photo_new'
    )) ?>
  </div>
<?php endif; ?>

<div class='layout_middle'>
<?php if ($this->paginator->count()): ?>
  <?php if( $this->paginator->count() > 0 ): ?>
    <?php echo $this->paginationControl($this->paginator); ?>
    <br />
  <?php endif; ?>
  <ul class="thumbs thumbs_nocaptions">
    <?php foreach( $this->paginator as $photo ): ?>
      <li>
        <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
          <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
        </a>
      </li>
    <?php endforeach;?>
  </ul>
  <?php if( $this->paginator->count() > 0 ): ?>
    <?php echo $this->paginationControl($this->paginator); ?>
    <br />
  <?php endif; ?>
<?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('This article does not have any photos.');?>
        <?php if ($this->canUpload): ?>
          <?php echo $this->translate("Get started by <a href='%1\$s'>uploading</a> a new photo.", $this->url(array('controller' => 'photo', 'action' => 'upload', 'subject' => $this->subject()->getGuid()), 'article_extended'));?>
        <?php endif; ?>
      </span>
    </div>
<?php endif; ?>  
</div>