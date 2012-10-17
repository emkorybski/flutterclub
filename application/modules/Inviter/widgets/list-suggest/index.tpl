<script type='text/javascript'>
suggest.current_suggests = <?php echo Zend_Json::encode($this->current_suggests); ?>;
</script>

<div class='generic_layout_container layout_user_list_popular'>

<ul id='suggest_list'>
  <?php foreach ($this->suggests as $user): ?>

  <li class="suggest_friend_widget" >
    <table width="100%" id="suggest_<?php echo $user->getIdentity() ?>"><tr>
      <td width="48px" style="padding: 0px 3px 2px 0px;" valign="top">
        <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
      </td>
     
      <td valign="top">
        <b><?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?></b>
        <br/>
        
        <a href="javascript:suggest.show_mutual_friends(<?php echo $user->getIdentity() ?>)" style="font-size: 10px">
          <?php echo $this->translate(array('%s mutual friend', '%s mutual friends', count($this->current_suggests[$user->getIdentity()]['mutual_friends'])),count($this->current_suggests[$user->getIdentity()]['mutual_friends']));?>
        </a>
        <br/>
        
        <a class="smoothbox buttonlink smoothbox icon_friend_add" style="font-size: 12px" href="<?php echo $this->url(array('action' => 'add', 'controller' => 'friends', 'module' => 'members'), 'default')?>/user_id/<?php echo $user->getIdentity(); ?>">
          <?php echo $this->translate('INVITER_Add as friend');?>
        </a>
      </td>
      
      <td width="5px" valign="top">
        <div class="nonefriend nonfriend_delete" 
             onmouseover="$(this).addClass('nonfriend_delete_hover')"
             onmouseout="$(this).removeClass('nonfriend_delete_hover')"
             onclick="suggest.remove_suggest(<?php echo $user->getIdentity() ?>, true)"
        >&nbsp;</div>
      </td>
    </tr></table>
  </li>
  <?php endforeach; ?>
</ul>
</div>