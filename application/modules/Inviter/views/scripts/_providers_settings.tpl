<style type="text/css">
    #submit_contacts-wrapper {
        clear: both;
    }
</style>
<?php if($this->form && $this->form->_sign_up) {
    $this->providers = $this->form->_providers;
    $this->fb_settings = $this->form->_fb_settings;
    $session = new Zend_Session_Namespace('inviter');
    $session->__set('user_referral_code', $this->form->_fb_settings['invite_code']);
}
?>
<script type='text/javascript'>

    en4.core.runonce.add(function () {
        provider.set_enabled('Facebook', <?php echo ($this->providers['facebook']) ? 1 : 0 ?>);
        provider.set_enabled('Twitter', <?php echo ($this->providers['twitter']) ? 1 : 0 ?>);
        provider.set_enabled('LinkedIn', <?php echo ($this->providers['linkedin']) ? 1 : 0 ?>);
        provider.set_enabled('GMail', <?php echo ($this->providers['gmail']) ? 1 : 0 ?>);
        provider.set_enabled('Yahoo!', <?php echo ($this->providers['yahoo']) ? 1 : 0 ?>);
        provider.set_enabled('Live/Hotmail', <?php echo ($this->providers['hotmail']) ? 1 : 0 ?>);
        provider.set_enabled('MSN', <?php echo ($this->providers['hotmail']) ? 1 : 0 ?>);
        provider.set_enabled('Last.fm', <?php echo ($this->providers['lastfm']) ? 1 : 0 ?>);
        provider.set_enabled('Foursquare', <?php echo ($this->providers['foursquare']) ? 1 : 0 ?>);
        provider.set_enabled('Mail.ru', <?php echo ($this->providers['mailru']) ? 1 : 0 ?>);
        provider.set_enabled('Orkut', <?php echo ($this->providers['gmail']) ? 1 : 0 ?>);

        provider.oauth_url = "<?php echo $this->url(array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'request', 'format' => 'smoothbox'), 'default') ?>";
    });

    suggest.current_suggests = <?php echo Zend_Json::encode($this->current_suggests); ?>;

    en4.core.runonce.add(function () {
        if ($('separator2-label') != undefined) {
            $('separator2-label').getParent('li').destroy();
        }
        var cnt = '<?php echo 1;//$this->count; ?>';
        cnt *= 1;

        if (cnt == 0) {
            if ($('inviter-writer-form') != undefined) {
                $('inviter-writer-form').slide('hide').slide('out');
            }
        } else {
            if ($('inviter-uploader-form') != undefined) {
                $('inviter-uploader-form').slide('hide').slide('out');
            }

            if ($('inviter-writer-form') != undefined) {
                $('inviter-writer-form').slide('hide').slide('out');
            }
        }

    });

    function tab_slider($tab) {
        var cnt = '<?php echo $this->count; ?>';
        cnt *= 1;
        $$('.inviter-form-cont').removeClass('inviter-form-bg');
        $$('.inviter-form-cont').removeClass('inviter-form-hover');
        $$('.inviter-form').slide('hide').slide('out');
        $$('.inviter-tab-title').addClass('inviter-form-title');

        $('inviter-' + $tab + '-form').slide('hide').slide('in');
        $('inviter-' + $tab + '-title').removeClass('inviter-form-title');
        $('inviter-' + $tab + '-conteiner').addClass('inviter-form-bg');
    }

    function changeFields() {

    }

    function searchMembers() {
        $('field_search_criteria').submit();
    }

    function show_creation_description(id) {
        $('inviter-uploader-conteiner').getElements('div')[1].setStyle('height', '');

        if ($(id).hasClass('creation_item_hide')) {
            $(id).removeClass('creation_item_hide');
        } else {
            $(id).addClass('creation_item_hide');
        }
    }
</script>

<div id="fb-root"></div>
<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js"></script>
<script type="text/javascript">
    FB.init({
        appId:'<?php echo $this->providers['facebook']; ?>',
        status:true,
        cookie:true,
        xfbml:true
    });
</script>
<input name="fb-invite-code" id="fb-invite-code" type="hidden" value="<?php echo $this->fb_settings['invite_code']; ?>">
<input name="fb-redirect-url" id="fb-redirect-url" type="hidden" value="<?php echo $this->fb_settings['redirect_url']; ?>">
<input name="fb-invitation-url" id="fb-invitation-url" type="hidden" value="<?php echo $this->fb_settings['invitation_url']; ?>">
<input name="fb-host" id="fb-host" type="hidden" value="<?php if(isset($this->fb_settings['host'])) echo $this->fb_settings['host']; ?>">
<input name="fb-picture" id="fb-picture" type="hidden" value="<?php if(isset($this->fb_settings['picture'])) echo $this->fb_settings['picture']; ?>">
<input name="fb-caption" id="fb-caption" type="hidden" value="<?php if(isset($this->fb_settings['caption'])) echo $this->translate($this->fb_settings['caption']); ?>">
<input name="fb-message" id="fb-message" type="hidden" value="<?php if(isset($this->fb_settings['message'])) echo $this->translate($this->fb_settings['message']); ?>">
<input name="fb-signup" id="fb-signup" type="hidden" value="<?php if(isset($this->fb_settings['signup'])) echo $this->fb_settings['signup'];?>">
<input name="fb-page-id" id="fb-page-id" type="hidden" value="<?php if(isset($this->fb_settings['page_id'])) echo $this->fb_settings['page_id'];?>">
<input name="fb-fail-message" id="fb-fail-message" type="hidden" value="<?php echo $this->translate('INVITER_Invitations not sent');?>">