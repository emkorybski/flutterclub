<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Invite.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

/*
 * boot priority
 * onView
 * onSubmit - after connection with provider
 * renders tpl
 * onProcess
 *
 */

class Inviter_Plugin_Signup_Invite extends Core_Plugin_FormSequence_Abstract
{
  protected $_name = 'inviter';

  protected $_formClass;

  protected $_script;

  protected $_adminFormClass;

  protected $_adminScript;

  protected $_skip;

  protected $_errors;

  public function __construct()
  {
    $session = new Zend_Session_Namespace('inviter');

    // IE problem
    if (isset($_REQUEST['fb_xd_fragment'])) {
      echo '<script type="text/javascript">if (window.opener && window.opener.provider){window.opener.provider.check_fb_session();} window.close();</script>';
      exit();
    }

    if (!$session->__isset('inviterStep') || $session->__get('inviterStep') == 'getContacts')
    {
      $this->_formClass = 'Inviter_Form_Signup_Invite';
      $this->_script = array('signup/index.tpl', 'inviter');
    }
    elseif($session->__get('inviterStep') == 'friendRequest')
    {
      $this->_formClass = 'Inviter_Form_Signup_Members';
      $this->_script = array('signup/members.tpl', 'inviter');
    }

    elseif($session->__get('inviterStep') == 'sendInvitation')
    {
      $this->_formClass = 'Inviter_Form_Signup_Contacts';
      $this->_script = array('signup/contacts.tpl', 'inviter');
    }

    $this->_adminFormClass = 'Inviter_Form_Admin_Signup_Invite';
    $this->_adminScript = array('admin-signup/invite.tpl', 'inviter');
  }

  public function onSubmit(Zend_Controller_Request_Abstract $request)
  {
	$session = new Zend_Session_Namespace('inviter');
    $skip = $request->getParam("skip");
    $translate = Zend_Registry::get('Zend_Translate');
    $form = $this->getForm();
    if ($skip=="skipFormInviter")
    {
      $this->setActive(false);
      $this->onSubmitIsValid();
      $this->getSession()->skip = true;
      $this->_skip = true;
      return true;
    }
    elseif(!in_array($skip, array("skipFormMembers", "skipFormContacts")))
    {

      if ($form->isValid($_REQUEST)) {

        /**
         * @var $providerApi Inviter_Api_Provider
         */
        $providerApi = Engine_Api::_()->getApi('provider', 'inviter');

        if ($form->getValue('inviterStep')== 'getContacts')
        {
          if (true === ($result = Engine_Api::_()->getApi('openinviter', 'inviter')->getContacts($form, true)) && $session->__isset('members'))
          {
            $this->_form = new Inviter_Form_Signup_Members();
            $this->_formClass = 'Inviter_Form_Signup_Members';
            $this->_script = array('signup/members.tpl', 'inviter');
            $session->__set('inviterStep', 'friendRequest');
            $session->__unset('contacts');
            return false;
          }
          elseif( $result !== true)
          {
            $this->_errors['contacts'] = $result;
          }
          elseif (!$session->__isset('contacts'))
          {
            $this->_errors['contacts'] = $translate->translate("INVITER_Unable to get contacts.");
          }
        }

        elseif($form->getValue('inviterStep') == 'friendRequest')
        {
          $user_ids = $request->getParam("inviterMembers");
          if (count($user_ids) == 0){
            $this->_errors['users'] = $translate->translate('INVITER_Failed! No user selected, please try again later.');
          }  else {
            $session->__set('user_ids', $user_ids);
          }
            $session->__unset('members');
          $session->__set('inviterStep', 'sendInvitation');
          return false;
        }
        elseif($form->getValue('inviterStep') == 'inviterFinalize')
        {
          $contact_ids = $request->getParam("inviterContacts");
          $message = $request->getParam("messageBox", "");

          $provider = $session->__isset('provider') ? $session->__get('provider') : false;
          $providerApi = Engine_Api::_()->getApi('provider', 'inviter');

          if (count($contact_ids) == 0){
            $this->_errors['contacts'] = $translate->translate('INVITER_Failed! No contact selected, please try again later.');
          }
          elseif ($provider == 'twitter' && $providerApi->checkIntegratedProvider($provider) && !$providerApi->checkTwitterMessageLength($message)) {
            $this->_errors['contacts'] = $translate->translate('INVITER_Failed! The text length of your message is over the limit.');
          } else {
            $session->__set('contact_ids', $contact_ids);
            $session->__set('message', $message);
          }
        }
      } else {
        $this->_errors['form_value'] = 'INVITER_Failed! Please check fields and try again later.';
      }
    }

    if (count($this->_errors) > 0)
    {
      foreach ($this->_errors as $error)
      {
        $form->addError($error);
      }

      return false;
    }

    elseif (in_array($form->getValue('inviterStep'), array('getContacts', 'friendRequest')) && $session->__isset('contacts') && $skip != 'skipFormContacts')
    {
      $this->_form = new Inviter_Form_Signup_Contacts();
      $this->_formClass = 'Inviter_Form_Signup_Contacts';
      $this->_script = array('signup/contacts.tpl', 'inviter');
      $session->__set('inviterStep', 'sendInvitation');

      return false;
    }

    parent::onSubmit($request);
  }

  public function onView()
  {
      $session = new Zend_Session_Namespace('inviter');

    if ($session->__isset('invite_code')) {
      // delete core invites
      $coreInvitesTbl = Engine_Api::_()->getDbtable('invites', 'invite');
      $coreInvitesTbl->delete(array("code = '{$session->__get('invite_code')}'"));
    }
  }

  public function onProcess()
  {
    $session = new Zend_Session_Namespace('inviter');
    $provider = $session->__isset('provider') ? $session->__get('provider') : false;
    $providerApi = Engine_Api::_()->getApi('provider', 'inviter');


    if (!$this->_skip && !$this->getSession()->skip && $session->__isset('user_referral_code'))
    {

    }
    if (!$this->_skip && !$this->getSession()->skip && $providerApi->checkIntegratedProvider($provider))
    {
      $contact_ids = ($session->__get('contact_ids')) ? explode(',', $session->__get('contact_ids')) : array();
      $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');

      if($provider == 'facebook') {
          $session->__unset('inviterStep');
          //Engine_Api::_()->getApi('core', 'inviter')->sendFacebookInvites($contact_ids, $session->__get('invite_code'));
      } else {
          $access_token_params = Zend_Json::decode($session->__get('account_info'), Zend_Json::TYPE_ARRAY);
          $token = $tokensTbl->getUserTokenByArray($access_token_params);

          if (!$token) {
              return false;
          }

          $providerApi->sendInvites($token, $provider, $contact_ids);
          $session->__unset('contact_ids');
          $session->__unset('inviterStep');
      }
    }
    elseif (!$this->_skip && !$this->getSession()->skip)
    {

      Engine_Api::_()->getApi('openinviter', 'inviter')->sendRequests();
      Engine_Api::_()->getApi('openinviter', 'inviter')->sendInvitations();
    }
  }

  public function onAdminProcess($form)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $step_table = Engine_Api::_()->getDbtable('signup', 'user');
    $step_row = $step_table->fetchRow($step_table->select()->where('class = ?', 'Inviter_Plugin_Signup_Invite'));
    $step_row->enable = $form->getValue('enable') && ($settings->getSetting('user.signup.inviteonly') != 1);
    $step_row->save();
  }
}