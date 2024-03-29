
<div class="he_friends_widget">
  <div class="inviter_widget_title"><?php echo $this->translate("INVITER_Invite your Twitter followers to join"); ?></div>

  <?php if ($this->show_login): ?>

  <?php $this->headScript()->appendFile('application/modules/Inviter/externals/scripts/core.js'); ?>

  <script type="text/javascript">
    en4.core.runonce.add(function() {
      provider.oauth_url = "<?php echo $this->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'format' => 'smoothbox'), 'default') ?>";
    });
  </script>

  <div class="inviter_widget_title">
    <button class="inviter_twitter_login_btn" onclick="provider.open_connect('twitter');"><?php echo $this->translate("INVITER_Connect with Twitter"); ?></button>
  </div>

  <?php else : ?>

    <div class="he_widget_container">
      <?php echo $this->render('_friends_list.tpl'); ?>
    </div>

    <div class="inviter_widget_title">
      <button onclick="window.location.href = '<?php echo $this->url(array('provider' => 'twitter'), 'inviter_members'); ?>'"><?php echo $this->translate('INVITER_Invite them'); ?></button>
    </div>

  <?php endif; ?>
</div>