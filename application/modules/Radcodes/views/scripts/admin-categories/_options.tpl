<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>
<div class="radcodes_categories_options">
<?php echo $this->htmlLink(
  $this->url(array('action'=>'add')),
  $this->translate("Add New Category"),
  array('class' => 'smoothbox buttonlink icon_radcodes_category_new')
); ?>
<?php echo $this->htmlLink(
  $this->url(array('action'=>'move')),
  $this->translate("Move Category"),
  array('class' => 'smoothbox buttonlink icon_radcodes_category_move')
); ?>
</div>