

<style type="text/css">
  .global_form > div > div {
    width:770px;
  }

  .global_form #cancel {
    margin-left:5px;
  }

  .global_form #fieldset-buttons {
    line-height:30px;
  }

  .global_form #submit-element {
    min-width:85px;
  }

  .global_form #submit-label {
    display:none;
  }

  .global_form #submit-wrapper {
    clear: none;
    overflow: visible;
  }
</style>


<div style="padding:15px;">
<?php if ($this->error) : ?>

  <h2>
    <?php echo $this->translate('We\'re sorry!') ?>
  </h2>

  <p><?php echo $this->error; ?></p>

<?php elseif($this->tokenRow) : ?>

  <?php echo $this->form->render($this); ?>

<?php endif; ?>
    <?php
        if($this->message_html != '' )
            echo $this->message_html;
    ?>
</div>