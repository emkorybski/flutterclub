<?php

	class Article_Widget_FlutterclubTvController extends Engine_Content_Widget_Abstract
	{
		public function indexAction() {
		
		$this->view->video='<div class="video">
						<iframe width="480" height="270" src="http://www.youtube.com/embed/KUzp3JLT-bE" frameborder="0" allowfullscreen></iframe>
						<br>
						
						<button id="view_more">view more videos</button>
						<button id="close">close</button>
						<hr id="lower">
						 <div id="small_video_wrapper">
						    
						   <iframe src="http://player.vimeo.com/video/52233374?badge=0" width="220" height="169" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
						   
						   <iframe width="220" height="169" src="http://www.youtube.com/embed/hGQkTno78BA" frameborder="0" allowfullscreen></iframe>
						 </div>
						
					      </div>'; 
					      
					      
	
		}
	
	}



?>