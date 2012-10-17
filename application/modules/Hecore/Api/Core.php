<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-07-02 19:52 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_Api_Core extends Core_Api_Abstract
{
  public function getViewerFriends()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbTable('membership', 'user');

    $select = $table->select();
    $select
      ->from('user_id')
      ->where('resource_id = ?', $viewer->getIdentity())
      ->where('active = 1')
      ->where('resource_approved = 1')
      ->where('user_approved = 1');

    return $table->getAdapter()->fetchCol($select)->toArray();
  }

  public function getMutualFriends($params, $user = null)
  {
    if ($user instanceof User_Model_User) {
      $viewer = $user;
    } elseif (is_numeric($user)) {
      $viewer = Engine_Api::_()->getItem('user', $user);
    } else {
      $viewer = Engine_Api::_()->user()->getViewer();
    }

    if ($params instanceof Core_Model_Item_Abstract) {
      $subject = $params;
      $list_type = 'mutual';
    } else {
      $subject = Engine_Api::_()->getItem('user', $params['object_id']);
      $list_type = $params['list_type'] ? $params['list_type'] : 'all';
    }

    if ($list_type != 'mutual') {
      return $this->getFriends($params);
    }

    // Diff friends
    $friendsTable = Engine_Api::_()->getDbtable('membership', 'user');
    $friendsName = $friendsTable->info('name');

    $select = new Zend_Db_Select($friendsTable->getAdapter());
    $select
      ->from($friendsName, 'user_id')
      ->join($friendsName, "`{$friendsName}`.`user_id`=`{$friendsName}_2`.user_id", null)
      ->where("`{$friendsName}`.resource_id = ?", $viewer->getIdentity())
      ->where("`{$friendsName}_2`.resource_id = ?", $subject->getIdentity())
      ->where("`{$friendsName}`.active = ?", 1)
      ->where("`{$friendsName}_2`.active = ?", 1);

    // Now get all common friends
    $uids = array();
    foreach ($select->query()->fetchAll() as $data) {
      $uids[] = $data['user_id'];
    }

    if (empty ($uids)) {
      return Zend_Paginator::factory(array());
    }

    // Get paginator
    $usersTable = Engine_Api::_()->getItemTable('user');
    $select = $usersTable->select()
      ->where('user_id IN(?)', $uids);

    return Zend_Paginator::factory($select);
  }

  public function getFriends($params = array(), $user = null)
  {
    if (!empty($params['object_id'])) {
      $viewer = Engine_Api::_()->getItem('user', $params['object_id']);
    } else {
      if ($user instanceof User_Model_User) {
        $viewer = $user;
      } elseif (is_numeric($user)) {
        $viewer = Engine_Api::_()->getItem('user', $user);
      } else {
        $viewer = Engine_Api::_()->user()->getViewer();
      }
    }

    $table = Engine_Api::_()->getItemTable('user');
    $prefix = $table->getTablePrefix();

    $select = $table->select();

    if (!empty($params['sort_list'])) {
      $list = $params['sort_list'];
      $order = "IF ({$prefix}users.user_id IN ({$list}), 9999, RAND()) DESC";
      $select->order($order);
    }

    $select
      ->setIntegrityCheck(false)
      ->from($prefix . 'users')
      ->joinLeft($prefix . 'user_membership', $prefix . 'user_membership.user_id = ' . $prefix . 'users.user_id', array())
      ->where($prefix . 'user_membership.resource_id = ?', $viewer->getIdentity())
      ->where($prefix . 'user_membership.resource_approved = 1')
      ->where($prefix . 'user_membership.user_approved = 1');

    return Zend_Paginator::factory($select);
  }

  public function getFriendsChecked($params = array())
  {
    $table = Engine_Api::_()->getDbTable('user_settings', 'hecore');
    $setting = 'hecore.friend.list';
    $user_id = $params['object_id'];

    $list = $table->getSetting($setting, $user_id);

    return (trim($list) != "") ? array_unique(explode(",", $list)) : array();
  }

  public function getRemoteServerUrl()
  {
    $customer_code = 'd393995ec8813d8b151f1af8f577c14c';

    $url = "http://www.hire-experts.com/{$customer_code}.php";

    return $url;
  }

  public function decryptByKey($sData, $sKey = 'key')
  {
    $sResult = '';
    $sData = $this->decodeBase64($sData);
    for ($i = 0; $i < strlen($sData); $i++) {
      $sChar = substr($sData, $i, 1);
      $sKeyChar = substr($sKey, ($i % strlen($sKey)) - 1, 1);
      $sChar = chr(ord($sChar) - ord($sKeyChar));
      $sResult .= $sChar;
    }

    return $sResult;
  }

  public function decodeBase64($sData)
  {
    $sBase64 = strtr($sData, '-_', '+/');

    return base64_decode($sBase64 . '==');
  }

  public function checkLicense($parameters = array())
  {
    $curl = curl_init();
    $parameters_str = '';

    $url = $this->getRemoteServerUrl();

    foreach ($parameters as $key => $value) {
      $parameters_str .= "$key=$value&";
    }

    $url .= "?$parameters_str";

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $this->_initDb();
    $result = curl_exec($curl);
    $this->_initDb();
    curl_close($curl);

    return $result;
  }

  public function checkProduct($name)
  {
    $result = true;

    $modulesTable = Engine_Api::_()->getDbTable('modules', 'hecore');
    $module = $modulesTable->findByName($name);

    if (!$module || !$module->key) {
      $result = false;
    }

    if ($result) {
      $parameters = array(
        'task' => 'check',
        'product' => $module->name,
        'license' => $module->key,
        'domain' => $_SERVER['HTTP_HOST']
      );

      $data = $this->checkLicense($parameters);
      if (!$data) {
        $result = false;
      }

      eval($data);
    }

    if ($result) {
      return array('result' => true);
    }

    $urlParams = array(
      'module' => 'hecore',
      'controller' => 'module',
      'action' => 'edit',
      'name' => $name,
      'format' => 'smoothbox'
    );

    $register_url = Zend_Controller_Front::getInstance()->getRouter()->assemble($urlParams, 'default');
    $register_url = 'http://' . $_SERVER['HTTP_HOST'] . str_replace('/install', '', $register_url);

    $translate = Zend_Registry::get('Zend_Translate');

    $error_msg = $translate->_('Your License Key is invalid. txt');

    $error_message = $translate->_('Your License Key is invalid. html');
    $error_message = sprintf($error_message, $register_url);

    $error_msg_js = Zend_Json::encode($error_message);

    $error = array(
      'result' => false,
      'message' => $error_msg,
      'script' => "window.addEvent('domready', function(){he_replace_form_error($error_msg_js);});"
    );

    return $error;
  }

  public function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false)
  {
    if ($length == 0)
      return '';

    if (strlen($string) > $length) {
      $length -= strlen($etc);
      if (!$break_words && !$middle) {
        $string = preg_replace('/\s+?(\S+)?$/', '', Engine_String::substr($string, 0, $length + 1));
      }
      if (!$middle) {
        return Engine_String::substr($string, 0, $length) . $etc;
      } else {
        return Engine_String::substr($string, 0, $length / 2) . $etc . Engine_String::substr($string, -$length / 2);
      }
    } else {
      return $string;
    }
  }

  public function getFeatureds($param = array())
  {

    $featured_tbl = Engine_Api::_()->getDbTable('featureds', 'hecore');
    $user = Engine_Api::_()->user()->getViewer()->getIdentity();

    if (!$user) {
      return;
    }

    $keyword = (isset($param['keyword'])) ? $param['keyword'] : '';

    if (isset($param['list_type']) && $param['list_type'] == 'mutual') {
      $paginator = $featured_tbl->getFriendFeatureds($user, $keyword);
    } else {
      $paginator = $featured_tbl->getFeatureds($keyword);
    }

    return $paginator;
  }

  /**
   * @param Engine_Db_Table_Rowset|null $users
   * @return array|bool
   */
  public function addFriends(Engine_Db_Table_Rowset $users = null)
  {
    /**
     * @var $viewer User_Model_User
     */
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      return false;
    }

    if (is_null($users)) {
      $table = Engine_Api::_()->getItemTable('user');
      $users = $table->fetchAll($table->select()->where('user_id != ?', $viewer->getIdentity()));
    }

    if ($users->getTableClass() != 'User_Model_DbTable_Users') {
      return false;
    }

    $ids = array();
    foreach ($users as $user) {
      try {
        $user->membership()
          ->addMember($viewer)
          ->setUserApproved($viewer);
      } catch (Exception $e) {
      }
    }

    foreach ($users as $user) {
      try {
        $user->membership()->setResourceApproved($viewer);
        $ids[] = $user->getIdentity();
      } catch (Exception $e) {
      }
    }

    return $ids;
  }

  public function _initDb()
  {
    $file = APPLICATION_PATH . '/application/settings/database.php';
    $options = include $file;

    $db = Zend_Db::factory($options['adapter'], $options['params']);

    Engine_Db_Table::setDefaultAdapter($db);
    Engine_Db_Table::setTablePrefix($options['tablePrefix']);

    // Non-production
    if( APPLICATION_ENV !== 'production' ) {
      $db->setProfiler(array(
        'class' => 'Zend_Db_Profiler_Firebug',
        'enabled' => true
      ));
    }

    // set DB to UTC timezone for this session
    switch( $options['adapter'] ) {
      case 'mysqli':
      case 'mysql':
      case 'pdo_mysql': {
      $db->query("SET time_zone = '+0:00'");
      break;
      }

      case 'postgresql': {
      $db->query("SET time_zone = '+0:00'");
      break;
      }

      default: {
      // do nothing
      }
    }

    // attempt to disable strict mode
    try {
      $db->query("SET SQL_MODE = ''");
    } catch (Exception $e) {}

    return $db;
  }

  public function checkDbConnect()
  {
    $db = Engine_Db_Table::getDefaultAdapter();
    try {
      $wait_timeout = $db->fetchRow("show variables like 'wait_timeout'");
    } catch (Exception $e) {
      $is_connected = false;
    }

    if (!$is_connected || (isset($wait_timeout['Value']) && $wait_timeout['Value'] < 60)) {

      $connection = $db->getConnection();
      if (method_exists($connection, 'close')) {
        $connection->close();
      }

      $this->_initDb();
      $db = Engine_Db_Table::getDefaultAdapter();
    }

    return $db;
  }
}