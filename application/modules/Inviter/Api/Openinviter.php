<?php
/**
 * The core of the OpenInviter system
 *
 * Contains methods and properties used by all
 * the OpenInivter plugins
 *
 * @author OpenInviter
 * @version 1.7.6
 */
class Inviter_Api_Openinviter extends Core_Api_Abstract
{
  public $tr;
  public $oi;

  public $pluginTypes=array('email'=>'Email Providers','social'=>'Social Networks');
  private $version='1.9.4';
  private $configStructure=array(
    'username'=>array('required'=>true,'default'=>''),
    'private_key'=>array('required'=>true,'default'=>''),
    'message_body'=>array('required'=>false,'default'=>''),
    'message_subject'=>array('required'=>false,'default'=>''),
    'plugins_cache_time'=>array('required'=>false,'default'=>1800),
    'plugins_cache_file'=>array('required'=>true,'default'=>'oi_plugins.php'),
    'cookie_path'=>array('required'=>true,'default'=>APPLICATION_PATH_TMP),
    'local_debug'=>array('required'=>false,'default'=>false),
    'remote_debug'=>array('required'=>false,'default'=>false),
    'hosted'=>array('required'=>false,'default'=>false),
    'proxies'=>array('required'=>false,'default'=>array()),
    'stats'=>array('required'=>false,'default'=>false),
    'stats_user'=>array('required'=>false,'default'=>''),
    'stats_password'=>array('required'=>false,'default'=>''),
    'update_files'=>array('required'=>false,'default'=>TRUE),
  );
  private $statsDB=false;
  private $configOK;
  private $basePath='';
  private $availablePlugins=array();
  private $currentPlugin=array();

  public function __construct()
  {
    $this->setBasePath(dirname(__FILE__));
    include($this->basePath."/openinviter/config.php");
    require_once($this->basePath."/openinviter/plugins/_base.php");
    $this->settings=$openinviter_settings;
    $this->setConfigOK($this->checkConfig());

    try {
      $this->tr = Zend_Registry::get('Zend_Translate');
    } catch (Exception $e) {
      $coreBootstrap = Engine_Api::_()->getModuleBootstrap('core');
      $coreBootstrap->_initTranslate();
      $this->tr = Zend_Registry::get('Zend_Translate');
    }
  }

  private function checkConfig()
  {
    $to_add=array();$ok=true;
    foreach ($this->configStructure as $option=>$details)
      {
      if (!isset($this->settings[$option])) $to_add[$option]=$details['default'];
      if ($ok) if ($details['required'] AND empty($this->settings[$option])) { $this->internalError="`{$option}` is not defined in config.php";$ok=false; }
      }
    if (!empty($to_add))
      {
      $file_path=$this->basePath."/config.php";
      foreach ($to_add as $option=>$value) $this->settings[$option]=$value;
      if (is_writable($file_path))
        {
        $file_contents="<?php\n";
        $file_contents.="\$openinviter_settings=array(\n".$this->arrayToText($this->settings)."\n);\n";
        $file_contents.="?>";
        file_put_contents($file_path,$file_contents);
        }
      }
    return $ok;
    }

  private function arrayToText($array)
    {
    $text='';
    $flag=false;
    $i=0;
    foreach ($array as $key=>$val)
      {
      if($flag) $text.=",\n";
      $flag=true;
      $text.="'{$key}'=>";
      if (is_array($val)) $text.='array('.$this->arrayToText($val).')';
      elseif (is_bool($val)) $text.=($val?'true':'false');
      else $text.="\"{$val}\"";
      }
    return($text);
    }

  private function statsCheck()
    {
    if (!$this->settings['stats']) return true;
    $db_file=$this->basePath.'/openinviter_stats.sqlite';
    if (!file_exists($db_file))
      {
      if (!is_writable($this->basePath)) { $this->internalError="Unable to write stats. ".$this->basePath." is not writable";return false; }
      if (!$this->statsOpenDB()) { $this->internalError="Unable to create the stats database.";return false; }
      $this->statsQuery("CREATE TABLE oi_imports (id INTEGER PRIMARY KEY, service VARCHAR(16), contacts INTEGER, insert_dt DATETIME, insert_ip VARCHAR(15))");
      $this->statsQuery("CREATE TABLE oi_messages (id INTEGER PRIMARY KEY, service VARCHAR(16), type CHAR(1), messages INTEGER, insert_dt DATETIME, insert_ip VARCHAR(15))");
      }
    elseif (!is_readable($db_file)) { $this->internalError="Unable to open stats database. {$db_file} is not readable.";return false; }
    elseif (!is_writable($db_file)) { $this->internalError="Unable to write stats. {$db_file} is not writable";return false; }
    elseif (!$this->statsOpenDB()) { $this->internalError="Unable to open the stats database.";return false; }
    return true;
    }

  private function statsOpenDB()
    {
    if (!$this->settings['stats']) return true;
    if (function_exists('sqlite_open'))
      if ($this->statsDB=sqlite_open($this->basePath.'/openinviter_stats.sqlite',0666)) return true;
    return false;
    }

  private function statsRecordImport($contacts)
    {
    if (!$this->settings['stats']) return true;
    if (!$this->statsDB) if (!$this->statsOpenDB()) return false;
    $this->statsQuery("INSERT INTO oi_imports (service,contacts,insert_dt,insert_ip) VALUES ('{$this->plugin->service}','{$contacts}','".date("Y-m-d H:i:s")."','{$_SERVER['REMOTE_ADDR']}')");
    }

  private function statsRecordMessages($msg_type,$messages)
    {
    if (!$this->settings['stats']) return true;
    if (!$this->statsDB) if (!$this->statsOpenDB()) return false;
    $this->statsQuery("INSERT INTO oi_messages (service,type,messages,insert_dt,insert_ip) VALUES ('{$this->plugin->service}','{$msg_type}','{$messages}','".date("Y-m-d H:i:s")."','{$_SERVER['REMOTE_ADDR']}')");
    }

  public function statsQuery($query)
    {
    if (!$this->settings['stats']) return false;
    if (!$this->statsDB)
      {
      if (!$this->statsCheck()) return false;
      if (!$this->statsOpenDB()) return false;
      }
    return sqlite_query($this->statsDB,$query,SQLITE_ASSOC);
    }

  /**
   * Start internal plugin
   *
   * Starts the internal plugin and
   * transfers the settings to it.
   *
   * @param string $plugin_name The name of the plugin being started
   */
  public function startPlugin($plugin_name,$getPlugins=false)
    {
    if (!$getPlugins) $this->currentPlugin = isset($this->availablePlugins[$plugin_name]) ? $this->availablePlugins[$plugin_name] : null;
    if (file_exists($this->basePath."/openinviter/postinstall.php")) { $this->internalError="You have to delete postinstall.php before using OpenInviter";return false; }
    elseif (!$this->configOK) return false;
    elseif (!$this->statsCheck()) return false;
    elseif ($this->settings['hosted'])
      {
      if (!file_exists($this->basePath."/openinviter/plugins/_hosted.plg.php")) $this->internalError="Invalid service provider";
      else
        {
        if (!class_exists('_hosted')) require_once($this->basePath."/openinviter/plugins/_hosted.plg.php");
        if ($getPlugins)
          {
          $this->servicesLink=new _hosted($plugin_name);
          $this->servicesLink->settings=$this->settings;
          $this->servicesLink->base_version=$this->version;
          $this->servicesLink->base_path=$this->basePath;
          }
        else
          {
          $this->plugin=new _hosted($plugin_name);
          $this->plugin->settings=$this->settings;
          $this->plugin->base_version=$this->version;
            $this->plugin->base_path=$this->basePath;
            $this->plugin->hostedServices=$this->getPlugins();
          }
        }
      }
    elseif (file_exists($this->basePath."/openinviter/plugins/{$plugin_name}.plg.php"))
      {
      $ok=true;
      if (!class_exists($plugin_name)) require_once($this->basePath."/openinviter/plugins/{$plugin_name}.plg.php");
      $this->plugin=new $plugin_name();
        $this->plugin->settings=$this->settings;
        $this->plugin->base_version=$this->version;
        $this->plugin->base_path=$this->basePath;
        $this->currentPlugin = isset($this->availablePlugins[$plugin_name]) ? $this->availablePlugins[$plugin_name] : null;
      if (file_exists($this->basePath."/openinviter/conf/{$plugin_name}.conf"))
        {
        include($this->basePath."/openinviter/conf/{$plugin_name}.conf");
        if (empty($enable)) $this->internalError="Invalid service provider";
        if (!empty($messageDelay)) $this->plugin->messageDelay=$messageDelay; else  $this->plugin->messageDelay=1;
        if (!empty($maxMessages)) $this->plugin->maxMessages=$maxMessages; else $this->plugin->maxMessages=10;
        }
      }
    else { $this->internalError="Invalid service provider";return false; }
    return true;
    }

  /**
   * Stop the internal plugin
   *
   * Acts as a wrapper function for the stopPlugin
   * function in the OpenInviter_Base class
   */
  public function stopPlugin($graceful=false)
    {
    $this->plugin->stopPlugin($graceful);
    }

  /**
   * Login function
   *
   * Acts as a wrapper function for the plugin's
   * login function.
   *
   * @param string $user The username being logged in
   * @param string $pass The password for the username being logged in
   * @return mixed FALSE if the login credentials don't match the plugin's requirements or the result of the plugin's login function.
   */
  public function login($user,$pass)
    {
    if (!$this->checkLoginCredentials($user)) return false;
    return $this->plugin->login($user,$pass);
    }

  /**
   * Get the current user's contacts
   *
   * Acts as a wrapper function for the plugin's
   * getMyContacts function.
   *
   * @return mixed The result of the plugin's getMyContacts function.
   */
  public function getMyContacts()
    {
    $contacts=$this->plugin->getMyContacts();
    if ($contacts!==false) $this->statsRecordImport(count($contacts));
    return $contacts;
    }

  /**
   * End the current user's session
   *
   * Acts as a wrapper function for the plugin's
   * logout function
   *
   * @return bool The result of the plugin's logout function.
   */
  public function logout()
    {
    return $this->plugin->logout();
    }

  public function writePlConf($name_file,$type)
    {
    if (!file_exists($this->basePath."/openinviter/conf")) mkdir($this->basePath."/openinviter/conf",0755,true);
    if ($type=='social')  file_put_contents($this->basePath."/openinviter/conf/{$name_file}.conf",'<?php $enable=true;$autoUpdate=true;$messageDelay=1;$maxMessages=10;?>');
    elseif($type=='email') file_put_contents($this->basePath."/openinviter/conf/{$name_file}.conf",'<?php $enable=true;$autoUpdate=true; ?>');
    elseif($type=='hosted') file_put_contents($this->basePath."/openinviter/conf/{$name_file}.conf",'<?php $enable=false;$autoUpdate=true; ?>');
    }

  /**
   * Get the installed plugins
   *
   * Returns information about the available plugins
   *
   * @return mixed An array of the plugins available or FALSE if there are no plugins available.
   */
  public function getPlugins($update=false,$required_details=false)
    {
    $plugins=array();
    if ($required_details)
      {
      $valid_rcache=false;$cache_rpath=$this->settings['cookie_path'].'/'."int_{$required_details}.php";
      if (file_exists($cache_rpath))
        {
        include($cache_rpath);
        $cache_rts=filemtime($cache_rpath);
        if (time()-$cache_rts<=$this->settings['plugins_cache_time']) $valid_rcache=true;
        }
      if ($valid_rcache) return $returnPlugins;
      }
    $cache_path=$this->settings['cookie_path'].'/'.$this->settings['plugins_cache_file'];$valid_cache=false;
    $cache_ts=0;
    if (!$update)
      if (file_exists($cache_path))
        {
        include($cache_path);
        $cache_ts=filemtime($cache_path);
        if (time()-$cache_ts<=$this->settings['plugins_cache_time']) $valid_cache=true;
        }
    if (!$valid_cache)
      {
      $array_file=array();
      $temp=glob($this->basePath."/openinviter/plugins/*.plg.php");
          foreach ($temp as $file) $array_file[basename($file,'.plg.php')]=$file;
          if (!$update)
            {
            if ($this->settings['hosted'])
              {
          if ($this->startPlugin('_hosted',true)!==FALSE) { $plugins=array();$plugins['hosted']=$this->servicesLink->getHostedServices(); }
              else return array();
              }
            if (isset($array_file['_hosted'])) unset($array_file['_hosted']);
            }
           if ($update==TRUE OR $this->settings['hosted']==FALSE)
            {
            $reWriteAll=false;
        if (count($array_file)>0)
          {
          ksort($array_file);$modified_files=array();
          if (!empty($plugins['hosted'])) { $reWriteAll=true;$plugins=array(); }
          else
            foreach ($plugins as $key=>$vals)
              {
              foreach ($vals as $key2=>$val2)
                if (!isset($array_file[$key2])) unset($vals[$key2]);
              if (empty($vals)) unset($plugins[$key]);
              else $plugins[$key]=$vals;
              }
          foreach ($array_file as $plugin_key=>$file)
            if (filemtime($file)>$cache_ts OR $reWriteAll)
              $modified_files[$plugin_key]=$file;
          foreach($modified_files as $plugin_key=>$file)
            if (file_exists($this->basePath."/openinviter/conf/{$plugin_key}.conf"))
              {
              include_once($this->basePath."/openinviter/conf/{$plugin_key}.conf");
              if ($enable AND $update==false)
                { include($file); if ($this->checkVersion($_pluginInfo['base_version'])) $plugins[$_pluginInfo['type']][$plugin_key]=$_pluginInfo; }
              elseif ($update==true)
                { include($file); if ($this->checkVersion($_pluginInfo['base_version'])) $plugins[$_pluginInfo['type']][$plugin_key]=array_merge(array('autoupdate'=>$autoUpdate),$_pluginInfo); }
              }
            else
              {  include($file);if ($this->checkVersion($_pluginInfo['base_version'])) $plugins[$_pluginInfo['type']][$plugin_key]=$_pluginInfo; $this->writePlConf($plugin_key,$_pluginInfo['type']);}
          }
        foreach ($plugins as $key=>$val) if (empty($val)) unset($plugins[$key]);
            }
      if (!$update)
        {
        if ((!$valid_cache) AND (empty($modified_files)) AND (!$this->settings['hosted'])) touch($this->settings['cookie_path'].'/'.$this->settings['plugins_cache_file']);
        else
          {
          $cache_contents="<?php\n";
          $cache_contents.="\$plugins=array(\n".$this->arrayToText($plugins)."\n);\n";
          $cache_contents.="?>";
          file_put_contents($cache_path,$cache_contents);
          }
        }
      }
    if (!$this->settings['hosted']) $returnPlugins=$plugins;
    else $returnPlugins=(!empty($plugins['hosted'])?$plugins['hosted']:array());
    if ($required_details)
      {
      if (!$valid_rcache)
        {
        foreach($returnPlugins as $types=>$plugins)
          foreach($plugins as $plugKey=>$plugin)
            if (!empty($plugin['imported_details']))
              { if (!in_array($required_details,$plugin['imported_details'])) unset($returnPlugins[$types][$plugKey]); }
            else unset($returnPlugins[$types][$plugKey]);
        if (!empty($returnPlugins))
          {
          $cache_contents="<?php\n";
          $cache_contents.="\$returnPlugins=array(\n".$this->arrayToText($returnPlugins)."\n);\n";
          $cache_contents.="?>";
          file_put_contents($cache_rpath,$cache_contents);
          }
        }
      return $returnPlugins;
      }
    $temp=array();
    if (!empty($returnPlugins))
      foreach ($returnPlugins as $type=>$type_plugins)
        $temp=array_merge($temp,$type_plugins);
    $this->availablePlugins=$temp;
    return $returnPlugins;
    }

  /**
   * Find out if the contacts should be displayed
   *
   * Tells whether the current plugin will display
   * a list of contacts or not
   *
   * @return bool TRUE if the plugin displays the list of contacts, FALSE otherwise.
   */
  public function showContacts()
    {
    return $this->plugin->showContacts;
    }

  /**
   * Check version requirements
   *
   * Checks if the current version of OpenInviter
   * is greater than the plugin's required version
   *
   * @param string $required_version The OpenInviter version that the plugin requires.
   * @return bool TRUE if the version if equal or greater, FALSE otherwise.
   */
  public function checkVersion($required_version)
    {
    if (version_compare($required_version,$this->version,'<=')) return true;
    return false;
    }

  /**
   * Find out the version of OpenInviter
   *
   * Find out the version of the OpenInviter
   * base class
   *
   * @return string The version of the OpenInviter base class.
   */
  public function getVersion()
    {
    return $this->version;
    }

  /**
   * Check the provided login credentials
   *
   * Checks whether the provided login credentials
   * match the plugin's required structure and (if required)
   * if the provided domain name is allowed for the
   * current plugin.
   *
   * @param string $user The provided user name.
   * @return bool TRUE if the login credentials match the required structure, FALSE otherwise.
   */
  private function checkLoginCredentials($user)
    {
    $is_email=$this->plugin->isEmail($user);
    if ($this->currentPlugin['requirement'])
      {
      if ($this->currentPlugin['requirement']=='email' AND !$is_email)
        {
        $this->internalError="Please enter the full email, not just the username";
        return false;
        }
      elseif ($this->currentPlugin['requirement']=='user' AND $is_email)
        {
        $this->internalError="Please enter just the username, not the full email";
        return false;
        }
      }
    if ($this->currentPlugin['allowed_domains'] AND $is_email)
      {
      $temp=explode('@',$user);$user_domain=$temp[1];$temp=false;
      foreach ($this->currentPlugin['allowed_domains'] as $domain)
        if (preg_match($domain,$user_domain)) { $temp=true;break; }
      if (!$temp)
        {
        $this->internalError="<b>{$user_domain}</b> is not a valid domain for this provider";
        return false;
        }
      }
    return true;
    }

  public function getPluginByDomain($user)
    {
    $user_domain=explode('@',$user);if (!isset($user_domain[1])) return false;
    $user_domain=$user_domain[1];
    foreach ($this->availablePlugins as $plugin=>$details)
      {
      $patterns=array();
      if ($details['allowed_domains']) $patterns=$details['allowed_domains']; elseif (isset($details['detected_domains'])) $patterns=$details['detected_domains'];
      foreach ($patterns as $domain_pattern)
        if (preg_match($domain_pattern,$user_domain)) return $plugin;
      }
    return false;
    }

  /**
   * Gets the OpenInviter's internal error
   *
   * Gets the OpenInviter's base class or the plugin's
   * internal error message
   *
   * @return mixed The error message or FALSE if there is no error.s
   */
  public function getInternalError()
    {
    if (isset($this->internalError)) return $this->internalError;
    if (isset($this->plugin->internalError)) return $this->plugin->internalError;
    return false;
    }

  /**
   * Get the current OpenInviter session ID
   *
   * Acts as a wrapper function for the plugin's
   * getSessionID function.
   *
   * @return mixed The result of the plugin's getSessionID function.
   */
  public function getSessionID()
    {
    return $this->plugin->getSessionID();
    }

  public function getContacts($form, $sign_up = false)
  {
    /**
     * @var $providerApi Inviter_Api_Provider
     */
    $providerApi = Engine_Api::_()->getApi('provider', 'inviter');
    $plugin_name = $providerApi->checkProvider($form->getValue('provider_box'));

    if ($providerApi->checkIntegratedProvider(strtolower($plugin_name))) {
      $session = new Zend_Session_Namespace('inviter');
      $session->__set('provider', strtolower($plugin_name));

      if ($sign_up) {
        $providerApi->findContacts(strtolower($plugin_name));
      }

      return true;
    }

    switch(strtolower($plugin_name)){
      case 'live/hotmail':
      case 'msn':
        $plugin_name = 'hotmail';
        break;
      case 'yahoo!':
        $plugin_name = 'yahoo';
        break;
      default:
        break;
    }

    if (strtolower($plugin_name) == 'twitter' && strstr(trim($form->getValue('email_box')), '@') !== FALSE) {
      return $this->tr->translate("INVITER_Please enter just the username, not the full email");
    }

    $this->startPlugin(strtolower($plugin_name));
    $internal=$this->getInternalError();

    if ($internal)
    {
      return $internal;
    }

    elseif (!$this->login(strtolower(trim($form->getValue('email_box'))),strtolower(trim(strtolower($form->getValue('password_box'))))))
    {
      $internal=$this->getInternalError();
      if($internal) {
        return $internal;
      } else {
        return $this->tr->translate("INVITER_Login failed. Please check the email and password you have provided and try again later");
      }
    }

    elseif (false===$contactRows=$this->getMyContacts())
    {
      return $this->tr->translate("INVITER_Unable to get contacts.");
    }

    else
    {
      $session = new Zend_Session_Namespace('inviter');
      $session->__set('provider',strtolower($plugin_name));
      $session->__set('sender', $form->getValue('email_box'));
      $session->__set('oi_session_id', $this->plugin->getSessionID());

      $contactCount = count($contactRows);
      $contactRows = $this->structContacts($contactRows);

      if (!empty($contactRows['members']))
      {
        $session->__set('members', $contactRows['members']);
      }

      if (!empty($contactRows['contacts']))
      {
        $session->__set('contacts', $contactRows['contacts']);
      }

      if (empty($contactRows['contacts']) && $contactCount > 0)
      {
        return $this->tr->translate("INVITER_You have already invited all your contacts and connected with them here.");
      }

      return true;
    }
  }

  public function structContacts($contactRows = array())
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $userTb = Engine_Api::_()->getItemTable('user');
    $invitesTb = Engine_Api::_()->getDbtable('invites', 'inviter');

    $contacts_str = "'".implode("','", array_keys($contactRows))."'";

    $invitesSl = $invitesTb->select()
      ->setIntegrityCheck(false)
      ->from($invitesTb->info('name'), array('GROUP_CONCAT(new_user_id) AS user_ids', 'GROUP_CONCAT(recipient) AS recipients'))
      ->where("recipient IN ({$contacts_str})")
      ->where("new_user_id>?", 0)
      ->limit(1);
    $invited = $invitesTb->fetchRow($invitesSl);
    $user_ids = is_null($invited->user_ids)?0:$invited->user_ids;

    $userSl = $userTb->select()
            ->setIntegrityCheck(false)
            ->from($userTb->info('name'))
            ->where($userTb->info('name').".email IN ({$contacts_str}) OR ".$userTb->info('name').".user_id IN ({$user_ids})")
            ->joinLeft(
              $invitesTb->info('name'),
              $invitesTb->info('name').'.new_user_id = '.$userTb->info('name').'.user_id',
              array($invitesTb->info('name').'.recipient AS recipient'));

    $memberRows = $userTb->fetchAll($userSl);

    $members = array();

    foreach ($memberRows as $member)
    {
      unset($contactRows[$member->email]);
      unset($contactRows[$member->recipient]);

      if (!$viewer->getIdentity() || (!$viewer->isSelf($member) && !$viewer->membership()->isMember($member)))
      {
        $members[$member->email] = $member->displayname;
      }
    }

    $id = 0;
    $contacts = array();

    foreach ($contactRows as $email=>$name)
    {
      $id++;
      $contacts[$id]['name'] = Zend_Json::decode('"'.$name.'"');
      $contacts[$id]['email'] = $email;
    }

    return array('members'=>$members, 'contacts'=>$contacts);
  }

  public function sendRequests($error)
  {
    $session = new Zend_Session_Namespace('inviter');
    $user_ids = $session->__get('user_ids');
    $user_ids = (is_array($user_ids))?$user_ids:explode(',', str_replace(', ', '', $user_ids));

    foreach ($user_ids as $user_id)
    {
      $viewer = Engine_Api::_()->user()->getViewer();
      $user = Engine_Api::_()->user()->getUser($user_id);

      if( null != $user && !$viewer->isSelf($user) && !$viewer->membership()->isMember($user) && !$viewer->isBlocked($user))
      {
        $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
        $db->beginTransaction();
        try
        {
          $user->membership()->addMember($viewer)->setUserApproved($viewer);

          if(!$user->membership()->isUserApprovalRequired()&&!$user->membership()->isReciprocal()){

            Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $viewer, 'friends_follow', '{item:$object} is now following {item:$subject}.');
            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_follow');
          }
          else if(!$user->membership()->isUserApprovalRequired()&&$user->membership()->isReciprocal()){
            Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.');
            Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.');
            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_accepted');
          }
          else if(!$user->membership()->isReciprocal()){

            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_follow_request');

          }

          else if($user->membership()->isReciprocal())
          {
            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_request');
          }

          $db->commit();
        }
        catch( Exception $e )
        {
          $db->rollBack();
        }
      }
      else
      {
        $error = true;
      }
    }

    return $error;
  }

  public function sendInvitations($page_id = null)
  {
      
    $session = new Zend_Session_Namespace('inviter');

    if ($session->__isset('provider'))
    {
      $this->startPlugin($session->provider);
      $internal=$this->getInternalError();

      if ($internal)
       return $internal;

      if (!$session->__isset('oi_session_id'))
        return $this->tr->_("INVITER_No active session");
    }
    elseif($session->__isset('uploaded_contacts'))
    {
      $viewer = Engine_Api::_()->user()->getViewer();
      $session->__set('sender', $viewer->email);
    }
    else
    {
      return $this->tr->_("INVITER_Provider missing");
    }

    if (!$session->__isset('sender'))
      return $this->tr->_("INVITER_Inviter information missing");

    if (!$session->__isset('contacts'))
      return $this->tr->_("INVITER_Missing contacts!!!");

    if (!$session->__isset('contact_ids'))
      return $this->tr->_("INVITER_You haven't selected any contacts to invite");


    $contacts = $this->getSelectedContacts();
    $message = $session->__get('message');

    if ($session->__isset('uploaded_contacts'))
    {
      $sendMessage = ($page_id) ? $this->sendPageEmails($session, $message, $contacts, $page_id) : $this->sendEmails($session, $message, $contacts);
    } else {
      $sendMessage = ($page_id) ? $this->sendPageMessage($session, $message, $contacts, $page_id) : $this->sendMessage($session, $message, $contacts);
    }

    if ($sendMessage===-1)
    {
      return $this->tr->_("INVITER_Selected contacts are already members");
    }

    elseif ($sendMessage===false)
    {
      $internal=$this->getInternalError();
      if ($internal)
        return $internal;
      else
        return $this->tr->_("INVITER_An error has occurred while sending invitation, please try again later");
    }

    return true;
  }

  public function getSelectedContacts()
  {
    $session = new Zend_Session_Namespace('inviter');

    $contactRows = $session->__get('contacts');
    $contact_ids = $session->__get('contact_ids');

    $contact_ids = is_array($contact_ids)?$contact_ids:explode(',',$contact_ids);
    $contacts = array();

    foreach ($contact_ids as $id)
    {
      $contacts[$contactRows[$id]['email']] = $contactRows[$id]['name'];
    }

    return $contacts;
  }

  public function sendEmails(Zend_Session_Namespace $session, $message, $contacts)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $recipients = array();
    foreach ($contacts as $email=>$name)
    {
      $recipients[] = $email;
    }
    $sent_emails = 0;

    if (is_array($recipients) && !empty($recipients))
    {

      // Initiate objects to be used below
      $inviterTable = Engine_Api::_()->getDbtable('invites', 'inviter');
      // Iterate through each recipient

      $userTb = Engine_Api::_()->getDbtable('users', 'user');
      $userSl = $userTb->select()
        ->setIntegrityCheck(false)
        ->from($userTb->info('name'), array('email', 'username'))
        ->where("email IN(?)", $recipients);

      $already_members_array = $userTb->fetchAll($userSl)->toArray();

      $already_members = array();
      foreach ($already_members_array as $already_member) {
        $already_members[$already_member['email']] = $already_member['username'];
      }

      foreach ($contacts as $recipient => $recipient_name) {
        // perform tests on each recipient before sending invite
        $recipient = trim($recipient);
        // watch out for poorly formatted emails
        if (!empty($recipient) && !array_key_exists($recipient, $already_members)) {
          // Passed the tests, lets start inserting database entry
          // generate unique invite code and confirm it truly is unique
          do {
            $invite_code  = substr(md5(rand(0,999).$recipient), 10, 7);
            $code_check   = $inviterTable->select()->where('code = ?', $invite_code);
          } while (null !== $inviterTable->fetchRow($code_check));

          $inviterUrl = ( _ENGINE_SSL ? 'https://' : 'http://' )
            . $_SERVER['HTTP_HOST']
            . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                'module' => 'inviter',
                'controller' => 'signup',
              ), 'default', true)
            . '?'
            . http_build_query(array('code' => $invite_code, 'email' => $recipient))
            ;

          $inviterUrl = '<a href="' . $inviterUrl . '">' . $inviterUrl . '</a>';

          $message = str_replace('[invite_url]', $inviterUrl, $message);

          // insert the invite into the database
          $db = Engine_Db_Table::getDefaultAdapter();
          $db->beginTransaction();
          try
          {
            $invitation = array(
              'user_id'   => $viewer->getIdentity(),
              'sender'     => trim($session->sender),
              'recipient' => trim($recipient),
              'recipient_name' => trim($recipient_name),
              'provider'   => strtolower(trim($session->__get('provider'))),
              'code'       => trim($invite_code),
              'sent_date' => gmdate('Y-m-d H:i:s'),
              'message'   => trim($message),
            );

             if (!$inviterTable->updateInvitation($invitation)) {
               $inviterTable->insertInvitation($invitation);
             }

            $from_sender = ($viewer && $viewer->getIdentity() != 0)
              ? $viewer->getTitle()
              : $session->sender;

            $mail_settings =   array(
              'from' => $from_sender,
              'from_email' => $session->sender,
              'to'   => $recipient,
              'message'    => $message,
              'code'       => $invite_code,
              'link'       => $inviterUrl,
            );
              $settings = Engine_Api::_()->getDbTable('settings', 'core');
                $mail_settings['queque'] = $settings->getSetting('inviter.queque', true);
            // send email
            Engine_Api::_()->getApi('mail', 'core')->sendSystem(
              $recipient,
              'inviter',
              $mail_settings
            );

            // mail sent, so commit
            $sent_emails++;
            $db->commit();
          } catch ( Zend_Mail_Transport_Exception $e) {

            $db->rollBack();
          }
        } // end if (!array_key_exists($recipient, $already_members))
      } // end foreach ($contacts as $recipient=>$recipient_name)
    } // end if (is_array($recipients) && !empty($recipients))

    Engine_Api::_()->getDbtable('statistics', 'inviter')->increment('inviter.sents', $sent_emails);

    return ($sent_emails) ? $sent_emails : -1;
  }

  public function sendMessage(Zend_Session_Namespace $session, $message_attach, $contacts)
  {
    $this->plugin->init($session->oi_session_id);
    $internal = $this->getInternalError();

    if ($internal) return $internal;

    if (!method_exists($this->plugin,'sendMessage'))
    {
      return $this->sendEmails($session, $message_attach, $contacts);
    }
    else
    {
      $inviterTable = Engine_Api::_()->getDbtable('invites', 'inviter');
      $viewer = Engine_Api::_()->user()->getViewer();

        // Verify mail template type
      $mailTemplateTable = Engine_Api::_()->getDbtable('MailTemplates', 'core');
      $mailTemplate = $mailTemplateTable->fetchRow($mailTemplateTable->select()->where('type = ?', 'inviter'));

      if( null === $mailTemplate ) {
        return false;
      }

      $sent = null;
      $sent_emails = 0;
      foreach ($contacts as $id=>$name)
      {
        $already_member = Engine_Api::_()->inviter()->findIdByRecipient(trim($id));
        if (!isset($already_member->new_user_id) || $already_member->new_user_id == 0)
        {
          do{
            $invite_code  = substr(md5(rand(0,999).$id), 10, 7);
          } while( null !== $inviterTable->fetchRow(array('code = ?' => $invite_code)) );

          $inviterUrl = ( _ENGINE_SSL ? 'https://' : 'http://' )
            . $_SERVER['HTTP_HOST']
            . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                'module' => 'inviter',
                'controller' => 'signup',
              ), 'default', true)
            . '?'
            . http_build_query(array('code' => $invite_code, 'email' => $name))
            ;

          $message_attach = str_replace('[invite_url]', $inviterUrl, $message_attach);

          $from_sender = ($viewer && $viewer->getIdentity() != 0)
            ? $viewer->getTitle()
            : $session->sender;

          $params =   array(
               'from' => $from_sender,
               'from_email' => $session->sender,
               'to'   => $name,
               'message'    => $message_attach,
               'code'       => $invite_code,
               'link'       => $inviterUrl,
          );

          // Build subject/body
          $subjectTemplate = $this->tr->_(strtoupper("_email_".$mailTemplate->type."_subject"));
          $bodyTemplate = $this->tr->_(strtoupper("_email_".$mailTemplate->type."_body"));
          foreach( $params as $var => $value ) {
            $raw = trim($var, '[]');
            $var = '[' . $var . ']';
            $val = ( isset($params[$raw]) ? $params[$raw] : $var ); // we could do auto params here?
            $subjectTemplate = str_replace($var, $val, $subjectTemplate);
            $bodyTemplate = str_replace($var, $val, $bodyTemplate);
          }

          $message = array('subject'=>$subjectTemplate, 'body'=>$bodyTemplate, 'attachment'=>'');
          $sent = $this->plugin->sendMessage($session->oi_session_id, $message, array($id=>$name));

          if ($sent!==false)
          {
            //Update Statistics && Add or Update Invitations
            $invitation = array(
              'user_id'   => $viewer->getIdentity(),
              'sender'     => trim($session->sender),
              'recipient' => trim($id),
              'recipient_name' => trim($name),
              'provider'   => strtolower(trim($session->__get('provider'))),
              'code'       => trim($invite_code),
              'sent_date' => gmdate('Y-m-d H:i:s'),
              'message'   => trim($message_attach),
            );
            if (!$inviterTable->updateInvitation($invitation))
            {
              $inviterTable->insertInvitation($invitation);
            }

            $sent_emails++;
          }
        }
      }

      Engine_Api::_()->getDbtable('statistics', 'inviter')->increment('inviter.sents', $sent_emails);

      return $sent;
    }
  }

  public function sendPageEmails(Zend_Session_Namespace $session, $message, $contacts, $page_id = null )
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $recipients = array();
    foreach ($contacts as $email=>$name)
    {
      $recipients[] = $email;
    }
    $sent_emails = 0;

    if (is_array($recipients) && !empty($recipients))
    {
      $userTb = Engine_Api::_()->getDbtable('users', 'user');

      $userSl = $userTb->select()
        ->setIntegrityCheck(false)
        ->from($userTb->info('name'), array('user_id', 'email', 'username'))
        ->where("email IN(?)", $recipients);

      $member_list = $userTb->fetchAll($userSl)->toArray();

      $already_members_array = Engine_Api::_()->inviter()->getPageAdminsLikes($page_id, $member_list);

      $already_members = array();
      foreach ($already_members_array as $already_member)
      {
        $already_members[$already_member['email']] = $already_member['username'];
      }

      foreach ($contacts as $recipient => $recipient_name)
      {
        // perform tests on each recipient before sending invite
        $recipient = trim($recipient);
        // watch out for poorly formatted emails
        if (!empty($recipient) && !array_key_exists($recipient, $already_members)) {
          // Passed the tests, lets start inserting database entry
          // generate unique invite code and confirm it truly is unique

          $page = Engine_Api::_()->getItem('page', $page_id);
          $inviterUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $page->getHref() . '?ctmsg=1';
          $inviterUrl = '<a href="' . $inviterUrl . '">' . $inviterUrl . '</a>';

          $message = str_replace('[invite_url]', $inviterUrl, $message);

          // insert the invite into the database
          $db = Engine_Db_Table::getDefaultAdapter();
          $db->beginTransaction();
          try
          {
            $from_sender = ($viewer && $viewer->getIdentity() != 0)
              ? $viewer->getTitle()
              : $session->sender;

            $mail_settings =   array(
              'from'       => $from_sender,
              'from_email' => $session->sender,
              'to'         => $recipient,
              'message'    => $message,
              'link'       => $inviterUrl,
              'page_title' => $page->getTitle(),
            );

              $settings = Engine_Api::_()->getDbTable('settings', 'core');
              $mail_settings['queque'] = $settings->getSetting('inviter.queque', true);

            // send email
            Engine_Api::_()->getApi('mail', 'core')->sendSystem(
              $recipient,
              'page_inviter',
              $mail_settings
            );

            // mail sent, so commit
            $sent_emails++;
            $db->commit();
          } catch ( Zend_Mail_Transport_Exception $e) {
            $db->rollBack();
          }
        } // end if (!array_key_exists($recipient, $already_members))
      } // end foreach ($contacts as $recipient=>$recipient_name)
    } // end if (is_array($recipients) && !empty($recipients))

    return ($sent_emails) ? $sent_emails : -1;
  }

  public function sendPageMessage(Zend_Session_Namespace $session, $message_attach, $contacts, $page_id = null )
  {
    $this->plugin->init($session->oi_session_id);
    $internal = $this->getInternalError();

    if ($internal) return $internal;

    if (!method_exists($this->plugin,'sendMessage'))
    {
      return $this->sendPageEmails($session, $message_attach, $contacts, $page_id);
    }
    else
    {
      $viewer = Engine_Api::_()->user()->getViewer();

        // Verify mail template type
      $mailTemplateTable = Engine_Api::_()->getDbtable('MailTemplates', 'core');
      $mailTemplate = $mailTemplateTable->fetchRow($mailTemplateTable->select()->where('type = ?', 'page_inviter'));

      if( null === $mailTemplate ) {
        return false;
      }

      $sent = null;
      $sent_emails = 0;

      $invitesTbl = Engine_Api::_()->getDbTable('invites', 'inviter');

      $recipients = array_keys($contacts);

      $select = $invitesTbl->select()
        ->setIntegrityCheck(false)
        ->from($invitesTbl->info('name'), array('user_id' => 'new_user_id', 'recipient'))
        ->where("recipient IN (?) && new_user_id != 0", $recipients);

      $member_list = $invitesTbl->fetchAll($select)->toArray();
      $already_members_array = Engine_Api::_()->inviter()->getPageAdminsLikes($page_id, $member_list);

      $already_members = array();
      foreach ($already_members_array as $member) {
        $already_members[$member['recipient']] = $member;
      }

      foreach ($contacts as $id=>$name)
      {
        if (!isset($already_members[$id]) || $already_members[$id] == 0)
        {
          $page = Engine_Api::_()->getItem('page', $page_id);
          $inviterUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $page->getHref();

          $message_attach = str_replace('[invite_url]', $inviterUrl, $message_attach);

          $from_sender = ($viewer && $viewer->getIdentity() != 0)
            ? $viewer->getTitle()
            : $session->sender;

          $params =   array(
            'from' => $from_sender,
            'from_email' => $session->sender,
            'to'   => $name,
            'message'    => $message_attach,
            'link'       => $inviterUrl,
            'page_title' => $page->getTitle(),
          );

          // Build subject/body
          $subjectTemplate = $this->tr->_(strtoupper("_email_".$mailTemplate->type."_subject"));
          $bodyTemplate = $this->tr->_(strtoupper("_email_".$mailTemplate->type."_body"));
          foreach( $params as $var => $value ) {
            $raw = trim($var, '[]');
            $var = '[' . $var . ']';
            $val = ( isset($params[$raw]) ? $params[$raw] : $var ); // we could do auto params here?
            $subjectTemplate = str_replace($var, $val, $subjectTemplate);
            $bodyTemplate = str_replace($var, $val, $bodyTemplate);
          }

          $message = array('subject'=>$subjectTemplate, 'body'=>$bodyTemplate, 'attachment'=>'');
          $sent = $this->plugin->sendMessage($session->oi_session_id, $message, array($id=>$name));

          if ($sent!==false)
          {
            $sent_emails++;
          }
        }
      }

      return $sent;
    }
  }

  public function setBasePath($path)
  {
    $this->basePath = $path;
  }

  public function setConfigOK($config)
  {
    $this->configOK = $config;
  }
}
