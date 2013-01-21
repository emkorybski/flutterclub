<?php

namespace bets;

require_once(PATH_DOMAIN . 'competition.php');

class Get_Fc_User
{

        public static function getFcUser() { 

		$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		
		$db_con = mysqli_connect('localhost','fc_admin','_d]M,zZ92u]0','fc_live') or die('Could not connect to server');
		$sel_query = "SELECT id FROM fc_user WHERE id_engine4_users=$user_id";
		$get_fc_user_id = mysqli_query($db_con,$sel_query);
		$fc_user_id = mysqli_free_result($get_fc_user_id);
		//	 echo $sel_query;
		if(empty($fc_user_id){
			exit("There was a problem, sorry");
		}
	        return $fc_user_id;
		}
		
}

?>