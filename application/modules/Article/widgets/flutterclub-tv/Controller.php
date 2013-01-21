<?php

	class Article_Widget_FlutterclubTvController extends Engine_Content_Widget_Abstract
	{
		public function indexAction() {
		
		$this->view->video='<div id="container">

			
				<p id="fc_tv">fc tv</p>
				<img id="gif" src="/fc/custom/images/loading1.gif" /><br/>
				<iframe id="hide_gif" name="videos" width="230" height="130" src="http://www.youtube.com/embed/hGQkTno78BA" frameborder="0" allowfullscreen></iframe>
				<button id="see_more">See more</button>
				<ul id="videos">
				
				<!-- <span class="new" >NEW!</span> -->
				<li  id="eight"><a  class="video" href="http://www.youtube.com/embed/hGQkTno78BA" target="videos">Dani</a></li>
				<li  id="one"><a  class="video" href="http://www.youtube.com/embed/3LsGOa9J__s" target="videos">Molly</a></li>
				<li  id="two"><a class="video" href="http://www.youtube.com/embed/vHqpEdwYwbw" target="videos">Stephanie</a></li>
				<li  id="three"><a  class="video" href="http://www.youtube.com/embed/lNCD_Kk8NNk" target="videos"> Krissa</a></li>
				
					<li id="four"><a class="video" href="http://www.youtube.com/embed/yGZtIucokfQ" target="videos">Daisy </a></li>
					<li id="five"><a  class="video" href="http://www.youtube.com/embed/a2uri5xjFU8" target="videos">Jennifer</a></li>	
					
				<li id="six"><a  class="video" href="http://www.youtube.com/embed/cEw64psrGFo" target="videos">Kendall</a></li>
					<li id="seven"><a  class="video" href="http://player.vimeo.com/video/52233374?badge=0" target="videos">Welcome</a></li>
			
				</ul>
			
		</div>';

		
		
		
					      
					      
	
		}
	
	}



?>