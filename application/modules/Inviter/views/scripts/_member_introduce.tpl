

<a href="javascript://" class="hide_introduce_btn" onclick="InviterIntroduce.hideMember(this, <?php echo $this->memberItem->user_id; ?>)"></a>
<div class="introduce_photo">
  <?php echo $this->htmlLink($this->memberItem->getHref(), $this->itemPhoto($this->memberItem, 'thumb.icon'))?>
</div>
<div class="introduce_member_title"><?php echo $this->htmlLink($this->memberItem->getHref(), $this->memberItem->getTitle())?></div>
<div class="introduce_info">
  <div class="introduce_desc">
    <div class="introduce_member_body"><?php echo $this->memberIntroduce->body; ?></div>

    <div class="introduce_additional_info">
      <?php if ($this->mutual_friend_count != 0) { ?>
        <a href="javascript:suggest.show_mutual_friends(<?php echo $this->memberItem->getIdentity() ?>)" class="inviter_mutual_friends">
          <?php echo $this->translate(array('INVITER_%s mutual friend', '%s mutual friends', $this->mutual_friend_count), $this->mutual_friend_count); ?>
        </a>
      <?php } ?>

      <?php if ($this->mutual_like_count) {
        $label = $this->translate(array('INVITER_%s mutual like', '%s mutual likes', $this->mutual_like_count), $this->mutual_like_count);;
        echo ($this->likedMembersAndPages == 0)
          ? $this->htmlLink($this->url(array('action' => 'index', 'user_id' => $this->memberItem->getIdentity()), 'like_default'), $label, array('target' => '_blank', 'class' => 'inviter_mutual_likes'))
          : $this->htmlLink("javascript:like.see_all_liked({$this->memberItem->getIdentity()});", $label, array('class' => 'inviter_mutual_likes'));
      }
      ?>

      <div class="clr"></div>
    </div>

    <?php
      $addFriendUrl = $this->url(array('controller' => 'friends', 'action' => 'add', 'user_id' => $this->memberItem->user_id), 'user_extended');
    ?>
    <button onclick="Smoothbox.open('<?php echo $addFriendUrl; ?>')"><?php echo $this->translate('Add Friend'); ?></button>
  </div>
</div>
<div class="clr"></div>