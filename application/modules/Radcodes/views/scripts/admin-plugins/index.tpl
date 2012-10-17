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
    <?php if (!empty($this->modules)): ?>
    <table class="admin_table" id="radcodes_plugins">
      <thead>
        <tr>
          <th><?php echo $this->translate('Plugin Name')?></th>
          <th><?php echo $this->translate('Latest')?></th>
          <th><?php echo $this->translate('Installed')?></th>
          <th width="100"><?php echo $this->translate('Options'); ?></th>
          <th><?php echo $this->translate('Review')?></th>
        </tr>
      </thead>
      <tbody>
	    <?php foreach ($this->modules as $type => $module): ?>
        <tr>
          <th>
            <img src="http://www.radcodes.com/images/showcase/icons/<?php echo $type; ?>.png" class="plugin_icon" />
            <div class="plugin_title"><?php echo $this->htmlLink($module['url'], $module['title'], array('target'=>'_blank'));?></div>
            <div class="plugin_desc">
              <?php echo $this->viewMore($module['description']); ?>
            </div>
          </th>
          <td>
            v<?php echo $module['latest_version']?>
          </td>
          <td>
            <?php if ($module['installed']): ?>
              v<?php echo $module['installed_version']?>
            <?php else: ?>
              <span class="plugin_not_installed"><?php echo $this->translate('not installed')?></span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($module['installed']): ?>
              <?php if ($module['upgradable']): ?>
                <?php echo $this->htmlLink('http://www.radcodes.com/shop/index.php?do=account', $this->translate('Upgrade Now'), array('target'=>'_blank', 'class'=>'plugin_upgrade')); ?>
              <?php else: ?>
                <?php echo $this->htmlLink('http://www.radcodes.com/shop/index.php?do=account', $this->translate('Up-To-Date'), array('target'=>'_blank', 'class'=>'plugin_current')); ?>
              <?php endif; ?>
            <?php else: ?>
              <?php echo $this->htmlLink($module['url'], $this->translate('Purchase Plugin'), array('target'=>'_blank', 'class'=>'plugin_purchase'));?>
            <?php endif; ?>
          </td>
          <td>
            <?php echo $this->htmlLink($module['review_url'], $this->translate('Write Review'), array('target'=>'_blank', 'class'=>'plugin_review'));?>
          </td>
        </tr>
	    <?php endforeach; ?>
      </tbody>
    <?php endif; ?>
    </table>
  </div>
</div>
     