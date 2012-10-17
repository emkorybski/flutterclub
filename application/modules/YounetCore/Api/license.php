<?php
    class ynlicense
    {
        private $_defaultYouNetModules = array(
             'younet-core' => array(
                    'name' => 'YouNet Company',
                    'current_v' => '4.01',
                    'latest_v' => '4.01',
                    'demo_url' => 'http://se4demo.modules2buy.com/',
                    'image_url' => '',
                    'purchase' => '',
                    'download' => '',
                    'price' => '',
                    'currency' =>'',
                    'is_active' => '0',
                    ),
           
           
            );
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
      public function getYnModules()
      {
          return $this->_defaultYouNetModules;
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
          $token->time = $params['time'];
          try{
              $pack = Zend_Registry::get('Engine_Package_Manager');
              $gdb = $pack->getDb();
              $db_config = include  APPLICATION_PATH . '/application/settings/database.php';
              try {
                  $gdb->insert($db_config['tablePrefix'].'younetcore_install',$token_data);   
              
              } catch (Exception $e) {
                  
                  throw $e;
              }
              
          }catch(Exception $e){
              //pass
          }
          return $token;

      }
      public function decrypt($data = "")
      {
          $url = $this->_config['url'];
          $domain = $this->getCurrentDomain(); 
          $domain = base64_encode($domain);
          $params['t'] = 'decrypt';
          $params['d'] = $domain;
          $params['data'] = $data;
          $data = $this->_doPost($params,$url);
          return $data;
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
    }
    $api = new ynlicense();
    $l1 = "l1";
?>
