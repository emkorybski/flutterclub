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

<?php
$current_page = $this->invites_paginator->getCurrentPageNumber();
?>

<script type="text/javascript">
    var pageAction = function (page) {
        window.location.href = en4.core.baseUrl + 'inviter/invitations/' + page;
    }
</script>
<?php echo $this->render('application/modules/Inviter/views/scripts/_providers_settings.tpl'); ?>
<div id="fb-root" name="fb-root"></div>
<script type='text/javascript'>
    FB.init({
        appId:'<?php echo $this->facebookKey; ?>',
        status:true, // check login status
        cookie:true, // enable cookies to allow the server to access the session
        xfbml:true  // parse XFBML
    });

    function select_all() {
        $$('.invite-select').each(function (item, index) {
            item.checked = $('invite-all').checked;
        });
    }

    en4.core.runonce.add(function () {
        var has_msg = "<?php echo $this->has_msg;?>";
        if (has_msg) {
            he_show_message("<?php echo $this->msg; ?>", "<?php echo $this->msg_type; ?>", 5000);
        }
    });

    function deleteSelected() {
        var selected = false;
        $$('.invite-select').each(function (item, index) {
            if(item.checked)
                selected = true;
        });
        if(selected)
            return confirm('<?php echo $this->string()->escapeJavascript($this->translate("INVITER_Invitations Delete selected")); ?>');
        else {
            alert('<?php echo ($this->translate("INVITER_Invites not selected")); ?>');
            return false;
        }
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


<div class="my_invitations">
    <h3><?php echo $this->translate('INVITER_Pending Invitations'); ?></h3>
    <p class="form-description"><?php echo $this->translate('INVITER_VIEWS_SCRIPTS_INVITATIONS_INDEX_DESCRIPTION'); ?></p>
    <br/>
    <?php echo $this->filter_form->render($this); ?>
            <div class="clear"></div>
    <?php if ($this->invites_paginator->getTotalItemCount() > 0): ?>
    <form name="invites-form" method="post"
          action="<?php echo $this->url(array(), 'inviter_invitations_delete_selected');?>">
        <table class="my_invitation_list">
            <?php foreach ($this->invites_paginator as $item): if (($item->provider == 'facebook' && !$item->recipient) || (!$item->recipient && !$item->sender)) continue; ?>
            <tr>
                <td width="15">
                    <input class="invite-select" type="checkbox" value="" name="invite_<?php echo $item->invite_id; ?>">
                </td>
                <td width='15px' style='padding:2px;'>
                    <img
                        src="application/modules/Inviter/externals/images/providers/<?php echo $item->provider; ?>_logo.png"
                        border='0' height='20px'/>
                </td>
                <td width='220px'><?php echo ($item->recipient_name) ? $item->recipient_name : $item->recipient; ?></td>
                <td width='130px'><?php echo $this->timestamp(strtotime($item->sent_date)); ?></td>
                <td width='220px'>
                    <a href="<?php echo $this->url(array('module' => 'inviter', 'controller' => 'invitations', 'action' => 'delete'), 'inviter_invitations_delete') ?>/<?php echo $item->invite_id; ?>"
                       class="buttonlink delete_btn smoothbox"><?php echo $this->translate('delete'); ?></a>
                    <?php if ($this->facebookKey && $item->provider == 'facebook') : ?>
                    <a href="javascript:void(0);" onclick="inviter.send_to_fb('<?php echo $item->recipient;?>');"
                       class="buttonlink send_btn"><?php echo $this->translate('INVITER_send new invite'); ?></a>
                    <?php else : ?>
                    <a href="<?php echo $this->url(array('module' => 'inviter', 'controller' => 'invitations', 'action' => 'sendnew'), 'inviter_invitations_sendnew') ?>/<?php echo $item->invite_id; ?>"
                       class="buttonlink send_btn smoothbox"><?php echo $this->translate('INVITER_send new invite'); ?></a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <div style="margin-top: 15px; margin-left: 11px;">
            <input type="checkbox" value="Check all" name="invite_all" id="invite-all" onclick="select_all();">
            <label for="invite-all"><?php echo $this->translate('INVITER_Invites Select All'); ?></label>
            <input id="delete-all" type="submit" onclick="return deleteSelected();"
                   value="<?php echo $this->translate('INVITER_Invites Delete Selected'); ?>">
        </div>
    </form>
    <?php else: ?>
    <div class="tip">
        <span><?php echo $this->translate('INVITER_There are no pending invitations.'); ?></span>
    </div>
    <?php endif; ?>
    <?php echo $this->paginationControl($this->invites_paginator, null, array("pagination/inviterpagination.tpl", "inviter")); ?>
</div>
