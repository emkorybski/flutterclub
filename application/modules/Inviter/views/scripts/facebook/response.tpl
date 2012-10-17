<?php if ($this->viewer()->getIdentity()): ?>
<div class="headline">
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

<div id="request-response">
    <?php if($this->state): ?>

    <script type="text/javascript">
        en4.core.runonce.add(function() {
            var t = window.opener;
            //window.opener.location.href = 'inviter/facebook/index/code/<?php echo $this->code;?>';
			window.opener.location.href = '<?php echo $this->url(array('module'=>'inviter', 'controller'=>'facebook', 'action'=>'index', 'state'=>null, 'code'=>$this->code), 'default');?>';
            window.close();
        })
    </script>
    <?php endif;?>
</div>