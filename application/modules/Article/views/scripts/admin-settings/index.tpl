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

<h2><?php echo $this->translate("Articles Plugin") ?></h2>

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

  
      <?php if ($this->notice == 'license'): ?>
        <div class="tip">
          <span>
            <?php echo $this->translate('Please enter your license key associated with this plugin.');?>
          </span>
        </div>
      <?php endif; ?>

      <?php echo $this->form->render($this); ?>

    </div>
  </div>
     