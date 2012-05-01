<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: share.tpl 9611 2012-01-24 02:13:10Z shaun $
 * @author     John
 */
?>

<div>
  <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
  <br />
  <div class="sharebox">
    <?php if( $this->attachment->getPhotoUrl() ): ?>
      <div class="sharebox_photo">
        <?php echo $this->htmlLink($this->attachment->getHref(), $this->itemPhoto($this->attachment, 'thumb.icon'), array('target' => '_parent')) ?>
      </div>
    <?php endif; ?>
    <div>
      <div class="sharebox_title">
        <?php echo $this->htmlLink($this->attachment->getHref(), $this->attachment->getTitle(), array('target' => '_parent')) ?>
      </div>
      <div class="sharebox_description">
        <?php echo $this->attachment->getDescription() ?>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
//<![CDATA[
var toggleFacebookShareCheckbox, toggleTwitterShareCheckbox;
(function($$) {
  toggleFacebookShareCheckbox = function(){
      $$('span.composer_facebook_toggle').toggleClass('composer_facebook_toggle_active');
      $$('input[name=post_to_facebook]').set('checked', $$('span.composer_facebook_toggle')[0].hasClass('composer_facebook_toggle_active'));
  }
  toggleTwitterShareCheckbox = function(){
      $$('span.composer_twitter_toggle').toggleClass('composer_twitter_toggle_active');
      $$('input[name=post_to_twitter]').set('checked', $$('span.composer_twitter_toggle')[0].hasClass('composer_twitter_toggle_active'));
  }
})($$)
//]]>
</script>