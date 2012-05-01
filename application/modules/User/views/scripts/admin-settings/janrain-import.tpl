<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: janrain-import.tpl 9577 2012-01-05 22:21:19Z john $
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

<?php if( $this->notice ): ?>
  <div class="tip">
    <span>
      <?php echo $this->notice ?>
    </span>
  </div>
<?php else: ?>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
<?php endif ?>