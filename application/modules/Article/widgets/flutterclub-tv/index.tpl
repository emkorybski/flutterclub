
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
	   
	    border:none;
	    margin-top:5px;
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
		margin-top:0px;
	}
        
</style>

<div id="tv_wrapper">

<h3>
  Flutterclub TV
</h3>
<hr id="upper"/>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>
						
	$(document).ready(function() {
	    
	
	$('#view_more').click(function(){
		$('#small_video_wrapper').show('fast');
		$(this).hide();
		$('#close').show('fast');
		//$(this).text('close');
	   });
	
	
	$('#close').click(function(){
		$('#small_video_wrapper').hide('fast');
		//$(this).text('view more videos');
		$('#view_more').show('fast');
		$(this).hide();
	  });
         
	    
	});
						
</script>


<?php

	echo $this->video;
	
?>
</div>