<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2010-07-02 17:53 ermek $
 * @author     Ermek
 */
?>


<div class="headline">
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
				->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<?php echo $this->render('_providers/' . $this->invitation->provider . '_invitation.tpl'); ?>

