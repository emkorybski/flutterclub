<?php

   session_start();
  
   
  //FUNCTIONS ---> Ajax callback output!!! 
function bd_con(){
        
        $bd_cnx = @mysql_connect("localhost","fc_admin","_d]M,zZ92u]0");
        return $bd_cnx;
}  
    
    
function deduct() {
        
        bd_con(); 
        
        $ded_float = floatval($_REQUEST['ded_amount']);
        
        if($ded_float == 0){
            exit;
        }
        $u_id = $_SESSION['uid'];
        if($ded_float != 0){
            
    //GET COMPETITION ID  
        $get_comp_id = mysql_query("SELECT id FROM fc_live.fc_competition ORDER BY id DESC LIMIT 1 OFFSET 2");   
        
        $comp_id = mysql_result($get_comp_id,0,0); 
        
    //GET FC USER ID
        $get_fcuser = mysql_query("SELECT id FROM fc_live.fc_user where id_engine4_users=$u_id");   
        
        $fc_user_id = mysql_result($get_fcuser,0,0);    
            
    //UPDATE USER BALANCE
        $get_balance = mysql_query("SELECT balance FROM fc_live.fc_user_balance WHERE iduser=$fc_user_id AND idcompetition=$comp_id");
        
        $balance = mysql_result($get_balance,0,0);
        
        $balance_float = floatval($balance);        
        
        $updated_bal = $balance_float - $ded_float;
        //$gameId = $_SESSION['gameid'];
        mysql_query("UPDATE fc_live.fc_user_balance SET balance=$updated_bal WHERE iduser=$fc_user_id AND idcompetition=$comp_id");
        
        }
}
  
function register_game(){
    
       bd_con();    
       
       $u_id = $_SESSION['uid'];
            
    //GET COMPETITION ID  
       $get_comp_id = mysql_query("SELECT id FROM fc_live.fc_competition ORDER BY id DESC LIMIT 1 OFFSET 2");   
        
       $comp_id = mysql_result($get_comp_id,0,0);        
            
    //GET FC USER ID AND GAME ID AND SAVE THEM IN SESSION VARIABLE
        $get_fcuser = mysql_query("SELECT id FROM fc_live.fc_user where id_engine4_users=$u_id");   
        
        $fc_user_id = mysql_result($get_fcuser,0,0);
         
        $_SESSION['fc_user']= $fc_user_id;
        
    //GET USER INITIAL BALANCE AND SAVE IT IN fc_blackjack
        
         $get_init_bal = mysql_query("SELECT balance FROM fc_live.fc_user_balance WHERE iduser=$fc_user_id AND idcompetition=$comp_id");
        
        $balance_init = mysql_result($get_init_bal,0,0);
	
	if($balance_init == 0)
	{ exit("You need at least 1 bet to play Blackjack. Click Exit button to go back."); }
	
        $balance_init_float = floatval($balance_init); 
        
       //REGISTER GAME, SAVE GAME ID INTO SESSION VARIABLE
      mysql_query("INSERT INTO fc_live.fc_blackjack VALUES('',$fc_user_id,$balance_init_float,'',NOW(),'',$comp_id)");     
       $get_gameid = mysql_query("SELECT id FROM fc_live.fc_blackjack ORDER BY id DESC LIMIT 1");   
        
       $game_id = mysql_result($get_gameid,0,0);
        
       $_SESSION['gameid'] = $game_id;
     
}
    

function exit_game() {
        
        bd_con();    
        //$ded_amount = $_POST['ded_amount'];
        $u_id = $_SESSION['uid'];
        $fc_user = $_SESSION['fc_user'];
        //$status = $_POST['status'];
        $exit_game = floatval($_REQUEST['exit_game']);  
        //$ded_amount = $_POST['ded_amount'];
          
        if(!empty($_REQUEST['exit_game'])){
            
    //GET COMPETITION ID  
       
        $get_comp_id = mysql_query("SELECT id FROM fc_live.fc_competition ORDER BY id DESC LIMIT 1 OFFSET 2");   
        
        $comp_id = mysql_result($get_comp_id,0,0);        
            
    //GET FC USER ID
        $get_fcuser = mysql_query("SELECT id FROM fc_live.fc_user where id_engine4_users=$u_id");   
        
        $fc_user_id = mysql_result($get_fcuser,0,0);
   
    //SAVE FINAL BALANCE FOR USER FROM fc_user_balance TABLE 
        
        $get_fin_bal = mysql_query("SELECT balance 
                                    FROM   fc_live.fc_user_balance 
                                    WHERE  iduser=$fc_user 
                                    AND    idcompetition=$comp_id");
        
        $final_bal = mysql_result($get_fin_bal,0,0);
        $exit_float = floatval($exit_game);
        $fin_bal_value = $exit_float + $final_bal;
        //$fin_bal_float = floatval($fin_bal_value);
        //echo $fin_bal_float;
        $gameId = $_SESSION['gameid'];
        
        mysql_query("UPDATE fc_live.fc_user_balance SET balance=$fin_bal_value WHERE iduser=$fc_user_id AND idcompetition=$comp_id");
        
        $get_initial_bal = mysql_query("SELECT balance_init FROM fc_live.fc_blackjack WHERE user_id=$fc_user_id AND comp_id=$comp_id AND id=$gameId");
        $initial_bal = mysql_result($get_initial_bal,0,0);
        $bal_diff = $initial_bal - $final_bal;
        $amount_won_lost = $exit_game - $bal_diff;
        
        mysql_query("UPDATE fc_live.fc_blackjack SET game_finished=NOW(),amount_won_lost='$amount_won_lost' WHERE user_id=$fc_user_id AND comp_id=$comp_id AND id=$gameId");
        
    }   
}    


//
// GAME INT. LOGICS
//
  
    if(isset($_SESSION['uid']) && isset($_REQUEST['status'])){
        //$con = bd_con();
       // $sessid_count = count($_SESSION);
        
        if(!$_SESSION['gameid'] && !$_SESSION['fc_user']  && $_REQUEST['status'] == 1 && $_REQUEST['exit_game'] == ''){
           
           register_game();
           deduct();
           echo "Deducted and registered!";
	   
        }
        else if($_SESSION['gameid'] && $_SESSION['fc_user'] && $_REQUEST['ded_amount'] && $_REQUEST['status'] == 1 && $_REQUEST['exit_game'] == ''){
            deduct();
            echo "Deducted!";
        }
        else if( $_REQUEST['status'] == 0 && $_SESSION['gameid'] && $_SESSION['fc_user']  && $_REQUEST['ded_amount'] == ''){
            exit_game();
            unset($_SESSION['fcuser']);
	    unset($_SESSION['gameid']);
	    $_SESSION[] = array();
            echo "Exit successful!"; 
            
        } else { exit("Exiting the game!"); }
    }
?>
