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

<table cellpadding='0' cellspacing='0' class='form'>
  <tr>
  <td class='form1'><?php echo $this->translate('100010215') ?></td>
  <td class='form2'> <?php echo $this->invites_sent ?> </td>
  </tr>

  <tr>
  <td class='form1'><?php echo $this->translate('100010216') ?></td>
  <td class='form2'> <?php echo $this->invites_converted ?> </td>
  </tr>

  <tr>
  <td class='form1'>&nbsp;</td>
  <td class='form2'>&nbsp;</td>
  </tr>
</table>

