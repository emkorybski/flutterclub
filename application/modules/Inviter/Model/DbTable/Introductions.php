<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Introductions.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Model_DbTable_Introductions extends Engine_Db_Table
{
  protected $_rowClass = "Inviter_Model_Introduction";

  public function getRandomIntroduction($user_id, $exclude_ids = array())
  {
    $exclude_ids = (count($exclude_ids) == 0) ? array(0) : $exclude_ids;
    $exclude_ids[] = $user_id;

    $membershipTbl = Engine_Api::_()->getDbtable('membership', 'user');
    $sub_select = "SELECT user_id FROM {$membershipTbl->info('name')} WHERE resource_id = {$user_id}";

    $select = $this->select()
      ->where('publish = ?', 1)
      ->where('body != ""')
      ->where('user_id NOT IN (?)', $exclude_ids)
      ->where('user_id NOT IN (' . $sub_select . ')')
      ->order('RAND()')
      ->limit(1);

    return $this->fetchRow($select);
  }

  public function getUserIntroduction($user_id)
  {
    $select = $this->select()
      ->where('user_id = ?', $user_id);

    $userIntroduce = $this->fetchRow($select);
    
    if ($userIntroduce === null) {
      $userIntroduce = $this->createRow(array('user_id' => $user_id));
      $userIntroduce->save();
    }

    return $userIntroduce;
  }
}
