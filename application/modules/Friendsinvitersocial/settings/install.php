<?php
class Friendsinvitersocial_Installer extends Engine_Package_Installer_Module
{
  function onInstall()
  {

    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);

    parent::onInstall();
  }
}
