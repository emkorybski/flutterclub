<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminLogController.php 2010-08-31 16:05 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_AdminLogController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $error_code = $this->_getParam('code', false);
    $log = $this->_getParam('log', 'main.log');
    $page = $this->_getParam('page', 1);

    if (!$error_code) {
      $this->_printArr('Incorrect error code.');
      exit();
    }

    $logPath = APPLICATION_PATH . '/temporary/log';

    if (!file_exists($logPath . DS . $log)) {
      $this->_printArr('Log file doesn\'t exist.');
      exit();
    }

    $res = fopen($logPath . DS . $log, 'r');
    $content = fread($res, filesize($logPath . DS . $log));

    $index = 1;
    $error_pos = 0;
    while ($index <= $page) {
      if ($index > 200) {
        break;
      }
      $error_pos = strpos($content, $error_code, ($error_pos + 1));
      $index++;
    }

    if (!$error_pos) {
      $this->_printArr('Error not found.');
      exit();
    }

    $win32 = (strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'win32') !== false);
    $error_newline = ($win32) ? "\n\r" : "\n\n";
    $error_newline = "{main}";

    $error_code_end = strpos($content, $error_newline, $error_pos);

    $start = (($error_pos-2000) > 0) ? $error_pos-2000 : 0;
    $end = $error_code_end - $start;
    $error_str = Engine_String::substr($content, $start, $end);
    $error = array_pop(explode($error_newline, $error_str));

    $this->_printArr($error . $error_newline);

    fclose($res);

    exit();
  }

  private function _printArr($var, $return = false)
  {
    $type = gettype($var);

    $out = print_r($var, true);
    $out = htmlspecialchars($out);
    $out = str_replace('  ', '&nbsp; ', $out);
    if ($type == 'boolean') {
      $content = $var ? 'true' : 'false';
    } else {
      $content = nl2br( $out );
    }

    $out = '<div style="
      border:2px inset #DDECF3;
      background:#F4F9FB;
      font-family:Verdana;
      font-size:11px;
      color:#444444;
      text-align:left;
      margin:20px;
      padding:16px">
        <span style="color: #444444">(' . $type . ')</span> ' . $content . '</div><br /><br />';

    if (!$return) {
      echo $out;
    } else {
      return $out;
    }

  }
}