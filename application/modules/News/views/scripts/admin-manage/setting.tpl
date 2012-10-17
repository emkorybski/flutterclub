<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    News
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7253 2010-09-01 20:40:55Z jung $
 * @author     John
 */
?>	
<?php
	$this->headLink()    	  
    	  ->appendStylesheet( 'application/modules/News/externals/styles/main.css');  
          
?>
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
	  	<div style="color:#717171;font-weight:bold;padding-bottom:5px;"><?php echo($this->mess); ?></div>
	    <?php echo $this->form->render($this) ?>
	  </div>
	  </div>
	</div>
</div>