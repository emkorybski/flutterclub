
<?php
  $this->headScript()->appendFile('application/modules/Inviter/externals/scripts/core.js');
  $this->headScript()->appendFile('http://connect.facebook.net/' . $this->getFacebookLocale() . '/all.js');
?>

<?php if ($this->show_login_btn) { ?>

  <script type="text/javascript">

  var appId = <?php echo $this->jsonInline($this->appId); ?>;

  en4.core.runonce.add(function() {
    FB.init({
      appId  : appId,
      status : false, // check login status
      cookie : true, // enable cookies to allow the server to access the session
      xfbml  : true  // parse XFBML
    });

    FB.Event.subscribe('auth.login', function(response){
      if (response.status == "connected") {
        window.location.href = window.location.href; //TODO ajax reloading!!!
      }
    });
  });
  </script>

  <fb:login-button></fb:login-button> <span><?php echo $this->translate('INVITER_You need to be logged into Facebook to invite your friends.'); ?></span>&nbsp;
  <span><?php echo $this->htmlLink($this->url(array('skip_inviter' => 1), 'inviter_facebook_signup'), $this->translate('INVITER_Skip this step.')); ?></span>

  <div id="fb-root"></div>

<?php } elseif (isset($this->skipForm) && $this->skipForm) { ?>

<script type='text/javascript'>
function skipFormContacts()
{
  document.getElementById("skip").value = "skipFormContacts";
  document.getElementById("invitation_send").submit();
}

function submitFormContacts(ids)
{
  document.getElementById("inviterStep").value = 'inviterFinalize';
  document.getElementById("inviterContacts").value = ids;
  document.getElementById("invitation_send").submit();
}

en4.core.runonce.add(function() {
  <?php if (isset($this->submitForm) && $this->submitForm) : ?>
    submitFormContacts(<?php echo $this->jsonInline($this->contactIds); ?>);
  <?php else : ?>
    skipFormContacts();
  <?php endif; ?>
});
</script>

<p><?php echo $this->translate("INVITER_Please wait soon you will redirected to signup page."); ?></p>

<div class="display_none">
  <?php echo $this->form->render($this); ?>
</div>

<?php } else { ?>
  
<div id="fb-root"></div>

<script type="text/javascript">

var appId = <?php echo $this->jsonInline($this->appId); ?>;

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
        window.location.reload();
      } else {
        window.location.reload();
      }
    });
  }
});

</script>

<fb:serverFbml style="margin-left: -10px;" class="fb_iframe_widget">
<script type="text/fbml">
<fb:fbml>
  <fb:request-form action="<?php echo $this->action_url; ?>" method="POST" invite="true" type=" "
    content="<fb:req-choice url='<?php echo $this->invite_url; ?>' label='Confirm' />">
    <fb:multi-friend-selector showborder="false" email_invite="false" import_external_friends="false" actiontext="<?php echo $this->translate('INVITER_Select the friends you want to invite.'); ?>" max="200" cols="5" exclude_ids="<?php echo $this->exclude_ids; ?>">
    </fb:multi-friend-selector>
    <input name="invite_code" type="hidden" value="<?php echo$this->invite_code; ?>"/>
    <input name="inviterStep" type="hidden" value="inviterFinalize"/>
    <input name="submitFacebook" type="hidden" value="1"/>
  </fb:request-form>
</fb:fbml>
</script>
</fb:serverFbml>

<?php } ?>