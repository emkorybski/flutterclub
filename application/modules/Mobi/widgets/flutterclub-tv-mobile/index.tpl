
<style type="text/css">

	#tv_wrapper {
		margin-top:5px;
		background:#000;
		height:auto;
		margin-bottom:10px;
		
	}	
	
	#tv_wrapper h3
	{
	    background-color:#dbe3e2;
	    border:none;
	    padding:3px;
	
              font-family:fc_bebas;
	    font-size:16px;
	    color:#000;
	}
	
	.video{width:100%; 
	          
		 border:none;
text-align:center;		 
		   }

	.video > iframe
	{
	   
	    border:none;
	    margin-top:5px;
	    margin-bottom:10px;
	}

	#small_video_wrapper
	{
	text-align:center;
	display:none;
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
	
	hr#upper{height:0px;border:1px solid #dbe3e2;margin-top:4px}
	
	

        
</style>

<div id="tv_wrapper">

<h3>
  Flutterclub TV
</h3>
<hr id="upper"/>

<?php

	echo $this->video;
	
?>
</div>