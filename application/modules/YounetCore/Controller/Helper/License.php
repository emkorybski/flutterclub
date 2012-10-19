<?php
    class YounetCore_Controller_Helper_License extends Zend_Controller_Plugin_Abstract{


        private function cache($m_v = array()) 
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
                if($data = $cache->load($id))
                {
                    return $data;
                }
                else
                {
                    $cache->save($m_v);
                    return false;
                }
            }
            return false;
        }
        public function routeShutdown( Zend_Controller_Request_Abstract $request )
        {
            $controller = $request->getControllerName(); 
            if(strpos($controller,'admin-') === false)
            {
                return true;
            }
            $m_invalid = array();
            $modules = array();
            //unset($_SESSION['im']);
            if($m_invalid = $this->cache())
            {   
                //$m_invalid = $_SESSION['im'];
            }
            else
            {   
                $modules = $this->getModules();
                if(count($modules)<0) 
                {           
                    return;
                }

                $m_invalid = array();
                $url = 'http://auth.modules2buy.com/ls.php';
                $params = array(
                't' =>'verifymodules',
                'data' =>base64_encode(serialize($modules)),
                );
                $m_invalid = $this->_doPost($params,$url);
                //$m_invalid = json_decode($m_invalid);
                $m_invalid = Zend_Json::decode($m_invalid,Zend_Json::TYPE_OBJECT);
                if($m_invalid === false && !is_object($m_invalid) && !is_array($m_invalid))
                {
                    return;
                }
                foreach($m_invalid as $key=>$m)
                {
                    
                    if($this->checkMainifest($key) == 1)
                                {
                                    unset($m_invalid->$key);
                                    $table = Engine_Api::_()->getDbtable('License', 'YounetCore');
                                    $where = $table->getAdapter()->quoteInto('name = ?', $key);
                                    $table->delete($where);
                                    continue;
                                }
                }
                
                $this->cache($m_invalid);
                $coreModules = $this->getCoreModules();
                if(count($coreModules)>0)
                {
                    foreach($coreModules as $key=>$m)
                    {

                        try{
                            if(array_key_exists($key,$modules)) 
                            {
                                
                                $table = Engine_Api::_()->getDbTable('License', 'YounetCore');
                                $data = array(
                                'current_version'=>$m['version'],
                                'lasted_version'=>$m['version'],
                                );
                                $where = $table->getAdapter()->quoteInto('name = ?', $key);
                                $table->update($data, $where);
                            }
                        }catch(Exception $ex)
                        {
                            //countinue;
                        }

                    } 
                }
            }
            if(count($m_invalid)<=0)
            {
                //nothing;
            }
            else
            {
                foreach($m_invalid as $key=>$m)
                {
                    try{
                       
                        $table = Engine_Api::_()->getDbTable('License', 'YounetCore');
                        $data = array(
                            'is_active' =>0,
                            'lasted_version'=>$m->latest_v,
                            'current_version'=>$m->current_v,
                        );
                        $where = $table->getAdapter()->quoteInto('name = ?', $key);
                        $table->update($data, $where);
                        
                        $table2 = Engine_Api::_()->getDbTable('modules', 'core');
                        $data = array(
                            'enabled' =>0,
                        );
                        $where = $table2->getAdapter()->quoteInto('name = ?', $key);
                        $table2->update($data, $where);  
                        
                        //try enable default module when using advanced module
                        /*if(strpos($key,'adv') === 0 )
                        {
                            $def_module = str_replace("adv","",$key) ;
                            $where = $table2->getAdapter()->quoteInto('name = ?', $def_module);
                            $data = array(
                            'enabled' =>1,
                            );
                            $table2->update($data, $where);  

                        }*/


                    }catch(Exception $ex)
                    {
                        //countinue;
                    }

                }


            }/*
            */
            $params = $request->getParams();
            if($m_invalid === false)
            {
                return false;
            }
            if(!is_array($m_invalid)) 
            {
                return false;
            }
            if(count($m_invalid)>0)
            {
                $module = $params['module'];
                $controller = $request->getControllerName();
                if(array_key_exists($module,$m_invalid))
                {
                    if(isset($m_invalid->$module)&& $this->_checkLock($m_invalid->$module,$controller))//$m_invalid->$module->lock == 1  && strpos($controller,"admin-") == 0)
                    {
                        $request->setModuleName('younet-core');
                        $request->setControllerName('admin-settings');
                        $request->setActionName('yours');
                        $request->setParam('invalid',$module);
                        $_SESSION['invalid'] = $module;

                    }

                }


            }
        }
        private function _checkLock($module,$controller)
        {

            switch($module->lock)
            {
                case 1:
                    return ($module->lock == 1  && strpos($controller,"admin-") == 0);
                case 2:
                    return true;
                case 3:
                    return true;
            }
            return false;
        }
        private function getCoreModules()
        {
            //return $this->_defaultYouNetModules;
            $table2 = Engine_Api::_()->getDbTable('modules', 'core');
            $lst_modules = $table2->fetchAll()->toArray();
            $modules = array();
            if(count($lst_modules)>0)
            {
                foreach($lst_modules as $key =>$m)
                {
                    $modules[$m['name']] = array(
                    'name' => $m['name'],
                    'title' => $m['title'],
                    'version'=>$m['version'],
                    'enabled'=>$m['enabled']
                    );
                }
            }
            return $modules;
        }
        private function getModules()
        {
            //return $this->_defaultYouNetModules;
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
                    );
                }
            }
            return $modules;
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
            curl_setopt($ch,CURLOPT_TIMEOUT,15);
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
    }
?>
