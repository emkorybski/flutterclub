<?php

class Friendsinviter_Widget_ListTopinviterController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $max_items = 5;

    $table = Engine_Api::_()->getItemTable('user');
    $userTableName = $table->info('name');

    $rTable = Engine_Api::_()->getDbtable('stats', 'friendsinviter');
    $rName = $rTable->info('name');
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($rName, array('invites_sent'))
      ->joinLeft($userTableName, "`{$userTableName}`.`user_id` = `{$rName}`.`user_id`" )
      ->where('search = ?', 1)
      ->where('invites_sent != 0')
      ->order('invites_sent DESC')
      ->limit($max_items);
      

    $users = $table->fetchAll($select);
    
    if( count($users) < 1 )
    {
      return $this->setNoRender();
    }

    $this->view->users = $users;
  }

  public function getCacheKey()
  {
    return true;
  }
}