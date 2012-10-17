<div class='generic_layout_container layout_user_list_popular'>

<ul>
	<?php foreach( $this->paginator as $user ): ?>
		<li>
			<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'popularmembers_thumb')) ?>
			<div class='popularmembers_info'>
				<div class='popularmembers_name'>
					<?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
				</div>
				<div class='popularmembers_friends'>
					<?php echo $this->translate(array('INVITER_%s referral', '%s referrals', $user->referrals),$this->locale()->toNumber($user->referrals)) ?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>
</ul>

</div>