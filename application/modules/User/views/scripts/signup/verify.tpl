<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: verify.tpl 9716 2012-05-14 20:05:57Z pamela $
 * @author     Jung
 */
?>



<?php if( $this->status ): ?>

  <script type="text/javascript">
    setTimeout(function() {
      parent.window.location.href = '<?php echo $this->url(array(), 'user_login', true); ?>';
    }, 5000);
  </script>

  <?php echo $this->translate("Your account has been verified. Please wait to be redirected or click %s to login.",
      $this->htmlLink(array('route'=>'user_login'), $this->translate("here"))) ?>

<?php else: ?>

  <div class="error">
    <span>
      <?php echo $this->translate($this->error) ?>
    </span>
  </div>

<?php endif;