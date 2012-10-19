<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _hecore_licenses.tpl 2010-07-02 17:53 michael $
 * @author     Michael
 */
?>

<?php if (count($this->modules)):?>

<form method="post" action="<?php echo $this->url()?>" onsubmit="hecore_license.edit(this);return false;">

  <div class="hecore_licenses">

  <?php foreach ($this->modules as $module):?>

      <div class="item">
        <div class="title"><?php echo $module['title']?></div>
        <div class="license<?php if ($module['is_valid']):?> valid<?php else:?> invalid<?php endif?>">
          <input type="text" name="license[<?php echo $this->checkModuleName($module['name'])?>]"
                 value="<?php echo $module['key'];?>"
              />
        </div>
        <div style="clear:both;"></div>
      </div>

  <?php endforeach;?>

  </div>

  <br />

  <button type="submit">
    <?php echo $this->translate('HECORE_LICENSES_SAVE')?>
  </button>

</form>

<?php else:?>

    <div class="tip"><span><?php echo $this->translate('HECORE_NOLICENSES')?></span></div>

<?php endif?>

