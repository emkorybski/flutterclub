
<?php
  $this->headScript()->appendFile('application/modules/Inviter/externals/scripts/core.js');
  $this->headScript()->appendFile('https://connect.facebook.net/' . $this->getFacebookLocale() . '/all.js');
?>

<?php if (isset($this->init_fb_app) && $this->init_fb_app) : ?>

<script type="text/javascript">

var appId = <?php echo $this->jsonInline($this->appId); ?>;

en4.core.runonce.add(function() {
  FB.init({
    appId  : appId,
    status : true, // check login status
    cookie : true, // enable cookies to allow the server to access the session
    xfbml  : true  // parse XFBML
  });

<?php if ($this->fb_logout) : ?>
  FB.XFBML.parse();

<?php else : ?>
  FB.getLoginStatus(function(response) {
    if (response.status == "connected") {
      FB.XFBML.parse();
    } else {
      FB.login(function(response){
        if (response.status == "connected") {
          FB.XFBML.parse();
          window.location.reload();
        } else {
          window.location.reload();
        }
      });
    }
  });
<?php endif; ?>
});
</script>

<?php endif; ?>

<div id="fb-root"></div>

<div class="global_form_popup">
  <div>
    <div>
      <?php echo $this->htmlLink($this->url(array('page' => $this->current_page), 'inviter_invitations', true), "&laquo;" . $this->translate('INVITER_Back to invitations'), array('style' => 'float:right; font-weight:bold;')); ?>
      <h3><?php echo $this->translate('INVITER_Resend New Invitation'); ?></h3>
      <p><?php echo $this->translate('INVITER_FORM_SENDNEW_DESCRIPTION'); ?></p>
      <br/>

    <?php if ($this->fb_logout) : ?>
      <?php echo $this->translate('INVITER_To continue you need login as %s', '<fb:name style="font-weight:bold" uid="' . $this->invitation->sender . '"></fb:name>'); ?><br/>
      <button onclick="if(FB && FB._session != null){FB.logout(function(response){window.location.href = window.location.href;});}else{window.location.href;}"><?php echo $this->translate('Login'); ?></button>
    <?php else : ?>
      <fb:serverfbml class="fb_iframe_widget">
      <script type="text/fbml">
      <fb:fbml>
        <fb:request-form action="<?php echo $this->action_url; ?>" method="POST" invite="true" type="invite"
          content="<fb:req-choice url='<?php echo $this->invite_url; ?>' label='Confirm' />">
          <input type="hidden" name="recipient_id" value="<?php echo $this->invitation->recipient; ?>"/>
          <fb:request-form-submit import_external_friends="false" label="<?php echo $this->translate('Resend to %N'); ?>" uid="<?php echo $this->invitation->recipient; ?>"></fb:request-form-submit>
        </fb:request-form>
      </fb:fbml>
      </script>
      </fb:serverfbml>
    <?php endif; ?>
    </div>
  </div>
</div>

