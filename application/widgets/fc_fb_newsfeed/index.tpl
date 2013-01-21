<?php

	ob_implicit_flush(true);
	
	for($i=0; $i > 2; $i++){
		 echo '<img id="gif" src="/fc/custom/images/loading1.gif" />';
	        for($j=0; $j < 40000; $j++)
	               echo '';
	}

?>
        
        <style type="text/css">
	
	.layout_fc_fb_newsfeed{
		background:#efefef;
	     
		-webkit-border-top-left-radius: 6px;
		-webkit-border-top-right-radius: 6px;
		-moz-border-top-left-radius: 6px;
		-moz-border-top-right-radius: 6px;
		border-top-left-radius: 6px;
		border-top-right-radius: 6px; 
		margin:0px;
		padding-bottom:10px;
	
	}
	
	   
	
            .profile_name{font-family:Trebuchet MS;font-weight:bold;color:#990000}
            
            .profile_message{font-family:Trebuchet MS;font-weight:normal;color:#990000;margin-left:20px;vertical-align:middle}
            
            .profile_img{margin-left:20px;border:4px solid #990000}
            
            #fb_newsfeed{margin-top:10px;background:#efefef;border:none;padding:10px;width:90%;margin-bottom:15px;margin-left:2%}
          
             .post_wrap{border:1px solid #990000;padding:10px;margin-bottom:5px;}
	    
            .profile_comments{margin-left:20px;font-size:12px;color:#990000;font-family: Trebuchet MS}
	    
	 #sorry{background:#fff;color:#990000;padding:10px;margin-left:15px;margin-right:15px;margin-bottom:15px}
	 
	/* #gif{margin-top:5px;margin-left:5px;padding:5px}	 */
	    
        </style>
	


<?php


       // $viewer = Engine_Api::_()->user()->getViewer();

       // $token = $tokensTbl->getUserToken($viewer->getIdentity(), $this->provider);

  //print_r($user->data[11]->properties);
  
   //echo $this->print_token;
   
   
  
   $arr_length = count($this->newsfeed_object->data);
   
   echo "<br/>";
   //echo "Hello " . $user->data;
   //echo "Hello " . $user->name;
   //echo $arr_length;    
        // parse_str($response, $params);
   //echo "<br/><br/>";
  
   if($arr_length == 0){
   
      echo '<p id="sorry">';
	echo $this->sorry;
      echo '</p>';
   } else {
  
 echo '<div id="fb_newsfeed">';
 //echo $this->loader;
 

   for($i = 0; $i < $arr_length; $i++)
{
       
      echo '<div class="post_wrap">';
       
       if(isset($this->newsfeed_object->data[$i]->from->name)){
          
          echo '<p class="profile_name">' . $this->newsfeed_object->data[$i]->from->name . '</p>';
          
        } 
       if(isset($this->newsfeed_object->data[$i]->story)){
        
        echo '<p class="profile_message">' . $this->newsfeed_object->data[$i]->story . '</p>';
        
        }
        
       if(isset($this->newsfeed_object->data[$i]->message)){
         
          echo '<p class="profile_message">' . $this->newsfeed_object->data[$i]->message . '</p>';
         
        } 
 /*      if(isset($this->newsfeed_object->data[$i]->picture)){
          echo '<img class="profile_img" src=' . $this->newsfeed_object->data[$i]->picture . '/>';
        }
        
        if(isset($this->newsfeed_object->data[$i]->properties[0]->href)){
          echo '<img class="message_photo" src=' . $this->newsfeed_object->data[$i]->properties[0]->href . '/>';
        }
 */       
        
       if(isset($this->newsfeed_object->data[$i]->comments->count)){
         echo '<p class="profile_comments">Comments: ' . $this->newsfeed_object->data[$i]->comments->count . '</p>';
	 echo '<hr/>';
        }
     
        
/* if(isset($comments)){ 
        foreach($comments as $comment){
            
            echo $comment . " | ";
        }
} */

          echo '</div>';

     // print_r($user->data[$i]);     
       //}
        }
	
	 echo '</div>';
  }

   ?>
   
 


