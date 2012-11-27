
<style type="text/css">

	#tv_wrapper {
		margin-top:5px;
		background:transparent;
		height:auto;
		margin-bottom:0px;
	
		
		
	
	}	
	
	#tv_wrapper h3
	{
	    background-color:#000;
	    border:none;
	    padding:3px;
	
              font-family:fc_bebas;
	    font-size:16px;
	    color:#dbe3e2;
	}
	
	.video{width:100%; 
	          
		 border:none;
                     text-align:center;		 
		   }

	.video > iframe
	{
	   
	    border:1px sold #ff00ff;
	    margin-top:5px;
	    margin-bottom:20px;
	}

	#small_video_wrapper
	{
	text-align:center;
	display:none;
	padding-top:20px;
	color:#dbe3e2;
	  }
	  
	  
	  
	  #small_video_wrapper > iframe
	{
		padding: 10px;
		margin-top:20px;
		z-index:0;
		
	  }
	
	#close
	{
		display:none
	}
	
	hr#upper{height:1px;color:#dbe3e2;border:1px solid #dbe3e2;margin-top:2px}
	
	hr#lower{height:1px;color:#dbe3e2;border:1px solid #dbe3e2;margin-top:-16px}
	
	#close, #view_more
	{
		border-color:#dbe3e2;
		margin-top:10px;
	}
        
</style>

<div id="tv_wrapper">

<h3>
  Flutterclub TV
</h3>
<hr id="upper"/>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>

	jQuery.noConflict();
						
	jQuery(document).ready(function() {
	    
	
	jQuery('#view_more').click(function(){
		jQuery('#small_video_wrapper').show('fast');
		jQuery(this).hide();
		jQuery('#close').show('fast');
		//jQuery(this).text('close');
	   });
	
	
	jQuery('#close').click(function(){
		jQuery('#small_video_wrapper').hide('fast');
		//jQuery(this).text('view more videos');
		jQuery('#view_more').show('fast');
		jQuery(this).hide();
	  });
         
	    
	});
						
</script>


<?php

	echo $this->video;
	
?>
</div>