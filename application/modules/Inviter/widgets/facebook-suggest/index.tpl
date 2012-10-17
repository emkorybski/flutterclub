
<?php
  $this->headScript()->appendFile('application/modules/Inviter/externals/scripts/core.js');
  $ajaxRequestUrl = $this->url(array('module' => 'inviter', 'controller' => 'introduce', 'action' => 'ajax-request'), 'default', true);
  $this->headScript()->appendScript('en4.core.runonce.add(function() {InviterIntroduce.url = ' . $this->jsonInline($ajaxRequestUrl) . ';});');
?>

<div class="he_friends_widget">
  <div class="inviter_widget_title"><?php echo $this->translate("INVITER_Invite your Facebook friends to join"); ?></div>

  <?php if ($this->show_login): ?>

  <script type="text/javascript">
    en4.core.runonce.add(function() {
      provider.oauth_url = "<?php echo $this->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'format' => 'smoothbox'), 'default') ?>";
    });
  </script>

  <div class="inviter_widget_title">
    <button class="inviter_facebook_login_btn" onclick="provider.open_connect('facebook');"><?php echo $this->translate("INVITER_Connect with Facebook"); ?></button>
  </div>

  <?php else : ?>

    <div class="he_widget_container">
      <?php echo $this->render('_friends_list.tpl'); ?>
    </div>

    <?php echo $this->render('application/modules/Inviter/views/scripts/_providers_settings.tpl'); ?>
    <div class="inviter_widget_title">
      <button
          onclick="
              inviter.send_to_fb('');
              //window.location.href = '<?php //echo $this->url(array('provider' => 'facebook'), 'inviter_members'); ?>
              "><?php echo $this->translate('INVITER_Invite them'); ?></button>
    </div>

  <?php endif; ?>
</div>