<?php

	class Article_Widget_FlutterclubTvController extends Engine_Content_Widget_Abstract
	{
		public function indexAction() {
		
		$this->view->video='<div class="video">
						<iframe width="480" height="330" src="http://www.youtube.com/embed/zBy_724kWdA" frameborder="0" allowfullscreen></iframe><br>
						
						
						<button id="view_more">view more videos</button>
						<button id="close">close</button>
						<hr id="lower">
						 <div id="small_video_wrapper">
						    
						    Sorry, no more videos yet. 
						    
						 </div>
						
					      </div>'; 
					      
					      
	
		}
	
	}



?>