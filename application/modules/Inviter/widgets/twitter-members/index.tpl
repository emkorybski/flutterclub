
<?php
  $this->headScript()->appendFile('application/modules/Inviter/externals/scripts/core.js');
  $ajaxRequestUrl = $this->url(array('module' => 'inviter', 'controller' => 'introduce', 'action' => 'ajax-request'), 'default', true);
  $this->headScript()->appendScript('en4.core.runonce.add(function() {InviterIntroduce.url = ' . $this->jsonInline($ajaxRequestUrl) . ';});');
  $this->headTranslate(array('Choose Friends'));
?>

<div class="he_friends_widget">
  <div class="inviter_widget_title"><?php echo $this->translate("INVITER_You're friended on Twitter, connect with them here!"); ?></div>

  <?php if ($this->show_login): ?>

  <script type="text/javascript">
    en4.core.runonce.add(function() {
      provider.oauth_url = "<?php echo $this->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'format' => 'smoothbox'), 'default') ?>";
    });
  </script>

  <div class="inviter_widget_title">
    <button class="inviter_twitter_login_btn" onclick="provider.open_connect('twitter');"><?php echo $this->translate("INVITER_Connect with Twitter"); ?></button>
  </div>

  <?php else : ?>

    <script type="text/javascript">
      var fbMembersTips = null;
      en4.core.runonce.add(function(){
        var options = {
          url: '<?php echo $this->url( array('module' => 'inviter', 'controller' => 'facebook', 'action' => 'user-content'), 'default' ); ?>',
          delay: 300,
          onShow: function(tip, element){
            var miniTipsOptions = {
              'htmlElement': '.he-hint-text',
              'delay': 1,
              'className': 'he-tip-mini',
              'id': 'he-mini-tool-tip-id',
              'ajax': false,
              'visibleOnHover': false
            };

            fbMembersTips = new HETips($$('.facebook_members_tip .he-hint-tip-links'), miniTipsOptions);
            Smoothbox.bind();
          }
        };
        var $thumbs = $$('.inviter_twitter_members .inviter_facebook_member');
        var $fb_members_tips = new HETips($thumbs, options);
      });
    </script>

    <div class="he_widget_container inviter_twitter_members">
      <?php $counter = 0; ?>
      <?php foreach ($this->members['se_users'] as $item): ?>
      <?php if ($counter % 3 == 0): ?><div class="row"><?php endif; ?>
        <?php
          $fb_item = $this->members['fb_users'][$item->getIdentity()];
          $counter++;
        ?>
        <div class="he_item">
          <div class="photo"><?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($fb_item, 'thumb.icon'), array('class' => 'inviter_facebook_member', 'id' => 'inviter_facebook_member_' . $item->getIdentity())); ?></div>
          <div class="clr"></div>
          <div class="title"><?php echo $this->htmlLink($item->getHref(), $fb_item->getTitle()); ?></div>
        </div>
      
      <?php if ($counter % 3 == 0 || $counter == count($this->members['se_users'])): ?></div><div class="clr"></div><?php endif; ?>
      <?php  endforeach; ?>
    </div>

    <div class="inviter_widget_title">
      <button onclick="InviterIntroduce.showAddFriendsBox('twitter');"><?php echo $this->translate('INVITER_VIEWS_SCRIPTS_INDEX_MEMBERS'); ?></button>
    </div>
  
  <?php endif; ?>
</div>