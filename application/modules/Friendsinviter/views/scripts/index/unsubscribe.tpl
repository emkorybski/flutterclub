<?php
  $this->headScript()
    ->appendFile($this->baseUrl() . '/application/modules/Friendsinviter/externals/scripts/friendsinviter.js')
    ->appendFile($this->baseUrl() . '/application/modules/Semods/externals/scripts/semods.js')
?>

    <?php if ($this->result != ""): ?>
    <div id="statusmessage" style='margin: 0px auto; width: 500px'>
      <ul class="form-notices"><li><?php echo $this->translate($this->result) ?></li></ul>
    </div>
    <?php endif; ?>
  


    <?php if (!$this->hide_unsubscribe_form) : ?>
  
    <div style="padding: 10px; Xpadding-top: 50px">
      
      <form class="global_form" method="post" action="<?php echo $this->url(array('module' => 'friendsinviter', 'controller' => 'index', 'action' => 'unsubscribe'), 'default', true) ?>" >
      <input type="hidden" name="task" value="unsubscribe">
      
      <div>
      <div>

      <h3><?php echo $this->translate("100010268") ?></h3>
      <p class="form_description"><?php echo $this->translate("100010269") ?></p>

      <?php if ($this->error_message != ""): ?>
      <div id="errormessage" Xstyle='margin-left:340px;'>
        <ul class="form-errors"><li><?php echo $this->translate($this->error_message) ?></li></ul>
      </div>
      <?php endif; ?>

      <table cellpadding="0" cellspacing="0">
      <tr>
      <td class='form1'><?php echo $this->translate('100010270') ?></td>
      <td class='form2'> <input type="text" class="text" name="email" value="<?php echo $this->email ?>" style="width: 200px"> </td>
      </tr>
      <tr>
      <td class='form1'>&nbsp;</td>
      <td class='form2'> <button type="submit" class="button" ?> <?php echo $this->translate('100010271') ?> </button> </td>
      </tr>
      </table>

      </div>
      </div>

      </form>
      
    </div>
    
    <?php endif; ?>

