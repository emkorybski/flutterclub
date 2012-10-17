<?php
  if(isset($l1)&&isset($l2)&&isset($_SESSION['l3']) && $l2 =="l2"&& $l2 =="l2"&& $_SESSION['l3'] =="l3")
  {
      $l4 = "l4";
  }
  else
  {
      throw new Exception("Invalid Register Step."); 
  }
  if( isset($pthecgymkqd) && $pthecgymkqd==true)
  {
      $select = new Zend_Db_Select($db);
      //$select->from($db_config['tablePrefix'].'core_modules')->where('name = ?', 'younet-core');
      $pack = Zend_Registry::get('Engine_Package_Manager');
          $gdb = $pack->getDb();
          $db_config = include  APPLICATION_PATH . '/application/settings/database.php';
          try {
              $time = $_POST['time'];   
              $token_data = array(
                'date_active' => $time,
                'params' =>serialize($_POST),
                'is_active' =>1
              );
              $gdb->update($db_config['tablePrefix'].'younetcore_license',$token_data,'id = '.$module_install->id);
              $pl_name= ucfirst($this->plugin_name);
              $path = $this->_operation->getPrimaryPackage()->getBasePath() . '/' . $this->_operation->getPrimaryPackage()->getPath() . '/';
              /***code install db ***/
              include_once $path.'license.php';       
              /**********************/
                 
          
          } catch (Exception $e) {
              
              throw $e;
          }
      throw new Exception("Continue install.");
  }
  else
  {
      throw new Exception("Invalid Register Step.");
  }
  
?>
