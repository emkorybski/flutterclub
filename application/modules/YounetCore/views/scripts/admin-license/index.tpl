<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!$this->isPackageVerified):
  ?>

  <div class="settings">
    <?php echo $this->form->render($this); ?>
  </div>

<?php else: ?>
  <h3><? echo $this->translate("Package is verified") ?></h3>
  <p class="description">
    Your Package is verified with following information:<br />
    License: <strong><?php echo $this->license_key ?></strong><br />
    Host: <strong><?php echo $this->host ?></strong><br />

  </p>
<?php
endif;
?>
