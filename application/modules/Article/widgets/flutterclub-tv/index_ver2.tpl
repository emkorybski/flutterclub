
<style type="text/css">

@import "/fc/custom/fonts/nunito/stylesheet.css";

	#container{padding-top:10px;padding-bottom:10px;text-align:center;margin-left:auto;margin-right:auto;width:620px}

	#tv_wrapper {
		margin-top:20px;
		
		background:transparent;
		height:auto;
		margin-bottom:0px;
		border-bottom:3px solid #dbe3e2;
		border-top:3px solid #dbe3e2;
		
	}	
	
	#list p
	{
	    background-color:#000;
	    border:3px solid #dbe3e2;
	    border-radius: 7px;
	    padding:10px;
	text-align:center;
              font-family:fc_bebas;
	    font-size:16px;
	    color:#dbe3e2;
	    margin-bottom:22px;
	    width:120px;
	}
	a.video span#new{color:#ff00ff;font-family:"nunitobold";}
	
	#list ul{margin-bottom:35px}

	#list{margin-left:2px;margin-top:-245px;}
	
	#list a{font-family:"nunitobold";font-size:18px}
	#list span.pointer{font-family:"nunitobold";font-size:18px;color:#aa0088;visibility:hidden}
	#list span.pointer_show{font-family:"nunitobold";font-size:18px;color:#aa0088;visibility:visible}
	#list a:hover{text-decoration:none;color:#ff00ff}
	
	iframe{margin-left:105px}
	

</style>

<div id="tv_wrapper">
<script type="text/javascript" src="mootools-1.2.5-core-nc.js">
	</script>
<script>
window.addEvent('domready', function()
{

$$('#one span').addClass('pointer_show');

$$('#one a').addEvent('click', function() {			
		
					$$('li span').removeClass('pointer_show');
					$$('#one span').addClass('pointer_show');
					

});	

  $$('#two a').addEvent('click', function() {			
		
					$$('li span').removeClass('pointer_show');
					$$('#two span').addClass('pointer_show');
					

});	

$$('#three a').addEvent('click', function() {			
		
					$$('li span').removeClass('pointer_show');
					$$('#three span').addClass('pointer_show');
					

});	

$$('#four a').addEvent('click', function() {			
		
					$$('li span').removeClass('pointer_show');
					$$('#four span').addClass('pointer_show');
					

});	

$$('#five a').addEvent('click', function() {			
		
					$$('li span').removeClass('pointer_show');
					$$('#five span').addClass('pointer_show');
					

});	

$$('#six a').addEvent('click', function() {			
		
					$$('li span').removeClass('pointer_show');
					$$('#six span').addClass('pointer_show');
					

});

$$('#seven a').addEvent('click', function() {			
		
					$$('li span').removeClass('pointer_show');
					$$('#seven span').addClass('pointer_show');
					

});
     	 	
});
		
</script>





<?php

	echo $this->video;
	
?>



</div>