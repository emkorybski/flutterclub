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

<h2><?php echo $this->translate("Radcodes Core Library") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

  <div class='clear'>
    <div class='settings'>
      <ul>
        <li><?php echo $this->htmlLink($this->url(array('action'=>'delete-page')),
          $this->translate("Delete Content Page")
        ); ?></li>
        <li><?php echo $this->htmlLink($this->url(array('action'=>'run-installer-function')),
          $this->translate("Execute Installer Function")
        ); ?></li>
      </ul>
      <?php //echo $this->form->render($this); ?>

    </div>
  </div>
     