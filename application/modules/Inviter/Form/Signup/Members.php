<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Members.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Form_Signup_Members extends Engine_Form
{
  public $_members;
  
  public function init() 
  {
    $this->setAttribs(array(
      'id'=>'friend_request',
      'class'=>''));

    $session = new Zend_Session_Namespace('inviter');
    $provider = $session->__isset('provider') ? $session->__get('provider') : false;

    $inviterApi = Engine_Api::_()->getApi('core', 'inviter');
    /**
     * @var $providerApi Inviter_Api_Provider
     */
    $providerApi = Engine_Api::_()->getApi('provider', 'inviter');

    if ($providerApi->checkIntegratedProvider($provider)) {
      $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');

      $access_token_params = Zend_Json::decode($session->__get('account_info'), Zend_Json::TYPE_ARRAY);
      $token = $tokensTbl->getUserTokenByArray($access_token_params);

      if (!$token) {
        return false;
      }

      try {
        $contacts = $providerApi->getNoneFriendContacts($token, $provider, 1000);
      } catch (Exception $e) {
        $contacts = false;
      }

      if ($contacts && $contacts['se_users']) {
        $this->_members = Zend_Paginator::factory($contacts['se_users']);
      } else {
        return false;
      }

      $this->addElement('Hidden', 'inviterMembers', array(
        'order'=>1,
      ));

      $this->addElement('Hidden', 'inviterStep', array(
        'order'=>2,
        'value'=>'friendRequest'
      ));

      $this->addElement('Hidden', 'skip', array('order'=>3));

      return;
    }

    
    if (!$session->__isset('members'))
    {
      return false;
    }
    
    $members = $session->__get('members');
    $members_str = "'".implode("','", array_keys($members))."'";
    
    $userTb = Engine_Api::_()->getItemTable('user');
    
    $userSl = $userTb->select()->where("email IN ({$members_str})");
    
    $members = Zend_Paginator::factory($userSl);

    if ($members->count() > 0)
    {
      $this->_members = $members; 
    }

    $this->addElement('Hidden', 'inviterMembers', array(
      'order'=>1,
    ));
    
    $this->addElement('Hidden', 'inviterStep', array(
      'order'=>2,
      'value'=>'friendRequest'
    ));
    
    $this->addElement('Hidden', 'skip', array('order'=>3));
  }
}