<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: FieldTwitter.php 9524 2011-11-30 23:24:25Z shaun $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John
 */
class Fields_View_Helper_FieldTwitter extends Fields_View_Helper_FieldAbstract
{
  public function fieldTwitter($subject, $field, $value)
  {
    $regex = '/^((http(s|):\/\/|)(www\.|)|)twitter\.com\/(#!|)/i';
    $username = preg_replace($regex, '', trim($value->value));
    $twitterUrl = 'https://www.twitter.com/#!/' .  $username;
    
    return $this->view->htmlLink($twitterUrl, $value->value, array(
      'target' => '_blank',
      'ref' => 'nofollow',
    ));
  }
}