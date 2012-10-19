
<h2><?php echo $this->translate("Friends Inviter Settings") ?></h2>
<div class="settings"><div class="global_form" id="admin_settings_form">
  <?php if ($this->form->saved_successfully): ?><h3 class="slowfade"><?php echo $this->translate("Settings were saved successfully.") ?></h3><?php endif; ?>
  <?php echo $this->form->render($this) ?>
</div></div>
