<table width="100%" id="suggest_<?php echo $this->user->getIdentity() ?>"><tr>

	<?php if ($this->widget):?>

  <td width="48px" style="padding: 0px 3px 2px 0px;" valign="top">
    <?php echo $this->htmlLink($this->user->getHref(), $this->itemPhoto($this->user, 'thumb.icon')) ?>
  </td>
  <td valign='top'>
    <b><?php echo $this->htmlLink($this->user->getHref(), $this->user->getTitle()) ?></b>
    <br/>
    <a href="javascript:suggest.show_mutual_friends(<?php echo $this->user->getIdentity() ?>)" style="font-size: 10px">
      <?php echo $this->translate(array('INVITER_%s mutual friend', '%s mutual friends', count($this->mutual_friends)),count($this->mutual_friends));?>
    </a>
    <br/>

    <a href='javascript://' class="smoothbox buttonlink smoothbox icon_friend_add" style="font-size: 12px;" onclick="Smoothbox.open('<?php echo $this->url(array('action' => 'add', 'controller' => 'friends', 'module' => 'members'), 'default')?>/user_id/<?php echo $this->user->getIdentity(); ?>');">
      <?php echo $this->translate('INVITER_Add as friend');?>
    </a>
  </td>
  <td width="5px" valign="top">

	<?php else:?>

  	<td width="50px" style="padding: 0px 7px 7px;" valign="top">
    <?php echo $this->htmlLink($this->user->getHref(), $this->itemPhoto($this->user, 'thumb.icon')) ?>
  </td>
  <td valign='top'>
    <b><?php echo $this->htmlLink($this->user->getHref(), $this->user->getTitle()) ?></b>
    <br/>
    <a href='javascript://' onclick="Smoothbox.open('<?php echo $this->url(array('action' => 'add', 'controller' => 'friends', 'module' => 'members'), 'default')?>/user_id/<?php echo $this->user->getIdentity(); ?>');">
      <?php echo $this->translate('INVITER_Add as friend');?>
    </a>
  </td>
  	<td width="20px" valign="top">

	<?php endif;?>


    <div class="nonefriend nonfriend_delete" onmouseover="$(this).addClass('nonfriend_delete_hover')" onmouseout="$(this).removeClass('nonfriend_delete_hover')" onclick="suggest.remove_suggest('<?php echo $this->user->getIdentity() ?>' <?php if ($this->widget): ?>, true <?php endif; ?>)"
    >&nbsp;</div>
  </td>
</tr></table>