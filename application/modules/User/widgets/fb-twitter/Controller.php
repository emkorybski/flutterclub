<?php



	class User_Widget_FbTwitterController extends Engine_Content_Widget_Abstract
	{
		public function indexAction() {
		
		$this->view->fb_tw_buttons='<div id="fb"><iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com/flutterclub&amp;send=false&amp;layout=standard&amp;width=250&amp;show_faces=true&amp;action=like&amp;colorscheme=light&amp;font&amp;height=35" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:250px; height:32px;margin-top:-12px;margin-left:0px;padding:none" allowTransparency="true"></iframe></div>


		<div id="twtr"><iframe allowtransparency="true" frameborder="0" scrolling="no"

      src="//platform.twitter.com/widgets/follow_button.html?screen_name=flutterclub"

      style="width:300px; height:20px;margin-left:257px;margin-top:-50px;"></iframe></div>';


		}
	
	}



?>