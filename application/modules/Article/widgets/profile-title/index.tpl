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
<?php $article = $this->article; ?>
<div class="article_profile_title">
  <h3 class="article_profile_title_sponsored_<?php echo $article->sponsored ? 'yes' : 'no'?>">
    <span class="article_profile_title_featured_<?php echo $article->featured ? 'yes' : 'no'?>"><?php echo $article->getTitle(); ?></span>
  </h3>
</div>
