
<ul>
  <?php foreach( $this->users as $user ): ?>
    <li>
      <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'topinviters_thumb')) ?>
      <div class='topinviters_info'>
        <div class='topinviters_name'>
          <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
        </div>
        <div class='topinviters_friends'>
          <?php echo $this->translate(array('%s invitation', '%s invitations', $user->invites_sent),$this->locale()->toNumber($user->invites_sent)) ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
