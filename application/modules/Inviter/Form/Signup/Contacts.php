<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Contacts.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Form_Signup_Contacts extends Engine_Form
{
  public $_contacts;

  public function init()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->setAttribs(array(
      'id'=>'invitation_send',
      'class'=>'',
      'decorators'=>array('ViewHelper'),
    ));

    $session = new Zend_Session_Namespace('inviter');

    $provider = $session->__isset('provider') ? $session->__get('provider') : false;
    $providerApi = Engine_Api::_()->getApi('provider', 'inviter');

    if ($providerApi->checkIntegratedProvider($provider)) {
      $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');

      $access_token_params = Zend_Json::decode($session->__get('account_info'), Zend_Json::TYPE_ARRAY);
      $token = $tokensTbl->getUserTokenByArray($access_token_params);

      $contact_list = $providerApi->getNoneMemberContacts($token, $provider, 5000);

      if ($contact_list === false) {
        return false;
      }

        switch ($provider) {
                case 'twitter':
                  $key = 'id';
                  $email = 'email';
                  break;
                case 'hotmail':
                    $key = 'nid';
                    $email = 'id';
                break;

                case 'yahoo':
                case 'lastfm':
                case 'gmail':
                case 'linkedin':
                case 'foursquare':
                case 'mailru':
                  $key = 'nid';
                  $email = 'id';
                  break;

                default:
                  $key = 'id';
                  $email = 'id';
                  break;
              }

      $contacts = array();
      foreach ($contact_list as $contact_info) {
        $contact_info['email'] = $contact_info[$email] . '';
        $contacts[$contact_info[$key].''] = $contact_info;
      }
    } else {
      $contacts = $session->__get('contacts');
    }

    $this->_contacts = $contacts;

    $this->addElement('Textarea', 'messageBox', array(
      'order'=>1,
      'style'=>'width: 470px; max-width: 470px; margin:0px; margin-bottom: 10px;',
      'decorators'=>array('ViewHelper'),
      'value' => $this->getTranslator()->_($settings->getSetting('invite.message')),
    ));

    $this->addElement('Hidden', 'inviterContacts', array(
      'order'=>2,
      'value'=>'',
    ));
    $this->addElement('Hidden', 'inviterStep', array(
      'order'=>3,
      'value'=>'inviterFinalize'
    ));

    $this->addElement('Hidden', 'skip', array('order'=>4));
  }
}