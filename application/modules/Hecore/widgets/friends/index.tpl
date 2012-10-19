<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-09-07 17:53 idris $
 * @author     Idris
 */

?>

<?php if ($this->viewer->isSelf($this->subject)): ?>
  <script type="text/javascript">
    he_friend.url.change_ipp = "<?php echo $this->url(array('action' => 'change-ipp'), 'hecore_friend'); ?>";
    he_friend.url.change_privacy = "<?php echo $this->url(array('action' => 'change-privacy'), 'hecore_friend'); ?>";
    he_friend.url.save_list = "<?php echo $this->url(array('action' => 'save-friends'), 'hecore_friend'); ?>";
    en4.core.runonce.add(function(){
      he_friend.init();
    });
  </script>
<?php endif; ?>

<div class="he_widget">
  <div class="he_friends_widget">
    <div class="he_widget_header">
      <div class="he_widget_see_all float_right_rtl_force">
        <?php echo $this->htmlLink('javascript:he_friend.see_all("all")', $this->translate('See All'), array('class' => 'he_see_all', 'id' => 'he_friend_see_all')); ?>
        (<?php echo $this->friends->getTotalItemCount(); ?>)
      </div>
      <?php if ($this->viewer->isSelf($this->subject)): ?>
        <div class="options float_left_rtl_force">
          <div class="toggler_wrapper">
            <?php echo $this->htmlLink('javascript:void(0)', '', array('class' => 'config', 'id' => 'settings_toggler')); ?>
          </div>
          <div class="config_form hidden" id="settings_form">
            <div class="config_header"><?php echo $this->translate('Settings'); ?></div>
            <div class="config_container">
              <div class="config_select_wrapper">
                <span class="label"><?php echo $this->translate('Show'); ?></span>
                <select id="friends_ipp" name="ipp">
                  <option <?php if ($this->ipp == 3): ?> selected <?php endif; ?> value="3">3</option>
                  <option <?php if ($this->ipp == 6): ?> selected <?php endif; ?> value="6">6</option>
                  <option <?php if ($this->ipp == 9): ?> selected <?php endif; ?> value="9">9</option>
                  <option <?php if ($this->ipp == 12): ?> selected <?php endif; ?> value="12">12</option>
                </select>
                <span class="label"><?php echo $this->translate('friends'); ?>.</span>
              </div>
              <?php if (!empty($this->privacy_list)): ?>
              <div class="config_select_wrapper">
                <span class="label"><?php echo $this->translate('Privacy'); ?></span>
                <select id="friends_privacy" name="privacy">
                  <?php foreach ($this->privacy_list as $privacy): ?>
                    <option <?php if ($this->privacy == $privacy): ?> selected <?php endif; ?> value="<?php echo $privacy; ?>"><?php echo $this->translate($this->privacy_labels[$privacy]); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <?php endif; ?>
              <?php if ($this->isListing): ?>
                <div class="config_display_wrapper">
                  <div class="config_display">
                    <?php echo $this->htmlLink('javascript:void(0)', $this->translate('Choose wich of your friends will be always displayed on this widget.'), array('id' => 'friend_list')); ?>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
      <div class="clr"></div>
    </div>
    <div class="clr"></div>
    <div class="he_widget_container" id="he_friend_conatainer">
      <?php echo $this->render('_friends_list.tpl'); ?>
    </div>
    <div class="he_friends_loader hidden" id="he_friends_loader"></div>
    <div class="clr"></div>
  </div>
</div>