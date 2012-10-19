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

  <div class="tip">
      <span><?php echo $this->translate('100010331') ?></span>
  </div>

  <table cellpadding='0' cellspacing='0' class='form'>
    <tr>
    <td class='form1'><?php echo $this->translate('100010332') ?></td>
    <td class='form2'>
      <input onclick="javascript:this.focus();this.select();" style="width:420px; background-color: #F6F6F6; border: 1px solid #C6C6C6; padding: 2px" type="text" readonly value="<?php echo $this->reflink ?>">
    </td>
    </tr>
  </table>

  
