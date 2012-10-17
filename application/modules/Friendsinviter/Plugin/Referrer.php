<?php
class Friendsinviter_Plugin_Referrer extends Zend_Controller_Plugin_Abstract
{

  public function preDispatch(Zend_Controller_Request_Abstract $request) {
    
    if(($request->getModuleName() == 'invite') && ($request->getActionName() == 'index') && ($request->getControllerName() == 'index')) {
      $request->setModuleName('friendsinviter');
    }
    
    try {
    
      $viewer = Engine_Api::_()->user()->getViewer();
    
    } catch (Exception $ex) {

      $viewer = null;
      
    }
    
    if(($viewer instanceof Core_Model_Item_Abstract) && $viewer->getIdentity() ) {
      return;  
    }
    

    $ref = $request->get('ref');
    if(!empty($ref)) {
      setcookie("signup_referer", $ref, 0, "/");
    }
    
    $social = $request->get('social');

    if(!empty($social)) {
      $session = new Zend_Session_Namespace('Friendsinviter');
      $session->social = true;
      $session->code = $request->get('code');
    }
    
    
  }

}
?>
