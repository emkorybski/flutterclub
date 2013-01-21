<?php

    $userId = $_POST['hiddenValue'];
    $userId = mysql_real_escape_string($userId);
    $fdata = $_POST['fdata'];
    $fdata = mysql_real_escape_string($fdata);  
    $userChoices = $_POST['choicesSubmit'];

//echo $userChoices[4];

    $count = count($userChoices); 
             
  //        echo $count . "||" . $fdata; 
    //$realCount = $count - 1;
      
if(isset($userChoices) && $count > 1 && $count == $fdata)
{
          
         //$user_online_options = include('/fc/application/settings/database.php');
         //echo $user_online_options['params']['host'];
  /*       mysql_connect($user_online_options['params']['host'],$user_online_options['params']['username'],$user_online_options['params']['password']);  */
	 
	  $dbc = mysql_connect('localhost','fc_admin','_d]M,zZ92u]0') or die('Could not connect to server');
         
         //mysql_select_db('fc_demo');
         $check = "SELECT count(id) FROM fc_live.engine4_user_daily WHERE user_id=$userId";
         //mysql_query ONLY allows one query at a time!!!!
         
         $select = mysql_query($check, $dbc);
         $check_result = mysql_result($select,0,0);
         //echo $check_result;
         
         if($check_result != 0)
         { exit("Sorry, your record for this competiton already exists."); }

          $values = array();
              for($i=0; $i < $count; $i++){
              $values[] = '('.$userId.',\'' .$userChoices[$i].'\')';
              
             // mysql_query("INSERT INTO fc_live.engine4_user_daily (user_id,bet_value) VALUES " . "($userId,'$userChoices[$i]')", $dbc);
                 
              }
              
            $valuesIMPL = implode(", ", $values);  
             //print_r($valuesIMPL); 
            $insert = "INSERT INTO fc_live.engine4_user_daily (user_id,bet_value) VALUES $valuesIMPL";
            mysql_query($insert, $dbc);
           //print_r($insert);
              echo "Your choices were submitted.";
          
         
} else { echo "Sorry, there was a problem submitting your choices. Check if your fields are not empty."; }
          


       
?>
