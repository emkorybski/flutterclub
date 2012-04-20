<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: janrain.tpl 9557 2011-12-16 23:55:36Z john $
 * @author     John Boehr <john@socialengine.com>
 */
?>

<?php
  echo $this->navigation()
    ->menu()
    ->setContainer($this->navigation)
    ->setUlClass('admin_friends_tabs')
    ->render()
?>

<div class='settings'>
  <?php echo $this->form->render($this) ?>
</div>