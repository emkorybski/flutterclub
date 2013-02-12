<?php
//require_once("competition.php");
//require_once("user.php");
//require_once("user_balance.php");

echo "OK???";

class Blackjack {
    
    
    /**
     *
     * @var tbl_name contains name of the table that the class operates on 
     */
    
    protected $tbl_name = "fc_blackjack";
    
    
    public function test(){
        
        return "OK!";
    }
            
            
    public function __construct(){
        
        $user_online_options = include('C:\\wamp\www\fc\application\settings\database.php');
        echo $user_online_options['params']['host'];
        mysqli_connect($user_online_options['params']['host'],
                      $user_online_options['params']['username'],
                      $user_online_options['params']['password'],
                      $user_online_options['params']['dbname']);
        
    }        
    
    public static function deduct() {
        
        $deduct = mysqli_query("UPDATE ... SET ... WHERE ... AND ...");
        $result_ded = mysqli_free_result($deduct);
        if($result_ded == true){
            return true;
        } else { return false; }
        
    }
    
    
    
    public static function increase(){
        
        $increase = mysqli_query("INSERT INTO ... VALUES()");
        $result_inc = mysqli_free_result($increase);
        if($result_inc == true){
            return true;
        } else { return false; }
        
    }
    
}






?>
