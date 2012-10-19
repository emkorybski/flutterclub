<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-09-07 17:53 michael $
 * @author     Michael
 */

?>

<?php

$this->headTranslate(array(
  'hecore_Featured Members',
  'Friends'
));

?>

<script type="text/javascript">

function displayFeatureds(){

  he_list.box(
    'hecore',
    'getFeatureds',
    en4.core.language.translate('hecore_Featured Members'),
    {list_title2: en4.core.language.translate('Friends')}
  );
}

</script>

<h3><?php echo $this->translate("hecore_Featured Members")?> <span>(<?php echo $this->total?>)</span>

  <?php if ($this->total > $this->count_items): ?>
    <a href="javascript:void(0);" onclick="displayFeatureds();"><?php echo $this->translate("See All"); ?></a>
  <?php endif; ?>

  <br class="clr" />

</h3>

<div class="list">
    <?php foreach ($this->paginator as $featured): ?>
      <div class="item">
        <?php echo $this->htmlLink($featured->getHref(), $this->itemPhoto($featured, 'thumb.icon'), array('class' => 'featured_profile_thumb')); ?>
        <?php echo $this->htmlLink($featured->getHref(), $featured->getTitle(), array('class' => 'featured_profile_title')); ?>
      </div>
    <?php endforeach; ?>
  <div class="clr"></div>
</div>