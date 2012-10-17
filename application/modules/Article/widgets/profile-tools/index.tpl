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
<div class='article_profile_tools'>
  <?php echo $this->htmlLink(Array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'article', 'id' => $this->article->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'buttonlink icon_article_share smoothbox')); ?>
  <?php echo $this->htmlLink(Array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' =>  $this->article->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'buttonlink icon_article_report smoothbox')); ?>
</div>