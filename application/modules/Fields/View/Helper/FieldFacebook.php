<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: FieldFacebook.php 9524 2011-11-30 23:24:25Z shaun $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John
 */
class Fields_View_Helper_FieldFacebook extends Fields_View_Helper_FieldAbstract
{
  public function fieldFacebook($subject, $field, $value)
  {

    // $facebookUrl = stripos($value->value, 'facebook.com/') === false
    //              ? 'http://www.facebook.com/search/?q=' . $value->value
    //              : $value->value;
    //
    // $value->value should contain either the full URL to a user's facebook
    // profile, or it should contain their profile username or userid. Any
    // other value is sent to Facebook's serach page
    $regex = '/^((http(s|):\/\/|)(www\.|)|)facebook\.com\//i';
    $username = preg_replace($regex, '', trim($value->value));
    
    // create user's profile address using their username/userid
    $facebookUrl = 'https://www.facebook.com/' .  $username;
    
    return $this->view->htmlLink($facebookUrl, $value->value, array(
      'target' => '_blank',
      'ref' => 'nofollow',
    ));
  }
}