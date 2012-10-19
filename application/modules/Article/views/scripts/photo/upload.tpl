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

<?php echo $this->form->render($this) ?>