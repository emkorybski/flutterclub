<div class="headline">
  <h2>
  <?php echo $this->translate('INVITER_My Introduction');?>
  </h2>
  <div class="tabs">
    <?php
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<?php echo $this->form->render($this); ?>