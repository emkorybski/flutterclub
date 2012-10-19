<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Settings.php 2010-07-02 19:52 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_Model_DbTable_User_Settings extends Engine_Db_Table
{
  protected $_name = 'hecore_user_settings';

  public function getSetting($key, $user_id = 0)
  {
    $db = $this->getAdapter();
    $select = $this->select();

    $select
      ->setIntegrityCheck(false)
      ->from($this->info('name'), array('value'))
      ->where('setting = ?', $key);

    if ($user_id) {
      $select
        ->where('user_id = ?', $user_id);
    }

    return $db->fetchOne($select);
  }
}