<?php
    class YounetCore_AdminSettingsController extends Core_Controller_Action_Admin
    {
        public function yoursAction()
        {
            $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('younet_core_admin_main', array(), 'younet_core_admin_main_yours');
            $api = Engine_Api::_()->getApi('core','YounetCore');
            $page = null;
            $limit = null;
            $this->view->modules_y = $api->getYnModules($page,$limit);
            $this->view->modules = $api->getModules(); 
            $yn_modules = $api->getYnModulesOnYourSite($this->view->modules_y,$this->view->modules);
            $request = $this->getRequest();
            if($request->isPost())
            {
                $checking = $request->getParams();
                if(isset($checking['checkingmodule']) && $checking['checkingmodule'] =="checkmodule" )
                {
                    try{
                        $api->rmc();
                    }catch(Exception $e)
                    {
                        //do nothing
                    }    
                }
                else
                {
                    try{
                    $api->rmc();
                    $params = $request->getParams();
                    $api->updateModule($params,$params['m']);    
                    }catch(Exception $e)
                    {
                        //do nothing
                    }    
                }
                

            }
            $this->view->modules = $api->getModules();
            $this->view->news = $api->getNews();
            $this->view->urlverify = $api->getUrl();  

        }
        public function younetAction()
        {

            $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('younet_core_admin_main', array(), 'younet_core_admin_main_younet');
            $api = Engine_Api::_()->getApi('core','YounetCore');
            $limit = 10;
            $page = $this->_getParam('page');
            if(!$page)
                $page=1;
            $this->view->modules = $api->getYnModules($page,$limit);
            $this->view->paginator = $paginator = Zend_Paginator::factory($this->view->modules);
            $this->view->urlPhoto = $api->getUrl();
            $paginator->setDefaultItemCountPerPage($limit);
            $paginator->setCurrentPageNumber($this->_getParam('page'));
            $this->view->yours = $api->getModules(); 
            //$this->view->news = $api->getNews();
        }
        public function informationAction()
        {
            $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('younet_core_admin_main', array(), 'younet_core_admin_main_info');
            $api = Engine_Api::_()->getApi('core','YounetCore');
            $this->view->license = $api->getLicenseRules();

        }
        public function pAction()
        {
            $api = Engine_Api::_()->getApi('core','YounetCore');  
            $photourl = $api->getUrl();  
            $m = $this->getRequest()->getParam('m');
            $t = $this->getRequest()->getParam('t');

            $result = $api->getPhotos($m,$t);
            echo $result;die();
        }
        public function lAction()
        {
             $api = Engine_Api::_()->getApi('core','YounetCore');  
             if($this->getRequest()->isPost())
             {
                 $data = $this->getRequest()->getParams(); 
                 $result = $api->verifyM($data);
                 echo $result;
             }
             die();
        }
        public function fAction()
        {
            $name = $this->getRequest()->getParam('ur');
            require_once APPLICATION_PATH . '/application/modules/YounetCore/Api/license.php';  
            $this->_f  = $name;
            $this->plugin_name = $name;
            $this->download_link = "download_link";
            $this->demo_link = "demo_link";
            $token = $api->getToken($this->plugin_name);
            $path = "";
            $this->token = $token;
            $this->urlverify = $api->getUrl();
            Engine_Api::_()->getApi('core','YounetCore')->insertVerifyToken($token);
            include_once APPLICATION_PATH . '/application/modules/YounetCore/Api/license3.php';   
            echo $vars;
            die();
        }
    }
?>