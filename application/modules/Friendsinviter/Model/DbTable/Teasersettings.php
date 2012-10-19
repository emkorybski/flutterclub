<?php
class Friendsinviter_Model_DbTable_Teasersettings extends Engine_Db_Table
{

  protected $_name = 'friendsinviter_teasersettings';


  public function setEnabled(User_Model_User $user, $enabled)
  {

    $db = $this->getAdapter();
    $tableName = $this->info("name");
    $enabled = (int)$enabled;

    $sql = "INSERT INTO $tableName (user_id, enabled)
              VALUES ( ?, ? )
              ON DUPLICATE KEY UPDATE
              user_id = ?,
              enabled = ?";

    $values = array('user_id' => $user->getIdentity(),
                    'enabled'  => $enabled,
                    );

    $db->query($sql, array_merge(array_values($values), array_values($values)));

  }

  public function checkEnabled(User_Model_User $user)
  {
    $select = $this->select()
      ->where('user_id = ?', $user->getIdentity())
      ->limit(1);

    $row = $this->fetchRow($select);

    if( null === $row )
    {
      return true;
    }

    return (bool) $row->enabled;
  }

  public function showForAll()
  {

    $db = $this->getAdapter();
    $tableName = $this->info("name");

    $db->query("TRUNCATE $tableName");

  }
  
}