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
<div>
    <p>
        <?php echo $this->translate('INVITER_Referral Link Description'); ?>
    </p>

    <div class="inviter-referral-link" id="inviter-referral-link">
        <?php if ($this->referral_link): ?>
        <?php echo $this->render('application/modules/Inviter/views/scripts/_referral_link.tpl'); ?>
        <?php endif; ?>
        <div class="clear"></div>
    </div>
</div>