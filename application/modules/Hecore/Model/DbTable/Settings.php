<?php
/**
 * SocialEngine
 *
 * @category Application_Extensions
 * @package Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 * @version $Id: Settings.php 2/3/12 6:07 PM mt.uulu $
 * @author Mirlan
 */

/**
 * @category Application_Extensions
 * @package Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 */


class Hecore_Model_DbTable_Settings extends User_Model_DbTable_Settings
{
  protected $_name = 'user_settings';

  public function setSetting(User_Model_User $user, $key, $value)
  {
    if( null === $value ) {
      $this->delete(array(
        'user_id = ?' => $user->getIdentity(),
        'name = ?' => $key,
      ));
    } else if( false === ($prev = $this->getSetting($user, $key)) ) {
      $this->insert(array(
        'user_id' => $user->getIdentity(),
        'name' => $key,
        'value' => $value,
      ));
    } else {
      $this->update(array(
        'value' => $value,
      ), array(
        'user_id = ?' => $user->getIdentity(),
        'name = ?' => $key,
      ));
    }

    return $this;
  }
}
