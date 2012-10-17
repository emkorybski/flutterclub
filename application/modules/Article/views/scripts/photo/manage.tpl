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

<?php if( true || $this->canUpload ): ?>
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


<?php if( $this->paginator->getTotalItemCount()): ?>

	<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="global_form articles_browse_filters">
	  <div>
	    <div>
	      <h3><?php echo $this->form->getTitle(); ?></h3>
	    
	      <?php $notices = $this->form->getNotices(); ?>
	    
	      <?php if (!empty($notices)): ?>
	        <ul class="form-notices">
	        <?php foreach ($notices as $notice): ?>
	          <li><?php echo $notice; ?></li>
	        <?php endforeach; ?>
	        </ul>
	      <?php endif; ?>
	      <ul class='articles_editphotos'>        
	        <?php foreach( $this->paginator as $photo ): ?>
	          <li>
	            <div class="articles_editphotos_photo">
	              <?php echo $this->itemPhoto($photo, 'thumb.normal')  ?>
	            </div>
	            <div class="articles_editphotos_info">
	              <?php
	                $key = $photo->getGuid();
	                echo $this->form->getSubForm($key)->render($this);
	                //print_r($this->form->getSubForm($key)->getValues());
	              ?>
	              <div class="articles_editphotos_cover">
	                <input type="radio" name="cover" value="<?php echo $photo->file_id ?>" <?php if( $this->article->photo_id == $photo->file_id ): ?> checked="checked"<?php endif; ?> />
	              </div>
	              <div class="articles_editphotos_label">
	                <label><?php echo $this->translate('Main Photo');?></label>
	              </div>
	            </div>
	          </li>
	        <?php endforeach; ?>
	      </ul>
	      <?php echo $this->form->submit->render(); ?>
	      
	    </div>
	  </div>
	</form>

<?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('This article does not have any photos.');?>
          <?php echo $this->translate("Get started by <a href='%1\$s'>uploading</a> a new photo.", $this->url(array('controller' => 'photo',
        'action' => 'upload',
        'subject' => $this->subject()->getGuid()), 'article_extended'));?>
      </span>
    </div>
<?php endif; ?>

<?php // echo $this->form->render($this) ?>