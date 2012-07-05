<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 9693 2012-04-19 21:31:22Z pamela $
 * @author     John
 */
?>

<ul>
  <?php foreach( $this->paginator as $post ):
    $user = $post->getOwner();
    $topic = $post->getParent();
    $forum = $topic->getParent();
    ?>
    <li>
      <?php /*
      <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'thumb')) ?>
       *
       */ ?>
      <div class='info'>
        <div class='author'>
          <?php //echo $this->translate('By') ?>
          <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
        </div>
        <div class="parent">
          <?php echo $this->translate('In') ?>
          <?php echo $this->htmlLink($topic->getHref(), $this->translate($topic->getTitle())) ?>
          -
          <?php echo $this->htmlLink($forum->getHref(), $this->translate($forum->getTitle())) ?>
        </div>
        <div class='date'>
          <?php echo $this->timestamp($post->creation_date) ?>
        </div>
      </div>
      <div class='description'>
        <?php echo $this->viewMore(strip_tags($post->getDescription()), 64) ?>
      </div>
    </li>
  <?php endforeach; ?>
</ul>