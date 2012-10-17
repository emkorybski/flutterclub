<?php
class Friendsinviter_Installer extends Engine_Package_Installer_Module
{
  function onInstall()
  {

    // ALTER IGNORE TABLE `engine4_users` ADD `user_referer` INT NOT NULL;

    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);

    try {
      
      $db->query("ALTER TABLE `engine4_users` ADD `user_referer` INT NOT NULL;");
      
    } catch(Exception $ex) {
      
    }

    parent::onInstall();
  }

  public function onDisable() {

    $db = $this->getDb();

    $db->update('engine4_user_signup', array(
      'class' => 'User_Plugin_Signup_Invite',
    ), array(
      'class = ?' => 'Friendsinviter_Plugin_Signup_Invite',
    ));

    return parent::onDisable();
    
  }

  public function onEnable() {

    $db = $this->getDb();

    $db->update('engine4_user_signup', array(
      'class' => 'Friendsinviter_Plugin_Signup_Invite',
    ), array(
      'class = ?' => 'User_Plugin_Signup_Invite',
    ));

    return parent::onEnable();
    
  }
  
}
