<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminIndexController.php 2010-08-31 16:05 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_AdminIndexController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('hecore_admin_main', array(), 'hecore_admin_main_plugins');
    $this->view->special_mode = _ENGINE_ADMIN_NEUTER;
  }

  public function indexAction()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $table = Engine_Api::_()->getDbtable('modules', 'core');
    $select = $table->select()
      ->where('type <> ?', 'extra');

    $modules = $table->fetchAll($select);

    $curl_url = 'http://www.hire-experts.com/plugin_rss.php';
    if (($settings->getSetting('hecore.module.check.licenses', 0) + 2 * 24 * 2600) < time()) {
      $settings->setSetting('hecore.module.check.licenses', time());
      $params = urlencode(Zend_Json::encode(array("items" => $modules->toArray(), "domain" => $_SERVER["HTTP_HOST"])));
      $curl_url .= "?items=" . $params;
    }

    $curl_handle = curl_init($curl_url);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

    $content = curl_exec($curl_handle);
    $content = Zend_Json::decode($content, Zend_Json::TYPE_OBJECT);

    $plugins = Array();
    $plugin_keys = "'0'";
    foreach ($content as $key => $p) {
      $plugins[$p->se_key] = $content[$key];
      $plugin_keys .= ",'" . $p->se_key . "'";
    }

    $installed = 0;
    $updated = 0;

    $select = $table->select()
      ->where("name IN ({$plugin_keys})")
      ->where('enabled', 1);

    $modules = $table->fetchAll($select);

    if ($modules->count() > 0) {
      foreach ($modules as $module) {
        $plugins[$module->name]->current_version = false;
        if (array_key_exists($module->name, $plugins)) {
          $plugins[$module->name]->installed = true;
          $installed++;
          if ($plugins[$module->name]->version != $module->version) {
            $plugins[$module->name]->current_version = $module->version;
            $plugins[$module->name]->updated = true;
            $updated++;
          }
        }
      }
    }

    $this->view->plugins = $plugins;
    $this->view->installed = $installed;
    $this->view->updated = $updated;
    $this->view->isSuperAdmin = $this->isSuperAdmin();
    $this->view->checkLicense = (bool)$this->_getParam('checkLicense', false);
  }

  public function licensesAction()
  {
    if (defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER) {
      return $this->_helper->redirector->gotoRoute(array('module' => 'hecore', 'controller' => 'index', 'action' => 'index'), 'admin_default', true);
    }
    $tbl_module = Engine_Api::_()->getDbTable('modules', 'hecore');
    $modules = $tbl_module->fetchAll();
    if ($licenses = $this->_getParam('license')) {
      foreach ($modules as $module) {
        $name = $module->name;
        if (array_key_exists($module->name, $licenses)) {
          $module->key = $licenses[$name];
          $module->save();
        }
      }
    }
    $prepare = array();
    foreach ($modules as $module) {
      $prepare[] = array(
        'name' => $module->name,
        'version' => $module->version,
        'key' => $module->key
      );
    }
    $request = Zend_Json::encode(array(
      'domain' => $_SERVER['HTTP_HOST'],
      'modules' => $prepare
    ));

    $url = Engine_Api::_()->getApi('core', 'hecore')->getRemoteServerUrl();
    $url .= '?task=check_licenses';
    $contents = Zend_Json::decode(
      $this->request($url, array('request' => $request)),
      Zend_Json::TYPE_ARRAY
    );
    $this->view->modules = $contents;
    $this->view->html = $this->view->render('_hecore_licenses.tpl');
  }

  public function updateAction()
  {
    if (defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER) {
      return $this->_helper->redirector->gotoRoute(array('module' => 'hecore', 'controller' => 'index', 'action' => 'index'), 'admin_default', true);
    }
    $plugin = $this->_getParam('plugin', 0);

    if ($this->isSuperAdmin() && ($plugin == 'hecore' ||
      $module = Engine_Api::_()->getDbTable('modules', 'hecore')->findByName($plugin))
    ) {
      $url = Engine_Api::_()->getApi('core', 'hecore')->getRemoteServerUrl();
      $url .= '?task=upgrade_plugin&domain=' . $_SERVER['HTTP_HOST'];
      if ($plugin == 'hecore') {
        $url .= "&product=hecore";
      } else {
        $url .= "&product={$module->name}&license={$module->key}";
      }
      if ($this->putArchive($url)) {

        $viewer = Engine_Api::_()->user()->getViewer();

        $authKeyRow = Engine_Api::_()->getDbtable('auth', 'core')->getKey($viewer, 'package');
        $this->view->authKey = $authKey = $authKeyRow->id;

        $installUrl = rtrim($this->view->baseUrl(), '/') . '/install';
        if (strpos($this->view->url(), 'index.php') !== false) {
          $installUrl .= '/index.php';
        }
        $return = 'http://' . $_SERVER['HTTP_HOST'] . $installUrl . '/manage/select';
        $installUrl .= '/auth/key' . '?key=' . $authKey . '&uid=' . $viewer->getIdentity() . '&return=' . $return;
        $this->view->installUrl = $installUrl;

        return $this->_helper->redirector->gotoUrl($installUrl, array('prependBase' => false));
      }
    }
    return $this->_redirectCustom(array(
      'route' => 'admin_default',
      'module' => 'hecore',
      'controller' => 'index',
      'action' => 'index',
      'checkLicense' => 1
    ));
  }

  public function checkAction()
  {
    if (defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER) {
      return $this->_helper->redirector->gotoRoute(array('module' => 'hecore', 'controller' => 'index', 'action' => 'index'), 'admin_default', true);
    }
    $url = 'http://www.hire-experts.com/check_file_hash.php?file_hash=' . $this->_getParam('hash');
    $url .= '&customer_code=d393995ec8813d8b151f1af8f577c14c';
    $installUrl = rtrim($this->view->baseUrl(), '/') . '/install';
    $return = 'http://' . $_SERVER['HTTP_HOST'] . $installUrl . '/manage/select';
    echo ($this->putArchive($url)) ? '<a href="' . $return . '">ok</a>' : 'error';
    exit();

  }

  public function isSuperAdmin()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }
    $viewerLevel = Engine_Api::_()->getDbtable('levels', 'authorization')->find($viewer->level_id)->current();

    if (null === $viewerLevel || $viewerLevel->flag != 'superadmin') {
      return false;
    }
    return true;
  }

  public function request($url, $post = false)
  {
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $url);
    if ($post) {
      curl_setopt($c, CURLOPT_POST, 1);
      curl_setopt($c, CURLOPT_POSTFIELDS, $post);
    }
    curl_setopt($c, CURLOPT_HEADER, 0);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($c, CURLOPT_FAILONERROR, 1);
    $result = curl_exec($c);
    curl_close($c);
    return $result;
  }

  public function putArchive($url)
  {
    $result = false;
    if ($contents = $this->request($url)) {
      $uid = substr(md5(rand(1111, 9999)), 0, 15);
      $fp = fopen(getcwd() . "/temporary/package/archives/" . $uid . ".tar", "w");
      $result = fwrite($fp, $contents);
      fclose($fp);
    }
    return $result;
  }

}