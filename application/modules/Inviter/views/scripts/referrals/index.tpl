<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2010-07-02 17:53 ermek $
 * @author     Ermek
 */
?>

<script type="text/javascript">
    var pageAction = function (page) {
        window.location.href = en4.core.baseUrl + 'inviter/referrals/' + page;
    }
</script>

<div class="headline">
    <div class="tabs">
        <?php
        // Render the menu
        echo $this->navigation()
            ->menu()
            ->setContainer($this->navigation)
            ->render();
        ?>
    </div>
</div>


<h3><?php echo $this->translate('INVITER_My Referrals'); ?></h3>
<p class="form-description"><?php echo $this->translate('INVITER_VIEWS_SCRIPTS_REFERRALS_DESCRIPTION'); ?></p>
    <br/>
<?php echo $this->filter_form->render($this); ?>
    <div class="clear"></div>
<div class='layout_middle' style="width:650px;">
    <?php if (count($this->referrals_paginator)) : ?>

    <div class="referrals-wrapper">
        <?php foreach ($this->referrals_paginator as $item) : $user = Engine_Api::_()->getItem('user', $item->new_user_id); if(!$user->getIdentity()) continue; ?>
        <div class="row">
            <div class="photo">
                <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.profile')); ?>
            </div>
            <div class="info">
                <div><?php echo $this->translate('INVITER_Referrals User') . ' ' . $this->htmlLink($user->getHref(), $user->getTitle()); ?></div>
                <div>
                    <?php
                    $provider = false;
                    if ($item->code && $item->provider == 'link') {
                        $provider = $this->translate('INVITER_Referral Came by referral link');
                    }
                    if ($item->code && $item->recipient && !$item->provider) {
                        $provider = $this->translate('INVITER_Referral Came by email invitation');
                    }
                    echo $this->translate("INVITER_Referral Provider") . ' ';
                    echo ($provider) ? $provider : '';
                    ?>
                    <?php if (!$provider) : ?>
                    <img align="top" vspace=""
                         src="application/modules/Inviter/externals/images/providers/<?php echo $item->provider; ?>_logo.png"
                         border='0' height='20px'/>
                    <?php endif;?>
                </div>
                <div>
                    <?php $date = date_parse($item->referred_date);
                        if (checkdate($date['month'], $date['day'], $date['year'])) echo $this->translate('INVITER_Referrals Registration date') . ' ' . $this->timestamp($item->referred_date);
                    ?>
                </div>
            </div>
            <div class="action">
                <?php echo $this->userFriendship($user); ?>
            </div>
            <div class="clear"></div>
        </div>
        <?php endforeach; ?>
        <?php echo $this->paginationControl($this->referrals_paginator, null, array("pagination/inviterpagination.tpl", "inviter")); ?>
    </div>
    <?php else: ?>
    <div class="tip">
        <span><?php echo $this->translate('INVITER_No referrals'); ?></span>
    </div>
    <?php endif; ?>
</div>
