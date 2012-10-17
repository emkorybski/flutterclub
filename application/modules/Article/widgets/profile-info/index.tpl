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
//$category = $article->getCategory();
?>

<div class='article_profile_info'>
  <ul>   
    <li><?php echo $this->translate('Posted by %s', $article->getOwner()->toString()); ?></li>
    <li><?php echo $this->locale()->toDateTime($article->creation_date);?>
    <li><?php echo $this->translate(array('%s comment', '%s comments', $article->comment_count), $this->locale()->toNumber($article->comment_count)); ?></li>   
    <li><?php echo $this->translate(array('%s view', '%s views', $article->view_count), $this->locale()->toNumber($article->view_count)); ?></li>  
  </ul>
</div>
