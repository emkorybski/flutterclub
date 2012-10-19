<?php
class User_Model_DbTable_Users extends Engine_Db_Table
{
  protected $_name = 'users';

  protected $_rowClass = 'User_Model_User';
  

  public function getReferrers() {
  {
    $rName = 'invites_stats_user';
    $cName = $this->info('name');
    $cName2 = $this->info('name');
    $select = $this->select()
      ->joinLeft($rName, "`{$cName}`.`user_id` = `{$rName}`.`user_id`", null)
      ->joinLeft($cName2, "`{$cName}`.`user_referer` = `{$cName2}`.`user_id`", null);


    return $select;
  }

}