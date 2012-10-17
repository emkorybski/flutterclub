<?php
    
    class YounetCore_Api_Core extends Core_Api_Abstract
    {

        private $_config = array(
        'url' =>'',
        );
        function __construct() 
        {

            $this->_config = array(
            'url' =>'http://auth.modules2buy.com/ls.php',
            );
        }
        public function getUrl()
        {
            return $this->_config['url'];
        }
        public function cynm($module_check)
        {
            
        }
        public function rmc()
        {
            if( Zend_Registry::isRegistered('Zend_Cache') &&($cache = Zend_Registry::get('Zend_Cache'))) {
                $id = '_new_younetcore_data_' ;
                $val = (bool) $cache->load($id);
                $frontendOptions = array(
                        'automatic_serialization' => true,
                        'cache_id_prefix' => 'Engine4_',
                        'lifetime' => '86400',
                        'caching' => true,
                );
                $backendOptions = array(
                'cache_dir' => APPLICATION_PATH . '/temporary/cache'
                );
                $cache = Zend_Cache::factory('Core','File', $frontendOptions, $backendOptions);
                $cache->remove($id);
                return true;
            }
            return false;
        }
        public function ss()
        {
            $table = Engine_Api::_()->getDbtable('modules', 'core');
            $rName = $table->info('name');
            $select = $table->select()->from($rName)  ;
            $select->where('name = ?','younet-core');
            $select->where('enabled = ?',1);
            $result = $table->fetchRow($select);
            if($result)
            {
                return true;
            }
            return false;
        }
        public function updateModule($params,$name)
        {
            $table = Engine_Api::_()->getDbtable('License', 'YounetCore');
            unset($params['module']);
            unset($params['controller']);
            unset($params['action']);
            unset($params['rewrite']);
            $data = array(
            'params' =>serialize($params),
            'is_active' => 1,
            );
            $where = $table->getAdapter()->quoteInto('name = ?', $name);
            $table->update($data, $where);
            $table2 = Engine_Api::_()->getDbTable('modules', 'core');
            $data = array(
                'enabled' =>1,
            );
            $where = $table2->getAdapter()->quoteInto('name = ?', $name);
            $table2->update($data, $where);  
        }
        public function insertVerifyToken($token)
        {
            $table = Engine_Api::_()->getDbtable('Install', 'YounetCore');   
            $data = array(
            'token' => $token->tk,
            'params' =>$token->time,
            );   
            $table->insert($data);


        }
        public function getPhotos($m,$t)
        {
             $url = $this->_config['url'];    
             $params['t'] = $t;
             $params['m'] = $m;
             $results = $this->_doPost($params,$url); 
             //$results = json_decode($results);
             return $results;
        }
        public function verifyM($data)
        {
             $url = $this->_config['url'];    
             $results = false;
             $results = $this->_doPost($data,$url);   
             return $results;
        }
        public function getModules()
        {
            $licenseTable = Engine_Api::_()->getDbtable('License', 'YounetCore');
            $lst_modules = $licenseTable->fetchAll()->toArray();
            $modules = array();
            if(count($lst_modules)>0)
            {
                foreach($lst_modules as $key =>$m)
                {
                    $modules[$m['name']] = array(
                    'name' => $m['title'],
                    'current_v' => $m['current_version'],
                    'latest_v' => $m['lasted_version'],
                    'demo_url' => $m['demo_link'],
                    'image_url' => '',
                    'purchase' => '',
                    'download' => $m['download_link'],
                    'price' => '',
                    'currency' =>'',
                    'params' =>$m['params'],
                    'is_active' =>$m['is_active'],
                    //'sort_description' =>$m['sort_description'],
                    );
                }
            }
            return $modules;
        }
        public function getYnModules($page = null ,$limit = null)
        {
            $url = $this->_config['url'];
            $domain = $this->getCurrentDomain(); 
            $domain = base64_encode($domain);
            $params['t'] = 'modules';
            $params['d'] = $domain;
            $modules = $this->_doPost($params,$url);
            //$modules = json_decode($modules);
            $modules = Zend_Json::decode($modules, Zend_Json::TYPE_OBJECT);
            $obj = array();
            foreach($modules as $key=>$module)
            {

                $obj[$key] = $module;
            }
            return $obj;

        }
        protected function _getModules()
        {
            return array();
        }
        public function parse($data)
        {
            $result = array();
            return $result;
        }

        public function getNews()
        {
            if( !class_exists('DOMDocument', false) )
            {
                return false;
            }
            try{
                $rss = Zend_Feed::import('http://socialengine.modules2buy.com/feed');
                $news = array(
                'title'       => $rss->title(),
                'link'        => $rss->link(),
                'description' => $rss->description(),
                'items'       => array()
                );
                $limit = 10;
                $count = 0;
                foreach( $rss as $item )
                {
                    if($count >= $limit)
                    {
                        break;
                    }
                    $news['items'][] = array(
                    'title'       => $item->title(),
                    'link'        => $item->link(),
                    'description' => $item->description(),
                    'pubDate'     => $item->pubDate(),
                    'guid'        => $item->guid(),
                    );
                }

                return $news;
                
            }
            catch(Exception $ex)
            {
                //donothing
            }
            return array();
        }
        public function getToken($module = "")
        {
            if($module == "")
            {
                return false;
            }
            $domain = $this->getCurrentDomain(); 
            $domain = base64_encode($domain);
            $params = array(
            't' =>'token',
            'd' =>$domain,
            'm' => $module,
            'time' =>time(),
            );
            $urlget = $this->_config['url'];
            $token = $this->_doPost($params,$urlget);
            $token = Zend_Json::decode($token,Zend_Json::TYPE_OBJECT);
            $token_data = array(
            'token' => $token->tk,
            'params' => $params['time'],
            );
            $db = Engine_Api::_()->getDbtable('install', 'younetcore')->getAdapter();  
            $db->beginTransaction();
            try {
                $table = $this->_helper->api()->getDbtable('install', 'younetcore');
                $st = $table->createRow();
                $st->setFromArray($token_data);
                $st->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
            return $token;

        }

        public function getVerifyKey($params)
        {
            $url = $this->_config['url'];
            $domain = $this->getCurrentDomain(); 
            $domain = base64_encode($domain);
            $params['t'] = 'license';
            $params['d'] = $domain;
            $license = $this->_doPost($params,$url);
            return $license;
        }

        public function getCurrentDomain()
        {
            return strtolower(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['HOST']) ? $_SERVER['HOST'] : ''));
        }
        public function getLicenseRules()
        {
            $params = array(
            't' =>'viewlicense',
            );
            $urlget = $this->_config['url'];
            $license = $this->_doPost($params,$urlget);
            return $license;
        }
        private function _doPost($params,$url)
        {
            $fields_string = "";
            foreach($params as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
            rtrim($fields_string,'&');
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_POST,count($params));
            curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_TIMEOUT,30);
            $head = curl_exec($ch);
            curl_close($ch);
            return $head;

        }
        public function checkMainifest($module_name = "")
        {
            if($module_name =="")
            {
                return 0;
            }
            $modulesList = Zend_Controller_Front::getInstance()->getControllerDirectory();
            $status = 0;
            foreach ($modulesList as $module => $path) {
                $contentManifestFile = dirname($path) . '/settings/manifest.php';
                if (!file_exists($contentManifestFile))
                    continue;
                $data = include $contentManifestFile;
                if( isset($data['package']['name']) && $data['package']['name'] == $module_name)
                {
                    $status = 1;
                    if(isset($data['package']['dependencies'])&& count($data['package']['dependencies'])>0)
                    {

                        foreach($data['package']['dependencies'] as $dependency)
                        {

                            if(isset($dependency['name']) && $dependency['name'] == 'younet-core')    
                            {
                                return 2;
                            }
                        }

                    }
                }
              

            }
            /*foreach( Zend_Registry::get('Engine_Manifest') as $data ) {

            }*/
            return $status;
        }
        public function getYnModulesOnYourSite($modules,$yours)
        {
            
            $lst_modules = array();
            $db = Engine_Api::_()->getDbtable('license', 'younetcore')->getAdapter();
            if( $db ) {
                try {
                    $table = new Zend_Db_Table(array(
                    'adapter' => $db,
                    'name' => 'engine4_core_modules',
                    ));
                    foreach( $table->fetchAll() as $row ) {
                        $lst_modules[$row->name] = $row->toArray();
                    }
                } catch( Exception $e ) {

                }
            }
            $modules_yn = array();
            if(count($lst_modules)>0)
            {
                foreach($lst_modules as $key =>$m)
                {
                    $t = false;
                    if(count($yours) >0)
                    {
                        if(array_key_exists($m['name'],$yours))
                        {
                            $t = true;
                        }
                    }
                    if(array_key_exists($m['name'],$modules) && $t == false && $m['name']!="younet-core")
                    {
                        if($this->checkMainifest($m['name']) == 2)
                        {
                            $modules_yn[$m['name']] = array(
                                'title' => $m['title'],
                                'name' => $m['name'],
                                'type' => 'module',
                                'current_version' => $m['version'],
                                'lasted_version' => $m['version'],
                                'is_active' =>$m['enabled'],
                                //'sort_description' =>$m['sort_description'],
                            );
                            $db->beginTransaction();
                            try {
                                $table = Engine_Api::_()->getDbtable('License', 'YounetCore');
                                $st = $table->createRow();
                                $st->setFromArray($modules_yn[$m['name']]);
                                $st->save();
                                $db->commit();
                            } catch (Exception $e) {
                                
                                $db->rollback();
                                //throw $e;
                            }
                        }
                        else
                        {
                            $db->beginTransaction();
                            try {
                                $table = Engine_Api::_()->getDbtable('License', 'YounetCore');
                                $where = $table->getAdapter()->quoteInto('name = ?', $m['name']);
                                $table->delete($where);
                                $db->commit();
                            } catch (Exception $e) {
                                
                                $db->rollback();
                                //throw $e;
                            }
                        }
                        
                        
                        
                    }

                }
            }
            return $modules_yn;
        }
    }
?>
