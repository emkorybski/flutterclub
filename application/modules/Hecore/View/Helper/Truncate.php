<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Truncate.php 2010-07-02 19:52 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_View_Helper_Truncate extends Engine_View_Helper_HtmlElement
{
  public function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false)
  {
    if ($length == 0)
      return '';
    
    if (Engine_String::strlen($string) > $length) {
      $length -= Engine_String::strlen($etc);
      if (!$break_words && !$middle) {
        $string = preg_replace('/\s+?(\S+)?$/', '', Engine_String::substr($string, 0, $length+1));
      }
      if(!$middle) {
        return Engine_String::substr($string, 0, $length).$etc;
      } else {
        return Engine_String::substr($string, 0, $length/2) . $etc . Engine_String::substr($string, -$length/2);
      }
    } else {
      return $string;
    }
  }
}