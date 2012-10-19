<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Featureds.php 2010-07-02 19:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_Model_DbTable_Featureds extends Engine_Db_Table
{
  protected $_rowClass = "Hecore_Model_Featured";

  public function multiSetFeatured($user_ids, $featured = true)
  {
    if (!$user_ids) {
      return $user_ids;
    }

    $this->delete($this->getAdapter()->quoteInto('user_id IN (?)', $user_ids));

    if ($featured) {
      foreach ($user_ids as $user_id) {
        $this->createRow(array('user_id' => $user_id))->save();
      }
    }
  }

  public function setFeatured($user_id, $featured = false)
  {
    if (!$user_id) {
      return;
    }

    $this->delete($this->getAdapter()->quoteInto('user_id = ?', $user_id));

    if ($featured) {
      $this->createRow(array('user_id' => $user_id))->save();
    }
  }

  public function getFeatureds($keyword = '', $rand_sort = false)
  {
    $user_tbl = Engine_Api::_()->getDbTable('users', 'user');

    $select = $user_tbl->select();
    $select
        ->setIntegrityCheck(false)
        ->from(array('u' => $user_tbl->info('name')) , 'u.*')
        ->join(array('f' => $this->info('name')), 'f.user_id = u.user_id', 'f.featured_id');

    if ($keyword) {
      $select->where('u.displayname LIKE ?', "%{$keyword}%");
    }

    if ($rand_sort) {
      $select->order('RAND()');
    }

    return Zend_Paginator::factory($select);
  }

  public function getFriendFeatureds($user, $keyword, $rand_sort = false)
  {
    $user_tbl = Engine_Api::_()->getDbTable('users', 'user');
    $membership_tbl = Engine_Api::_()->getDbTable('membership', 'user');

    $select = $user_tbl->select();
    $select
        ->setIntegrityCheck(false)
        ->from(array('u' => $user_tbl->info('name')) , 'u.*')
        ->join(array('f' => $this->info('name')), 'f.user_id = u.user_id', 'f.featured_id')
        ->joinLeft(array('m' => $membership_tbl->info('name')), 'm.user_id = u.user_id', array())
        ->where('m.resource_id = ?', $user)
        ->where('m.resource_approved = 1')
        ->where('m.user_approved = 1')
        ->order('RAND()');

    if ($keyword) {
      $select->where('u.displayname LIKE ?', "%{$keyword}%");
    }
    if ($rand_sort) {
      $select->order('RAND()');
    }

    return Zend_Paginator::factory($select);
  }
}