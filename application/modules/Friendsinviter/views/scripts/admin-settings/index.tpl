
<script>
function semods_add_row(id) {
  var el = document.getElementById(id+'_template').cloneNode(true);
  el.id = '';
  var moreRow = document.getElementById(id+"_addmorerow");
  moreRow.parentNode.insertBefore(el, moreRow)
}
</script>

<h2><?php echo $this->translate("Friends Inviter Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
      
    ?>
  </div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>

      <div class="global_form" id="admin_settings_form">
      <?php if ($this->form->saved_successfully): ?><h3 class="slowfade"><?php echo $this->translate("Settings were saved successfully.") ?></h3><?php endif; ?>
      
      <?php echo $this->form->render($this); ?>
      
      </div>

  </div>
</div>



