<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: verify.tpl 9536 2011-12-07 19:06:20Z shaun $
 * @author     Jung
 */
?>



<?php if( $this->status ): ?>

  <script type="text/javascript">
    setTimeout(function() {
      parent.window.location.href = '<?php echo $this->url(array(), 'user_login', true); ?>';
    }, 5000);
  </script>

  <?php echo $this->translate("This account needs to be verified by email. Click %s to resend the email.",
      $this->htmlLink(array('route'=>'user_login'), $this->translate("here"))) ?>

<?php else: ?>

  <div class="error">
    <span>
      <?php echo $this->translate($this->error) ?>
    </span>
  </div>

<?php endif;