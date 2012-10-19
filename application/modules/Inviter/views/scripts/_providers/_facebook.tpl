

<?php
  $this->headScript()->appendFile('application/modules/Inviter/externals/scripts/core.js');
?>
<div id="fb-root"></div>

<?php if (isset($this->init_fb_app) && $this->init_fb_app) : ?>

<script type="text/javascript">
en4.core.runonce.add(function(){
    var appId = <?php echo $this->jsonInline($this->appId); ?>;
    var inviterIndexUrl = <?php echo $this->jsonInline($this->url(array(), 'inviter', true)); ?>;
    var inviterContactsUrl = <?php echo $this->jsonInline($this->url(array(), 'inviter_contacts', true)); ?>;

    window.fbAsyncInit = function() {
        FB.init({
        appId  : appId,
        status : true, // check login status
        cookie : true, // enable cookies to allow the server to access the session
        xfbml  : true  // parse XFBML
      });
      FB.getLoginStatus(function(response) {
        if (response.status == "connected") {
          FB.XFBML.parse();
        } else {
          FB.login(function(response){
            if (response.status == "connected") {
              FB.XFBML.parse();
              window.location.href = inviterContactsUrl;
            } else {
              window.location.href = inviterIndexUrl;
            }
          });
        }
      });
    };

    (function() {
      var e = document.createElement('script'); e.async = true;
      e.src = document.location.protocol + '//connect.facebook.net/<?php echo $this->getFacebookLocale(); ?>/all.js';
      document.getElementById('fb-root').appendChild(e);
    }());

})

window.addEvent('load', function(){en4.core.runonce.trigger();});
</script>

<?php endif; ?>
<fb:serverFbml style="margin-left: -10px;" class="fb_iframe_widget">
<script type="text/fbml">
<fb:fbml>
  <fb:request-form action="<?php echo $this->action_url; ?>" method="POST" invite="true" type=" "
    content="<fb:req-choice url='<?php echo $this->invite_url; ?>' label='Confirm' />">
    <fb:multi-friend-selector showborder="false" email_invite="false" import_external_friends="false" actiontext="<?php echo $this->translate('INVITER_Select the friends you want to invite.'); ?>" max="200" cols="5" exclude_ids="<?php echo $this->exclude_ids; ?>">
    </fb:multi-friend-selector>
    <input type="hidden" name="provider" value="facebook"/>
  </fb:request-form>
</fb:fbml>
</script>
</fb:serverFbml>  
