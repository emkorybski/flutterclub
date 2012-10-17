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
$article = $this->article;
$photoCount = $this->paginator->getTotalItemCount();

/*
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
 */

?>

    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->photosNavigation)
        ->setUlClass('navigation')
        ->render();
    ?>

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