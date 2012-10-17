<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: FacebookFriend.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Model_FacebookFriend extends Core_Model_Item_Abstract
{
  public $uid;
  public $name;
  public $profile_url;
  public $pic_square;

  public function __construct($params = array(), $provider = 'facebook')
  {
    if ($provider == 'twitter') {

      $this->uid = isset($params['id']) ? $params['id'] : 0;
      $this->name = isset($params['name']) ? $params['name'] : '';
      $this->profile_url = isset($params['email']) ? 'http://twitter.com/#!/' . $params['email'] : '';
      $this->pic_square = isset($params['profile_image_url']) ? $params['profile_image_url'] : '';

    } elseif ($provider == 'linkedin') {

      $this->uid = isset($params['id']) ? $params['id'] : 0;
      $this->name = isset($params['name']) ? $params['name'] : '';
      $this->profile_url = isset($params['public_profile_url']) ? $params['public_profile_url'] : '';
      $this->pic_square = isset($params['profile_image_url']) ? $params['profile_image_url'] : '';
      $this->pic_square = ($this->pic_square) ? $this->pic_square : 'application/modules/User/externals/images/nophoto_user_thumb_icon.png';

    } else {

      $this->uid = isset($params['uid']) ? $params['uid'] : 0;
      $this->name = isset($params['name']) ? $params['name'] : '';
      $this->profile_url = isset($params['profile_url']) ? $params['profile_url'] : '';
      $this->pic_square = isset($params['pic_square']) ? $params['pic_square'] : '';
      
    }
  }

  public function getType()
  {
    return 'inviter_facebook_user';
  }
  
  public function getIdentity()
  {
    return $this->uid;
  }

  public function getTitle()
  {
    return $this->name;
  }

  public function getHref()
  {
    return $this->profile_url;
  }

  public function getPhotoUrl($type = null)
  {
    return $this->pic_square;

  }
}
