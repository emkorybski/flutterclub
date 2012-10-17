<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Bootstrap.php 2010-07-02 19:52 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    $this->initViewHelperPath();

    parent::__construct($application);
  }
}

if (!function_exists('print_arr')) {
  function print_arr($var, $return = false) {
    $type = gettype($var);

    $out = print_r($var, true);
    $out = htmlspecialchars($out);
    $out = str_replace(' ', '&nbsp; ', $out);
    if ($type == 'boolean')
      $content = $var ? 'true' : 'false';
    else
      $content = nl2br( $out );

    $out = '<div style="
       border:2px inset #666;
       background:black;
       font-family:Verdana;
       font-size:11px;
       color:#6F6;
       text-align:left;
       margin:20px;
       padding:16px">
         <span style="color: #F66">('.$type.')</span> '.$content.'</div><br /><br />';

    if (!$return)
      echo $out;
    else
      return $out;
  }
}

if (!function_exists('print_die')) {
  function print_die($var, $return = false)
  {
    print_arr($var, $return);
    die;
  }
}

if (!function_exists('print_log')) {
  function print_log($str)
  {
    $log = new Zend_Log();
    $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/hecore_log.log'));
    $log->log($str . "\n\r\n\r", Zend_Log::INFO);
  }
}

if (!function_exists('print_firebug')) {
  function print_firebug($str)
  {
    $log = new Zend_Log();
    $log->addWriter(new Zend_Log_Writer_Firebug());
    $log->log($str, Zend_Log::INFO);
  }
}