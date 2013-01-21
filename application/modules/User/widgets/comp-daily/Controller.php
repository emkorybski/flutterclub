<?php


class User_Widget_CompDailyController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
            //$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            
         $this->view->partial = "<p>No current daily competitions.</p>";
          
	 //$choicesTable =  Engine_Api::_()->getDbTable('user_daily','user');
	 
	 
            
            //foreach($userChoices as $userChoice){
            
           // mysql_query("INSERT INTO fc_daily_competiton VALUES" . "(NULL,$user_id,NULL,$userChoice,CURDATE())");
            
            //$choicesTable->insert($userChoices);
            }
            
      // } 
}
        
        