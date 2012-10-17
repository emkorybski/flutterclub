<?php
class YounetCore_Bootstrap extends Engine_Application_Bootstrap_Abstract
{

    public function _initABC()
    {
        $front =  Zend_Controller_Front::getInstance();
        $plugin =  new YounetCore_Controller_Helper_License();
        $front->registerPlugin($plugin);
    }
}