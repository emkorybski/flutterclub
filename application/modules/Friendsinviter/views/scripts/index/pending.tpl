<?php
  $this->headScript()
    ->appendFile($this->baseUrl() . '/application/modules/Friendsinviter/externals/scripts/friendsinviter.js')
    ->appendFile($this->baseUrl() . '/application/modules/Semods/externals/scripts/semods.js')
?>

<?php if( count($this->navigation) ): ?>
<div class="headline">
  <h2>
	<?php echo $this->translate('Invite Your Friends');?>
  </h2>
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
<?php endif; ?>


<?php if (count($this->invites) == 0): ?>

  <div class="tip">
    <span><?php echo $this->translate('100010204') ?></span>
  </div>

<?php else: ?>

 <?php if ($this->maxpage > 1): ?>
    <br>
    <div class='center' style='text-align: center'>
   <?php if ($this->p != 1): ?><a href="<?php echo $this->url(array('module' => 'friendsinviter', 'controller' => 'index', 'action' => 'pending', 'p' => $this->p - 1), 'default', true) ?>">&#171; <?php echo $this->translate('100010205') ?></a><?php else: ?><font class='disabled'>&#171; <?php echo $this->translate('100010205') ?></font><?php endif; ?>
   <?php if ($this->p_start == $this->p_end): ?>
      &nbsp;|&nbsp; <?php echo $this->translate('100010206') ?> <?php echo $this->p_start ?> <?php echo $this->translate('100010208') ?> <?php echo $this->invites_total ?> &nbsp;|&nbsp; 
    <?php else: ?>
      &nbsp;|&nbsp; <?php echo $this->translate('100010207') ?> <?php echo $this->p_start ?>-<?php echo $this->p_end ?> <?php echo $this->translate('100010208') ?> <?php echo $this->invites_total ?> &nbsp;|&nbsp; 
    <?php endif; ?>
   <?php if ($this->p != $this->maxpage): ?><a href="<?php echo $this->url(array('module' => 'friendsinviter', 'controller' => 'index', 'action' => 'pending', 'p' => $this->p + 1), 'default', true) ?>"><?php echo $this->translate('100010209') ?> &#187;</a><?php else: ?><font class='disabled'><?php echo $this->translate('100010202') ?> &#187;</font><?php endif; ?>
    </div>
    <br><br>
  <?php endif; ?>

<div style="padding: 0px 20px">
  <?php foreach($this->invites as $invite) { ?>
	<div id="invite_<?php echo $invite['id'] ?>" class="clearfix" style="background-position: top left; background-repeat:no-repeat;background-image:url('application/modules/Friendsinviter/externals/images/icons/invite16.gif'); width:550px;border-bottom: 1px solid #DEDEDE;margin-bottom:5px; padding: 0px 0px 5px 22px;">
	<div style="float:left"><?php echo $invite['recipient'] ?></div>
	
	<div class='profile_action_date'>
	  &nbsp;&nbsp; <?php echo $this->timestamp($invite['timestamp']) ?> &nbsp;&nbsp;
	  <a href="javascript:invite_delete('<?php echo $invite['id'] ?>')"><?php echo $this->translate('100010326') ?></a>
	  <span style="padding-left: 2px;padding-right: 2px;"> | </span>
	  <span id="<?php echo $invite['id'] ?>_link">
	  <a href="javascript:invite_resend('<?php echo $invite['id'] ?>')"><?php echo $this->translate('100010327') ?></a>
	  </span>
	  <span id="<?php echo $invite['id'] ?>_progress" style="display:none; height: 18px; background: url(application/modules/Friendsinviter/externals/images/semods_ajaxprogress1.gif) left no-repeat; padding-left: 20px">
		<?php echo $this->translate('100010328') ?>
	  </span>
	</div>
	</div>
  <?php } ?>
</div>

<?php endif; ?>
  