<div class="he-hint">
	<div class="he-hint-body">
		<div class="he-hint-right">
			<?php echo $this->htmlLink($this->user->getHref(), $this->itemPhoto($this->user, 'thumb.profile', '', array('width' => '100px', 'height' => '100px', 'target' => '_blank'))); ?>
		</div>
		<div class="he-hint-left">
			<div class="title">
				<?php
					$display_name = $this->user->getTitle();
					echo $this->htmlLink($this->user->getHref(), $display_name, array('target' => '_blank'));
				?>
			</div>
			<div class="clr"></div>
			<div class="horizontal-list facebook_members_tip" style="margin-top:17px;">
        <?php if ($this->mutual_like_count) {
          $label = $this->translate(array('INVITER_%s mutual like', '%s mutual likes', $this->mutual_like_count), $this->mutual_like_count);;
          echo ($this->likedMembersAndPages == 0)
            ? $this->htmlLink($this->url(array('action' => 'index', 'user_id' => $this->user->getIdentity()), 'like_default'), $label, array('target' => '_blank', 'class' => 'inviter_mutual_likes'))
            : $this->htmlLink("javascript:like.see_all_liked({$this->user->getIdentity()});", $label, array('class' => 'inviter_mutual_likes'));
        }
        ?>
        <?php if ($this->mutual_friend_count != 0) : ?>
          <a href="javascript:suggest.show_mutual_friends(<?php echo $this->user->getIdentity() ?>)" class="inviter_mutual_friends">
            <?php echo $this->translate(array('INVITER_%s mutual friend', '%s mutual friends', $this->mutual_friend_count), $this->mutual_friend_count); ?>
          </a>
        <?php endif; ?>
				<?php if ($this->mutual_friend_count != 0) : ?>
        <div class="clr"></div>
				<?php foreach($this->paginator as $item): ?>
					<div class="item">
            <?php
              $photo = $this->itemPhoto($item, 'thumb.icon', '', array('width' => '32px', 'height' => '32px'));
							echo $this->htmlLink($item->getHref(), $photo, array('class' => 'he-hint-tip-links', 'style' => 'width: 32px; height:32px; display:block;'));
						?>
						<div class="he-hint-title display_none"></div>
						<div class="he-hint-text display_none"><?php echo $item->getTitle(); ?></div>
					</div>
				<?php endforeach; ?>
				<?php endif; ?>
				<div class="clr"></div>

			</div>
		</div>
		<div class="clr"></div>
	</div>
	<div class="he-hint-options">
		<?php if ($this->viewer()->getIdentity() && $this->viewer()->getIdentity() != $this->user->getIdentity()): ?>
			<?php echo $this->htmlLink($this->url(array('action' => 'compose', 'to' => $this->user->getIdentity()), 'messages_general'), $this->translate("Send Message"), array('class' => 'he-hint-send-message', 'target' => '_blank')); ?>
			<?php echo $this->userFriendship($this->user); ?>
			<div class="clr"></div>
		<?php endif; ?>
	</div>
</div>