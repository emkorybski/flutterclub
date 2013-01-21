
<style type="text/css">

@import "/fc/custom/fonts/nunito/stylesheet.css";

	#container{padding-top:10px;padding-bottom:10px;margin-left:auto;margin-right:auto;width:240px;}

	#tv_wrapper {
		margin-top:22px;
		margin-right:5px;
		background:transparent;
		height:auto;
		text-align:center;
		margin-bottom:0px;
		border-bottom:3px solid #dbe3e2;
		border-top:3px solid #dbe3e2;
		
	}	
	
         p#fc_tv
	{
	    background-color:#000;
	    border:3px solid #dbe3e2;
	    border-radius: 7px;
	    padding-top:3px;
	    padding-bottom:3px;
	    text-align:center;
              font-family:fc_bebas;
	    font-size:16px;
	    color:#dbe3e2;
	    margin-bottom:5px;
	    width:231px;
	}
	
	#see_more{width:236px;margin-top:-20px}
	
	span.new{font-family:"nunitobold";font-size:12px;color:#ff00ff}
	
	ul#videos{margin-bottom:15px;margin-right:5px;margin-top:10px;display:none}

	
	
	 ul#videos li{width:220px;border:1px solid:#fff}
	
	
	ul#videos li a.video{font-family:"nunitobold";font-size:16px;width:220px;background:#333;padding:3px;display:block;margin-bottom:3px;padding-left:8px}
	
	

	ul#videos li a:hover{text-decoration:none;color:#ff00ff}
	
	iframe{margin-left:5px;margin-top:4px; margin-bottom:10px;border:none;padding-top:5px;padding-bottom:5px;}
	#gif{position:absolute;display:none;margin-top:5px;margin-left:5px}	
	

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
	
$$('#global_header').addEvent('load', function()
{
	$$('#gif').setStyle('display','none');	
	});


	
$$('#see_more').addEvent('click', function()
{

	var gtextval = $$('#see_more').get('text');
	
	if(gtextval == 'See more'){
	$$('#videos').setStyle('display','block');
	$$('#see_more').empty();
	$$('#see_more').set('text','Hide menu');
	}
	if(gtextval == 'Hide menu'){
		$$('#videos').setStyle('display','none');
		$$('#see_more').empty();
		$$('#see_more').set('text','See more');
	
	}
});

$$('#eight a.video').setStyle('border-left','1px solid #ff00ff');
$$('#eight a.video').setStyle('color','#ff00ff');
$$('#gif').setStyle('display','block');

  $$('#eight a.video').addEvent('click', function() {			
		
		$$('#eight a.video').setStyle('border-left','1px solid #ff00ff');
		$$('#one a, #three a, #two a, #four a, #five a, #six a, #seven a').setStyle('border-left','none');	
		$$('#eight a.video').setStyle('color','#ff00ff');	
$$('#three a, #two a, #one a, #four a, #five a, #six a, #seven a').setStyle('color','#aa0088');		
$$('#gif').setStyle('display','block');
					
});


  $$('#one a.video').addEvent('click', function() {			
		
		$$('#one a.video').setStyle('border-left','1px solid #ff00ff');
		$$('#three a, #eight a, #two a, #four a, #five a, #six a, #seven a').setStyle('border-left','none');	
		$$('#one a.video').setStyle('color','#ff00ff');	
$$('#three a, #two a, #four a, #eight a, #five a, #six a, #seven a').setStyle('color','#aa0088');		
$$('#gif').setStyle('display','block');
					
});	

  $$('#two a').addEvent('click', function() {	
  
		$$('#two a.video').setStyle('border-left','1px solid #ff00ff');
		$$('#three a, #one a, #four a, #five a, #six a, #seven a, #eight a').setStyle('border-left','none');
$$('#two a').setStyle('color','#ff00ff');	
$$('#three a, #one a, #four a, #five a, #six a,#seven a, #eight a').setStyle('color','#aa0088');
$$('#gif').setStyle('display','block');

});	

$$('#three a').addEvent('click', function() {			
		
		$$('#three a.video').setStyle('border-left','1px solid #ff00ff');
		$$('#two a, #one a, #four a, #five a, #six a, #seven a, #eight a').setStyle('border-left','none');
$$('#three a').setStyle('color','#ff00ff');
$$('#two a, #one a, #four a, #five a, #six a, #seven a, #eight a').setStyle('color','#aa0088');
$$('#gif').setStyle('display','block');

});	

$$('#four a').addEvent('click', function() {			
		
		$$('#four a.video').setStyle('border-left','1px solid #ff00ff');
		$$('#three a, #one a, #two a, #five a, #six a, #seven a, #eight a').setStyle('border-left','none');
$$('#four a').setStyle('color','#ff00ff');
$$('#three a, #one a, #two a, #five a, #six a, #seven a, #eight a').setStyle('color','#aa0088');
$$('#gif').setStyle('display','block');

});	

$$('#five a').addEvent('click', function() {			
		
			$$('#five a.video').setStyle('border-left','1px solid #ff00ff');
		$$('#three a, #one a, #four a, #two a, #six a, #seven a, #eight a').setStyle('border-left','none');
$$(' #five a').setStyle('color','#ff00ff');
$$('#three a, #one a, #four a, #two a, #six a, #seven a,#eight a').setStyle('color','#aa0088');
$$('#gif').setStyle('display','block');

});	

$$('#six a').addEvent('click', function() {			
		
			$$('#six a.video').setStyle('border-left','1px solid #ff00ff');
			$$('#six a.video').setStyle('color','#ff00ff');
			$$('#two a, #three a, #four  a, #five a, #one a, #seven a,#eight a').setStyle('border-left','none');
			$$('#three a, #one a, #four a, #two a, #five a, #seven a,#eight a').setStyle('color','#aa0088');
			$$('#gif').setStyle('display','block');

});
   

$$('#seven a').addEvent('click', function() {			
		
			$$('#seven a.video').setStyle('border-left','1px solid #ff00ff');
			$$('#seven a.video').setStyle('color','#ff00ff');
			$$('#two a, #three a, #four a,  #five a, #one a, #six a,#eight a').setStyle('border-left','none');
			$$('#three a, #one a, #four a, #two a, #five a, #six a, #eight a').setStyle('color','#aa0088');
			$$('#gif').setStyle('display','block');

});
});
		
</script>



<div id="tv_wrapper">

<?php

	echo $this->video;
	
?>



</div>