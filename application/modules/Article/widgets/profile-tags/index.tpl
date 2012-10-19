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
<div class='article_profile_tags'>
  <ul>
    <li class="article_profile_tags_header"><?php echo $this->translate('Tags:'); ?></li>
    <?php foreach ($this->tagMaps as $tagMap): ?>
      <?php $tag = $tagMap->getTag(); ?>
      <?php if (!empty($tag->text)): ?>
        <li>
          #<?php echo $this->htmlLink(array('route'=>'article_browse', 'tag'=>$tag->tag_id), $tag->text); ?>
        </li>
      <?php endif;?>
    <?php endforeach; ?>
  </ul>
</div>