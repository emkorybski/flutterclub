<?php
	

$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		
		$db_con = mysql_connect('localhost','fc_admin','_d]M,zZ92u]0') or die('Could not connect to server');
		$sel_query = "SELECT id FROM fc_user WHERE id_engine4_users=$user_id";
		$get_fc_user_id = mysql_query($sel_query, $db_con);
		$fc_user_id = mysql_result($get_fc_user_id,0,0);
			echo $fc_user_id;
	        return $fc_user_id;
?>