<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-02-08 14:58 ermek $
 * @author     Ermek
 */
?>

<?php
  $this->headScript()->appendFile('application/modules/Inviter/externals/scripts/core.js');
  $ajaxRequestUrl = $this->url(array('module' => 'inviter', 'controller' => 'introduce', 'action' => 'ajax-request'), 'default', true);
  $this->headScript()->appendScript('en4.core.runonce.add(function() {InviterIntroduce.url = ' . $this->jsonInline($ajaxRequestUrl) . ';});');

  $widget_uid = uniqid('find_more_friend_');
?>

<script type="text/javascript">
	en4.core.runonce.add(function(){

		var miniTipsOptions = {
			'htmlElement': '.inviter_tip_text',
			'delay': 1,
			'className': 'he-tip-mini',
			'id': 'he-mini-tool-tip-id',
			'ajax': false,
			'visibleOnHover': false
		};

		var $likesTips = new HETips($$('.inviter_more_friends_item'), miniTipsOptions);
	});
</script>

<div id="<?php echo $widget_uid; ?>">
  <a onclick="InviterIntroduce.hideMoreFriends('<?php echo $widget_uid; ?>')" class="hide_introduce_btn" href="javascript://"></a>
  <div class="inviter_more_friends_title">
    <b><?php echo $this->htmlLink($this->url(array(), 'inviter_general'), $this->translate('INVITER_%s, More Friends Are Waiting', $this->viewer()->getTitle())); ?></b>
  </div>


  <div class="inviter_more_friends_items">
  <?php if (isset($this->paginator)) : ?>
  <?php foreach($this->paginator as $item) : ?>
    <span>
      <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('class' => 'inviter_more_friends_item')); ?>
      <div class="inviter_tip_title display_none"></div>
      <div class="inviter_tip_text display_none"><?php echo $item->getTitle(); ?></div>
    </span>
  <?php endforeach; ?>
  <?php else : ?>
    <img class="inviter_more_friends_provider" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Inviter/externals/images/providers_icons/fb.png" title="Facebook"/>
    <img class="inviter_more_friends_provider" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Inviter/externals/images/providers_icons/tw.png" title="Twitter"/>
    <img class="inviter_more_friends_provider" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Inviter/externals/images/providers_icons/gm.png" title="GMail"/>
    <img class="inviter_more_friends_provider" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Inviter/externals/images/providers_icons/hm.png" title="Live/Hotmail"/>
    <img class="inviter_more_friends_provider" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Inviter/externals/images/providers_icons/ya.png" title="Yahoo!"/>
  <?php endif; ?>
    <div class="clr"></div>
  </div>

  <div class="inviter_more_friends_desc">
    <?php

      if (isset($this->paginator)) {
        $friends_link = $this->htmlLink('javascript://', $this->translate(array('INVITER_Friend', 'INVITER_%s Friends', $this->item_count), $this->item_count), array(
          'onclick' => "he_list.box('inviter', 'getInviterUsedFriends', '{$this->translate('Friends')}', {'disable_list2': true});"
        ));
        $inviter_desc = $this->translate(array(
          "INVITER_These %s found their friends",
          "These %s found their friends using the friend finder. Have you found all of your friends? Give it a try.",
          $this->item_count
        ), $friends_link);
      } else {
        $inviter_desc = $this->translate('INVITER_Have you found all of your friends? Give it a try.');
      }

      echo $inviter_desc;
    ?>
  </div>
  <button onclick="window.location.href = '<?php echo $this->url(array(), 'inviter_general') ?>'"><?php echo $this->translate('INVITER_Find Friends'); ?></button>
  <br/>
</div>

