<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Sendnew.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Inviter_Form_Sendnew extends Engine_Form
{
  protected $_errors = array();
  protected $_success;
  public     $_inv;
  protected $_sn;
  
  public function setParams($invite_id)
  {
    $this->_inv = Engine_Api::_()->inviter()->getInvitation($invite_id);
    $this->_sn = $this->_inv->provider . '_' . $this->_inv->sender;
  }
  
  public function init()
  {
    $this->setTitle('INVITER_Resend New Invitation');
    $this->setDescription('INVITER_FORM_SENDNEW_DESCRIPTION')
    ->setAttrib('id','resend_invitation');
    
    $auth_session = new Zend_Session_Namespace('Zend_Auth');
    $email_box = new Zend_Form_Element_Text(array(
      'name'=>'email_box',
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true),),
    ));

    if(!$auth_session->__isset($this->_sn) && !$email_box->isValid($this->_inv->recipient))
    {
      $this->addElement('text', 'email_box', array(
        'label'    => 'INVITER_From',
        'allowEmpty' => false,
        'disable'  =>  true,
        'class'    => 'sendnew',
      ));
    
      $this->addElement('password', 'password_box', array(
        'label'=>'Password',
        'type' => 'password',
        'required' => true,
        'trim' => true,
        'autocomplete'=>'off',
        'class'    => 'sendnew',
      ));
    }
    
    $this->addElement('textarea', 'message_box', array(
      'label' => 'Message',
      'value' => $this->_inv->message,
      'rows'  => 6,
      'cols'  => 45,
      'id'    => 'message_box',
    ));

    $this->addDefaultDecorators($this->message_box);

    $this->addElement('hidden', 'id', array(
      'value' => $this->_inv->invite_id,
    ));
    
    $this->addElement('Button', 'submit', array(
      'type' => 'submit',
      'label' => 'INVITER_Resend Invitation',
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');

    
  }

  //public function sendNewInvite
  public function sendNewInvite()
  {
    $email_box = new Zend_Form_Element_Text(array(
      'name'=>'email_box',
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true),),
    ));

    $translate = Zend_Registry::get('Zend_Translate');
    $inviter = Engine_Api::_()->getApi('openinviter', 'inviter');
    $session = new Zend_Session_Namespace('inviter');
    $session->__set('contacts', array($this->_inv->recipient => $this->_inv->recipient_name));
    $session->__set('provider',strtolower($this->_inv->provider));
    $session->__set('sender', $this->_inv->sender);

		if($email_box->isValid($this->_inv->recipient))
    {
      $session->__set('uploaded_contacts', true);
      $inviter->sendEmails($session, $this->getValue('message_box'), array($this->_inv->recipient => $this->_inv->recipient_name));
      $this->_success = $translate->_("INVITER_Invite sent successfully!");
    }
    else
    {
      $inviter->getPlugins();
      $auth_session = new Zend_Session_Namespace('Zend_Auth');

      $inviter->startPlugin($this->_inv->provider);
      $internal=$inviter->getInternalError();
      if ($internal)
        $this->_errors['inviter']=$internal;

      elseif($auth_session->__isset($this->_sn))
      {
        $sn = $auth_session->__get($this->_sn);
        $session->__set('oi_session_id', $sn['oi_session_id']);
      }

      elseif (!$inviter->login($this->_inv->sender,strtolower($this->getValue('password_box'))))
      {
        $internal=$inviter->getInternalError();
        $this->_errors['login']=($internal?$internal:$translate->_("INVITER_Login failed. Please check the email and password you have provided and try again later"));
      }

      else
      {
        $session->__set('oi_session_id', $inviter->plugin->getSessionID());
        $auth_session->__set($this->_sn, array('oi_session_id' => $session->__get('oi_session_id'), 'provider' => $this->_inv->provider));
      }

      if (count($this->_errors) == 0)
      {
        $sendMessage = $inviter->sendMessage($session, $this->getValue('message_box'), array($this->_inv->recipient => $this->_inv->recipient_name));

        if ($sendMessage===-1)
        {
          $this->_errors['members'] = $translate->_("INVITER_Selected contact is already member");
        }
        elseif ($sendMessage===false)
        {
          $internal=$inviter->getInternalError();
          $this->_errors['internal']=($internal?$internal:$translate->_("INVITER_Failed! Error has occurred while sending your invites.Please try again later."));
        }
        else
        {
          $this->_success = $translate->_("INVITER_Invite sent successfully!");
        }
      }
    }
    
    if (count($this->_errors)>0)
    {
      $this->setErrors($this->_errors);
    }

    $session->unsetAll();
    return $this->_success;
  }
}
