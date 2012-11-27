<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 8464 2011-02-15 02:53:07Z john $
 * @author     John
 */
?>


<script type="text/javascript" src="mootools-1.2.5-core-nc.js">
</script>


<script type="text/javascript">
window.addEvent('domready', function()
{
		
			var images = document.getElements('#sidebar div');
			var countImages = images.length;
			
			//alert(countImages);
			
	if( countImages <= 9 )		
	{
		$$('#show').setStyle('visibility', 'hidden');
        }
			
	if( countImages > 9 )
	{	
			for(counter = 0; counter < countImages - 9; counter++)
			{
				var hideImages = images[9+counter];
				
					hideImages.addClass('hide');
					//hideImages.setStyle('display', 'none');

			}
			
$$('#show').addEvent('click', function() {			
			
	var getText = $$('#show').get('text');
		
	if(getText == 'Show less')
		        	
	{		
				
		

			for(i = 0; i < countImages - 9; i++)
				{
					var showImage = images[9+i];	
				
					showImage.removeClass('show');
					showImage.addClass('hide');
					
				
				}
				//this.addClass('firstClick');	
				//var text = this.get('text');	
				this.empty();
				var changed_text = this.set('text', 'Show more');
				
				
				
	}	  

	if(getText == 'Show more')
		        	
	{			
	       
			
			
				for(j = 0; j < countImages - 9; j++)
					{
						var showedImage = images[9+j];	
				
						showedImage.removeClass('hide');
						showedImage.addClass('show');
				
					}	
				//$$('.firstClick').destroy();
				this.empty();
				var new_text = this.set('text','Show less');

			
	}
	
});	

}
     	 	
});
		
</script>

<style>
	
		
		#sidebar 
		{ 
		   padding:0px;
		   }
	
		  #show { clear:left;margin-top:15px;color:#aa0088}
	
           	#show:hover { cursor:pointer }
		  
		  .hide { display:none }
		  
		  .show { display:block}
		
	</style>


<!--
<h3><?php echo $this->count ?> Members Online</h3>
-->

<div>
  <div id="sidebar">
  <?php foreach( $this->paginator as $user ): ?>
    <div class='whosonline_thumb'>
      <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', $user->getTitle()), array('title'=>$user->getTitle())) ?>
    </div>
  <?php endforeach; ?>
   <br/>
   <hr style="width:180px;"/>
   <a id="show">Show more</a>
   </div>		
  
  <?php if( $this->guestCount ): ?>
    <div class="online_guests">
      <?php echo $this->translate(array('%s guest online', '%s guests online', $this->guestCount),
          $this->locale()->toNumber($this->guestCount)) ?>
    </div>
  <?php endif ?>
</div>
