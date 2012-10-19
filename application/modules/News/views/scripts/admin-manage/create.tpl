<?php
	$this->headLink()    	  
    	  ->appendStylesheet( 'application/modules/News/externals/styles/main.css');  
          
?>
<style type="text/css">
   
</style>
<div id='global_content_wrapper'> 
    <div id='global_content'> 
<h2><?php echo $this->translate("News Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
  <div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>
</div>