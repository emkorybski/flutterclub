<?php

//require_once('custom/config.php');
//require_once(PATH_DOMAIN . 'competition.php');
//require_once(PATH_DOMAIN . 'user.php');
//require_once(PATH_DOMAIN . 'user_balance.php');

class Widget_FC_FB_NewsfeedController extends Engine_Content_Widget_Abstract
{

	//private $provider = 'facebook';

	public function indexAction()
                
	{
	
	
	
	 $viewer = Engine_Api::_()->user()->getViewer();
	
	 $user_id = $viewer->getIdentity();
	
	$fbTokensTbl = Engine_Api::_()->getDbTable('facebook', 'user');

        $select = $fbTokensTbl->select()->where("user_id = $user_id", 1);
	
	$fetchToken = $fbTokensTbl->fetchAll($select);
	
	//$print_token = print_r($fetchToken[0]['access_token']);
	
	//echo "<br/>" . $user_id;
	
	//$fbTokenInArray = array_key_exists( "access_token", $fetchToken );
	
	
       
	
         if($fetchToken[0]['access_token'] != null) { 
	    
           $app_id = "315323888562930";
           $app_secret = "1616174780cb52fdeafb0d12d46a55f0";
          // $my_url = "http://www.flutterclub.com/fc/pages/leaderboard-test";
	  $my_url = "http://www.flutterclub.com/fc/pages/fb-newsfeed";

      session_start();
             
      $code = $_REQUEST["code"];
      $state = $_REQUEST["state"];

     //echo "<br/>" . $code;
        if(empty($code)) {
                // Redirect to Login Dialog
                 $_SESSION['state'] = md5(uniqid(rand(), TRUE)); // CSRF protection
                $dialog_url = "https://www.facebook.com/dialog/oauth?client_id=" 
                . $app_id . "&redirect_uri=" . urlencode($my_url) . "&state="
                . $_SESSION['state'] . "&scope=read_stream";

            echo("<script> top.location.href='" . $dialog_url . "'</script>");
            }
            
       if($_SESSION['state'] && ($_SESSION['state'] === $_REQUEST['state'])){
     // state variable matches
 //  if(empty($state)) {
    
            $token_url = "https://graph.facebook.com/oauth/access_token?"
       . "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
       . "&client_secret=" . $app_secret . "&code=" . $code;
        
       
       $response = file_get_contents($token_url);
       $params = null;
       parse_str($response, $params);
       
        $_SESSION['access_token'] = $params['access_token'];
   
  $graph_url = "https://graph.facebook.com/me?access_token=" 
      . $params['access_token'];
	
     $graph_url = "https://graph.facebook.com/me/home?access_token="
        . $params['access_token']; 
        
     $user = json_decode(file_get_contents($graph_url));
    
            $this->view->newsfeed_object = $user;
           
        //print_r($user);
		}
	}

 else { 
	
	$this->view->sorry = "Sorry, you need to be logged in with Facebook to use this widget.";	
} 

	}
}

?>