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
$category = $article->getCategory();
?>

<div class='article_profile_breadcrumb'>
  <ul>
	<li><?php echo $this->htmlLink(array('route' => 'article_home'),
		  $this->translate('Article Home Page')
		);?> &raquo;</li>
	<li><?php echo $this->htmlLink(array('route' => 'article_general', 'action' => 'browse'),
		  $this->translate('Browse Articles')
		);?> &raquo;</li>
    <?php if ($category->hasParentCategory() and ($parentCategory = $category->getParentCategory())): ?>
      <li>
        <?php echo $this->htmlLink($parentCategory->getHref(), $this->translate($parentCategory->getTitle()))?>
         &raquo;
      </li>
    <?php endif; ?>		
	<li><?php echo $this->htmlLink($category->getHref(), $this->translate($category->getTitle()))?></li>
  </ul>
</div>
