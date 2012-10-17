<?php
    $this->_packageManager = Zend_Registry::get('Engine_Package_Manager');
    $listPack = $this->_packageManager->listInstalledPackages(array('caching' => false));
    $package = null; $l2 = "l2";
    foreach( $listPack as $installedPackage ) {
         
      if(strtolower($installedPackage->getName()) == strtolower($this->plugin_name)) {
       $package = $installedPackage; 
      }
    }
    $pthecgymkqd = true;
    if($package == null)   
    {
        $pthecgymkqd = false;
        throw new Exception("Unknow module name. Please try install again.");
        
    }
    else
    {
        $db = $this->getDb();
        $db_config = include  APPLICATION_PATH . '/application/settings/database.php';
        $select = new Zend_Db_Select($db);
        $select->from($db_config['tablePrefix'].'core_modules')->where('name = ?', 'younet-core');
        $module_core = $select->query()->fetchObject();
        if( !empty($module_core) ) {
            $select = new Zend_Db_Select(($db));
            $select->from($db_config['tablePrefix'].'younetcore_license')->where('name = ?', $package->getName());
            $module_install = $select->query()->fetchObject();
            $data =  array(
                            'name' => $package->getName(),
                            'title' => $package->getTitle(),
                            'descriptions' => $package->getDescription(),
                            'type' => $package->getType(),
                            'current_version' => $package->getVersion(),
                            'lasted_version' => $package->getVersion(),
                            'is_active' => 0,
                            'params' => null,
                            'download_link' => $this->download_link,
                            'demo_link' =>$this->demo_link,
                        );
            if(empty($module_install))
            {
                $db->insert($db_config['tablePrefix'].'younetcore_license',$data);
            }
            else
            {
                $db->update($db_config['tablePrefix'].'younetcore_license',$data,'id = '.$module_install->id);
            }
              
        }
        else
        {
             $pthecgymkqd = false;
             throw new Exception("YouNet Developments Core module cannot be found.Please install it first.");
        }
        
    }
    
    
?>
