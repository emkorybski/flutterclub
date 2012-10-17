<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Friends Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: install.php 2010-07-02 19:52 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Friends Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Installer extends Engine_Package_Installer_Module
{
  public function onPreInstall()
  {
    parent::onPreInstall();

    $db = $this->getDb();
    $translate = Zend_Registry::get('Zend_Translate');

    $select = $db->select()
      ->from('engine4_core_modules')
      ->where('name = ?', 'hecore')
      ->where('enabled = ?', 1);

    $hecore = $db->fetchRow($select);

    if (!$hecore) {
      $error_message = $translate->_('Error! This plugin requires Hire-Experts Core module. It is free module and can be downloaded from Hire-Experts.com');
      return $this->_error($error_message);
    }

    if (version_compare($hecore['version'], '4.1.1') < 0) {
      $error_message = $translate->_('This plugin requires Hire-Experts Core Module. We found that you has old version of Core module, please download latest version of Hire-Experts Core Module and install. Note: Core module is free.');
      return $this->_error($error_message);
    }

    $operation = $this->_databaseOperationType;
    $module_name = $this->getOperation()->getTargetPackage()->getName();

    $select = $db->select()
      ->from('engine4_hecore_modules')
      ->where('name = ?', $module_name);

    $module = $db->fetchRow($select);

    if ($operation == 'install') {

      if ($module && $module['installed']) {
        return;
      }

      $url_params = array(
        'module' => 'hecore',
        'controller' => 'module',
        'action' => 'license',
        'name' => $module_name,
        'version' => $this->_targetVersion,
        'format' => 'smoothbox'
      );

      $route = Zend_Controller_Front::getInstance()->getRouter();
      $register_url = $route->assemble($url_params, 'default', true);
      $register_url = str_replace('/install', '', $register_url);

      $error_message = $translate->_('It is paid plugin from Hire-Experts LLC. You need to type License Key to install this module - <a class="smoothbox" href="%s">Click Here</a>');
      $error_message = sprintf($error_message, $register_url);

      return $this->_error($error_message);
    }
    elseif ($operation == 'upgrade') {

      $url_params = array(
        'module' => 'hecore',
        'controller' => 'module',
        'action' => 'license',
        'name' => $module_name,
        'version' => $this->_currentVersion,
        'target_version' => $this->_targetVersion,
        'format' => 'smoothbox'
      );

      $route = Zend_Controller_Front::getInstance()->getRouter();
      $register_url = $route->assemble($url_params, 'default', true);
      $register_url = str_replace('/install', '', $register_url);

      $error_message = $translate->_('It is paid plugin from Hire-Experts LLC. You need to type License Key to install this module - <a class="smoothbox" href="%s">Click Here</a>');
      $error_message = sprintf($error_message, $register_url);

      $values = array(
        'task' => 'upgrade',
        'product' => $module_name,
        'license' => (isset($module['key'])) ? $module['key'] : 0,
        'version' => $this->_currentVersion,
        'target_version' => $this->_targetVersion,
      );

      $form = new Engine_Form();
      $hecoreApi = $this;
      $parameters = $values;

      $result = $this->checkLicense($values);

      if (!$result) {
        return $this->_error($error_message);
      }

      eval($result);

      if ($form->isErrors()) {
        return $this->_error($error_message);
      }

    } else { //if ($operation == 'refresh'){

      $url_params = array(
        'module' => 'hecore',
        'controller' => 'module',
        'action' => 'license',
        'name' => $module_name,
        'version' => $this->_currentVersion,
        'target_version' => $this->_targetVersion,
        'format' => 'smoothbox'
      );

      $route = Zend_Controller_Front::getInstance()->getRouter();
      $register_url = $route->assemble($url_params, 'default', true);
      $register_url = str_replace('/install', '', $register_url);

      $error_message = $translate->_('It is paid plugin from Hire-Experts LLC. You need to type License Key to install this module - <a class="smoothbox" href="%s">Click Here</a>');
      $error_message = sprintf($error_message, $register_url);

      $values = array(
        'task' => 'refresh',
        'product' => $module_name,
        'license' => (isset($module['key'])) ? $module['key'] : 0,
        'version' => $this->_currentVersion,
        'target_version' => $this->_targetVersion,
      );

      $result = false;

      $server_result = $this->checkLicense($values);

      if (!$server_result) {
        return $this->_error($error_message);
      }

      eval($server_result);

      if (!$result) {
        return $this->_error($error_message);
      }

    }
  }

  public function checkLicense($params = array())
  {
    $params = array_merge($params, array(
      'domain' => $_SERVER['HTTP_HOST']
    ));

    $curl = curl_init();
    $params_str = '';

    $customer_code = 'd393995ec8813d8b151f1af8f577c14c';

    $url = "http://www.hire-experts.com/$customer_code.php";

    foreach ($params as $key => $value) {
      $params_str .= "$key=$value&";
    }

    $url .= "?$params_str";

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);
    curl_close($curl);

    return $result;
  }

  public function decryptByKey($sData, $sKey = 'key')
  {
    $sResult = '';
    $sData   = $this->decodeBase64($sData);
    
    for ($i = 0; $i < strlen($sData); $i++) {
      $sChar    = substr($sData, $i, 1);
      $sKeyChar = substr($sKey, ($i % strlen($sKey)) - 1, 1);
      $sChar    = chr(ord($sChar) - ord($sKeyChar));
      $sResult .= $sChar;
    }

    return $sResult;
  }

  public function decodeBase64($sData)
  {
    $sBase64 = strtr($sData, '-_', '+/');

    return base64_decode($sBase64 . '==');
  }
}