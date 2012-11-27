
<style type="text/css">

@import "/fc/custom/fonts/nunito/stylesheet.css";

	#container{padding-top:10px;padding-bottom:10px;margin-left:auto;margin-right:auto;width:600px;}

	#tv_wrapper {
		margin-top:20px;
		
		background:transparent;
		height:auto;
		text-align:center;
		margin-bottom:0px;
		border-bottom:3px solid #dbe3e2;
		border-top:3px solid #dbe3e2;
		min-width:620px;
	}	
	
         p#fc_tv
	{
	    background-color:#000;
	    border:3px solid #dbe3e2;
	    border-radius: 7px;
	    padding:10px;
	    text-align:center;
              font-family:fc_bebas;
	    font-size:16px;
	    color:#dbe3e2;
	    margin-bottom:20px;
	    width:100px;
	}
	
	
	
	span.new{font-family:"nunitobold";font-size:12px;color:#ff00ff}
	
	ul#videos{margin-bottom:15px;margin-right:5px}

	
	
	 ul#videos li{width:135px;border:1px solid:#fff}
	
	
	ul#videos li a.video{font-family:"nunitobold";font-size:16px;width:114px;background:#333;padding:3px;display:block;margin-bottom:3px;padding-left:8px}
	
	

	ul#videos li a:hover{text-decoration:none;color:#ff00ff}
	
	iframe{margin-left:155px;margin-top:-340px;border:none;padding-top:15px;padding-bottom:15px;}
	#gif{position:absolute;display:none;top:528px;left:50%}	
	

</style>


<script type="text/javascript" src="mootools-1.2.5-core-nc.js">
	</script>
<script>
window.addEvent('domready', function()
{

$$('#hide_gif').addEvent('load', function()
{
	$$('#gif').setStyle('display','none');	
	});

$$('#one a.video').setStyle('border-right','1px solid #ff00ff');
$$('#one a.video').setStyle('color','#ff00ff');
$$('#gif').setStyle('display','block');


  $$('#one a.video').addEvent('click', function() {			
		
		$$('#one a.video').setStyle('border-right','1px solid #ff00ff');
		$$('#three a, #two a, #four a, #five a, #six a, #seven a').setStyle('border-right','none');	
		$$('#one a.video').setStyle('color','#ff00ff');	
$$('#three a, #two a, #four a, #five a, #six a, #seven a').setStyle('color','#aa0088');		
$$('#gif').setStyle('display','block');
					
});	

  $$('#two a').addEvent('click', function() {	
  
		$$('#two a.video').setStyle('border-right','1px solid #ff00ff');
		$$('#three a, #one a, #four a, #five a, #six a, #seven a').setStyle('border-right','none');
$$('#two a').setStyle('color','#ff00ff');	
$$('#three a, #one a, #four a, #five a, #six a,#seven a').setStyle('color','#aa0088');
$$('#gif').setStyle('display','block');

});	

$$('#three a').addEvent('click', function() {			
		
		$$('#three a.video').setStyle('border-right','1px solid #ff00ff');
		$$('#two a, #one a, #four a, #five a, #six a, #seven a').setStyle('border-right','none');
$$('#three a').setStyle('color','#ff00ff');
$$('#two a, #one a, #four a, #five a, #six a, #seven a').setStyle('color','#aa0088');
$$('#gif').setStyle('display','block');

});	

$$('#four a').addEvent('click', function() {			
		
		$$('#four a.video').setStyle('border-right','1px solid #ff00ff');
		$$('#three a, #one a, #two a, #five a, #six a, #seven a').setStyle('border-right','none');
$$('#four a').setStyle('color','#ff00ff');
$$('#three a, #one a, #two a, #five a, #six a, #seven a').setStyle('color','#aa0088');
$$('#gif').setStyle('display','block');

});	

$$('#five a').addEvent('click', function() {			
		
			$$('#five a.video').setStyle('border-right','1px solid #ff00ff');
		$$('#three a, #one a, #four a, #two a, #six a, #seven a').setStyle('border-right','none');
$$(' #five a').setStyle('color','#ff00ff');
$$('#three a, #one a, #four a, #two a, #six a, #seven a').setStyle('color','#aa0088');
$$('#gif').setStyle('display','block');

});	

$$('#six a').addEvent('click', function() {			
		
			$$('#six a.video').setStyle('border-right','1px solid #ff00ff');
			$$('#six a.video').setStyle('color','#ff00ff');
			$$('#two a, #three a, #four  a, #five a, #one a, #seven a').setStyle('border-right','none');
			$$('#three a, #one a, #four a, #two a, #five a, #seven a').setStyle('color','#aa0088');
			$$('#gif').setStyle('display','block');

});
   

$$('#seven a').addEvent('click', function() {			
		
			$$('#seven a.video').setStyle('border-right','1px solid #ff00ff');
			$$('#seven a.video').setStyle('color','#ff00ff');
			$$('#two a, #three a, #four a,  #five a, #one a, #six a').setStyle('border-right','none');
			$$('#three a, #one a, #four a, #two a, #five a, #six a').setStyle('color','#aa0088');
			$$('#gif').setStyle('display','block');

});
});
		
</script>



<div id="tv_wrapper">

<?php

	echo $this->video;
	
?>



</div>