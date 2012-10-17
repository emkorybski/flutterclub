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

<div class="admin_home_news">
  <h3 class="sep">
    <span><?php echo $this->translate("Radcodes Plugins") ?></span>
  </h3>

	<table class="admin_home_stats">
	  <thead>
	    <tr>
        <th><?php echo $this->translate('Plugi Name')?></th>
        <th><?php echo $this->translate('Latest')?></th>
        <th><?php echo $this->translate('Installed')?></th>
        <th width="100"><?php echo $this->translate('Options'); ?></th>
      </tr>
	  </thead>
	  <tbody>
    <?php if (!empty($this->modules)): ?>
	    <?php foreach ($this->modules as $module): ?>
	      <tr>
	        <td><?php echo $this->htmlLink($module['url'], $module['title'], array('target'=>'_blank'));?></td>
	        <td>v<?php echo $module['latest_version']?></td>
          <td>
            <?php if ($module['installed']): ?>
              v<?php echo $module['installed_version']?>
            <?php else: ?>
              --</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($module['installed']): ?>
              <?php if ($module['upgradable']): ?>
                <?php echo $this->htmlLink('http://www.radcodes.com/shop/index.php?do=account', $this->translate('Upgrade'), array('target'=>'_blank', 'class'=>'plugin_upgrade')); ?>
              <?php else: ?>
                <?php echo $this->htmlLink('http://www.radcodes.com/shop/index.php?do=account', $this->translate('Current'), array('target'=>'_blank', 'class'=>'plugin_current')); ?>
              <?php endif; ?>
            <?php else: ?>
              <?php echo $this->htmlLink($module['url'], $this->translate('Purchase'), array('target'=>'_blank', 'class'=>'plugin_purchase'));?>
            <?php endif; ?>
          </td>
	      </tr>
	    <?php endforeach; ?>
    <?php else: ?>
        <tr>
          <td>Coming soon ..</td>
        </tr>
    <?php endif; ?>
	  </tbody>
	</table>

</div>