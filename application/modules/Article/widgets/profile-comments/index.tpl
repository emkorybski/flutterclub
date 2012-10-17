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

<div class='article_profile_comments'>
  <a name="comments"></a>
  <?php echo $this->action("list", "comment", "core", array("type"=>"article", "id"=>$this->article->getIdentity())) ?>  
</div>