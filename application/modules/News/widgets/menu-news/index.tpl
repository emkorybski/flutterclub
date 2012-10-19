<?php if($this->flag == true):?>
<div class="headline">
  <h2>
    <?php echo $this->translate('News');?>
  </h2>
  <?php if( count($this->navigation) > 0 ): ?>
    <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
  <?php endif; ?>
</div>
<?php  endif;?>
