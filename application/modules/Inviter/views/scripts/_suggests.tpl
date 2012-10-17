<ul id='suggest_list'>
  <?php $i = 0; foreach ($this->suggests as $user): $i++;?>
     <?php if ($i == 13): ?>
      </ul>
      
       <div class='browsemembers_viewmore' id="suggest_more_button" style="clear:both;">
          <a id="more_link" class="buttonlink icon_viewmore" href="javascript:suggest.suggest_more($(this));"><?php echo $this->translate('INVITER_View More');?></a>
       </div>
      
      <ul style="display:none" id="more_suggests" />
     <?php endif; ?>
  
    <li class="suggest_friend" >
    <table width="100%" id="suggest_<?php echo $user->getIdentity() ?>"><tr>
      <td width="50px" style="padding: 7px; padding-top: 0px;" valign="top">
        <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
      </td>
     
      <td valign="top">
        <b><?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?></b>
        <br/>
        
        <a class="smoothbox" href="<?php echo $this->url(array('action' => 'add', 'controller' => 'friends', 'module' => 'members'), 'default')?>/user_id/<?php echo $user->getIdentity(); ?>">
          <?php echo $this->translate('INVITER_Add as friend');?>
        </a>
      </td>
      
      <td width="20px" valign="top">
        <div class="nonefriend nonfriend_delete" 
             onmouseover="$(this).addClass('nonfriend_delete_hover')"
             onmouseout="$(this).removeClass('nonfriend_delete_hover')"
             onclick="suggest.remove_suggest(<?php echo $user->getIdentity() ?>)"
        >&nbsp;</div>
      </td>
    </tr></table>
    </li>
  <?php endforeach; ?>
</ul>