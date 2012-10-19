<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Socialengineaddon
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: information.tpl (var) 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2>
  <?php echo $this->translate('YouNet Developments License') ?>
</h2>
<?php if( count($this->navigation) ): ?>
<div class='tabs'>
  <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>
<div style="overflow-y: scroll;height:400px">
    <?php echo $this->license;?>
</div>