<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AjaxController.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_AjaxController extends Core_Controller_Action_Standard
{
  public function init()
  {
    $XmlRequest = new Zend_Controller_Request_Http();
    if (!$XmlRequest->isXmlHttpRequest())
    {
      header('location: notfound');
    }
  }
  
  public function suggestAction()
  {
     $noneFriend_id = $this->_getParam('nonefriend_id', 0);
     $currents = $this->_getParam('current_suggests', array());
     $widget = $this->_getParam('widget');
     
     unset($currents[$noneFriend_id]);
     
     $current_user_ids = array();
     foreach ($currents as $current) {
       $current_user_ids[] = $current['user_id'];
     }
     $current_user_ids = implode(',', $current_user_ids);
     
     $suggest_array = Engine_Api::_()->getDbtable('nonefriends', 'inviter')->getSuggests(array(
       'current_suggests' => $current_user_ids,
       'noneFriend_id' => $noneFriend_id,
       'total_suggests' => 1
     ));
     
     $suggest = $suggest_array['suggests'];
     
     if ($suggest instanceof User_Model_User)
     {
       $currents[$suggest->getIdentity()] = $suggest->getIdentity();
       
       $mutualFriends = $suggest_array['mutual_friends'][$suggest->getIdentity()];
        
       $content = $this->view->suggest(array('user'=>$suggest, 'mutual_friends'=>explode(',',$mutualFriends), 'widget'=>$widget));
     }
     else 
     {
       $content = '';
     }
     
     $this->view->current_suggests = $currents;
     $this->view->html = Zend_Json::encode($content);
  }
  
  public function addtosuggestAction()
  {
    $viewer = $this->_helper->api()->user()->getViewer();
    $user_ids = $this->_getParam('user_ids');
    
    if (!$viewer->getIdentity() && count($user_ids) == 0) 
    {
      $this->view->result = 0;
      return;
    }

    $table = Engine_Api::_()->getDbtable('nonefriends', 'inviter');
    $select = $table->select()->where('user_id = ?', $viewer->getIdentity())->limit(1);
    
    $nonfriendsRow = $table->fetchRow($select);
    
    $nonfriend_ids = $nonfriendsRow->nonefriend_ids;
    $nonfriend_ids = explode(',', $nonfriend_ids);
    
    $nonfriends = array();
    foreach ($nonfriend_ids as $nonfriend_id)
    {
      $nonfriends[$nonfriend_id] = $nonfriend_id;
    }
    
    foreach ($user_ids as $user_id)
    {
      unset($nonfriends[$user_id]);
    }
    
    if (count($nonfriends) > 0)
    {
      $nonfriends = array_unique($nonfriends);
      $nonfriends = implode(',', $nonfriends);
    
      $nonfriendsRow->nonefriend_ids = $nonfriends;
      $nonfriendsRow->save();
    }
    else 
    {
      $nonfriendsRow->delete();
    }
    
    $this->view->result = 1;
  }

    public function requestAction() {
        $this->_helper->layout->disableLayout();
        $provider = $this->_getParam('provider', false);

        if(!$provider || $provider != 'facebook') {
            $this->view->state = false;
            $this->view->error = "Ivalid provider.";
            return;
        }

        $new_token = $this->_getParam('new_param', false);
        $viewer = $this->_helper->api()->user()->getViewer();
        $session = new Zend_Session_Namespace('inviter');
        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        //get user token
        $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');
        $tokenRow = $tokensTbl->findUserToken($viewer->getIdentity(), $provider);

        $app_id  = $settings->getSetting('inviter.facebook.consumer.key', false);
        $secret = $settings->getSetting('inviter.facebook.consumer.secret', false);
        $fbApi = Engine_Api::_()->loadClass('Inviter_Plugin_fbApi');
        $fbApi->init($app_id, $secret);

        if ($provider == 'facebook' && $new_token && $tokenRow) {
			$url = $this->view->url(array ('module'=>'inviter', 'controller'=>'facebook', 'action'=>'index', 'logout'=>true), 'default' );
            $redirect_url = $host_url . $url;
			
            if($new_token && $tokenRow) {
                $access_token = $tokenRow->toArray();

                $logout_url = $fbApi->getLogoutUrl($access_token['oauth_token'], $redirect_url );
                $tokenRow->delete();
                $this->view->logout_url = $logout_url;
                return;
            }
        } else {
            $url = $this->view->url(array ('module'=>'inviter', 'controller'=>'facebook', 'action'=>'response', 'state'=>true), 'default' );
            $redirect_url = $host_url . $url;
            $login_url = $fbApi->getLoginUrl($redirect_url);
            $this->view->login_url = $login_url;
            return;
        }

    }

    private function _generateForm($tokenRow) {
       $form = new Engine_Form();

       $form->setDisableTranslator(true);

       $params = array('module' => 'inviter', 'controller' => 'oauth', 'action' => 'callback');
       $form->setAction($this->view->url($params, 'default'));
       $form->setTitle(Zend_Registry::get('Zend_Translate')->_('INVITER_Confirm Account'));
       $form->getDecorator('Description')->setOption('escape', false);

       $object_name = ($tokenRow->provider != 'gmail' && $tokenRow->provider != 'yahoo') ? $tokenRow->object_name : "{$tokenRow->object_name} ({$tokenRow->object_id})";
       $form->setDescription($this->view->translate('INVITER_FORM_CONFIRM_ACCOUNT_DESC', $object_name));

       $form->addElement('Button', 'ok', array(
         'onclick'=>'alert("OK");',
         'label' => 'INVITER_Continue'
       ));

       $params['action'] = 'request';
       $params['new'] = 1;
       $form->addElement('Cancel', 'cancel', array(
         'label' => 'INVTTER_Use another account',
         'link' => true,
         'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
         'href' => 'javascript:void(0);',
//         'href' => $this->view->url($params, 'default'),
         'onclick'=>'facebook_inviter.request(1);',
         'decorators' => array(
           'ViewHelper'
         )
       ));

       $form->addDisplayGroup(array('submit', 'cancel'), 'buttons');

       return $form;
     }
}
