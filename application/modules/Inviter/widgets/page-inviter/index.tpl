<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-02-08 14:58 kirill $
 * @author     Kirill
 */
?>
<div class="page-inviter-wrapper">
    <div>

      <div class="page-inviter-more-friends">
        <?php echo $this->translate('PAGE_INVITER_Invite friends now'); ?>
      </div>

      <div style="text-align: center; margin-top: 5px;">
        <img class="inviter_more_friends_provider" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Inviter/externals/images/providers_icons/fb.png" title="Facebook"/>
        <img class="inviter_more_friends_provider" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Inviter/externals/images/providers_icons/tw.png" title="Twitter"/>
        <img class="inviter_more_friends_provider" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Inviter/externals/images/providers_icons/gm.png" title="GMail"/>
        <img class="inviter_more_friends_provider" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Inviter/externals/images/providers_icons/hm.png" title="Live/Hotmail"/>
        <img class="inviter_more_friends_provider" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Inviter/externals/images/providers_icons/ya.png" title="Yahoo!"/>
        <div class="clr"></div>
      </div>

      <div style="margin-top: 10px; text-align: center;">
            <button onclick="window.location.href = '<?php echo $this->url(array('page_id'=>$this->subject->getIdentity()), 'page_inviter') ?>'"><?php echo $this->translate('PAGE_INVITER_Invite'); ?></button>
        </div>
    </div>
</div>
