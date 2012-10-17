<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_IndexController extends Core_Controller_Action_Standard
{
  public function init()
  {
    $this->view->host_url = $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    if (!$this->_helper->api()->user()->getViewer()->getIdentity()) {
      $this->_redirect($host_url . $this->view->url(array(), 'default'));
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $auth_table = Engine_Api::_()->getDbTable('permissions', 'authorization');
    if (!$auth_table->isAllowed('inviter', $viewer, 'use')) {
      $this->_redirectCustom(array('route' => 'default'));
    }
    $this->view->headTranslate(array(
      'INVITER_Failed!, please check your contacts and try again.',
      'INVITER_Members successfully have been added to suggest list.',
      'INVITER_Failed! Please check and try again later.',
      'INVITER_Edit Suggest List',
      'INVITER_Add to Suggest',
      'INVITER_Mutual Friends',
    ));
  }

  public function indexAction()
  {
    $this->_helper->content
      ->setNoRender()
      ->setEnabled();
  }

  public function processAction()
  {

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $session = new Zend_Session_Namespace('inviter');
    $all_providers = Engine_Api::_()->inviter()->getProviders(true);
    $this->view->form = $form = new Inviter_Form_Import();
    $session = new Zend_Session_Namespace('inviter');

    $oauth_account_info = array();
    if ($this->getRequest()->isPost() && $session->__isset('account_info')) {
      $oauth_account_info = $session->__get('account_info');
    }
    $session->unsetAll();

    //check form
    $params = $this->_getAllParams();

    // save oauth session
    $oauth_account_info = array();
    if ($this->getRequest()->isPost() && $session->__isset('account_info')) {
      $oauth_account_info = $session->__get('account_info');
    }

    $fb_session_error = $session->__isset('inviter_fb_session_error');

    $session->unsetAll();

    if ($this->getRequest()->isPost() && $params['provider_box']) {

      $sel_provider = $all_providers->getRowMatching(array('provider_title' => $params['provider_box']));

      // check supported provider
      if (!$sel_provider) {
        $form->addError('Please select your provider from list');
        $form->markAsError();
      }
    }

    $post_params = array();

    if ($this->getRequest()->isPost()) {
      $post_params = $this->getRequest()->getPost();
      $providerApi = Engine_Api::_()->getApi('provider', 'inviter');
      $post_params['provider_box'] = $providerApi->checkProvider($post_params['provider_box']);
    }

    if ($this->getRequest()->isPost() && $form->isValid($post_params)) {
      $providerApi = Engine_Api::_()->getApi('provider', 'inviter');

      if ($providerApi->checkIntegratedProvider($post_params['provider_box'])) {
        $session->__set('provider', $params['provider_box']);
        $session->__set('account_info', $oauth_account_info);

        if ($viewer->getIdentity()) {
          $this->_redirectCustom(array('route' => 'inviter_members'));
        } else {
          $this->_redirectCustom(array('route' => 'inviter_contacts'));
        }
      }
      elseif (true === ($error_msg = Engine_Api::_()->getApi('openinviter', 'inviter')->getContacts($form)))
      {
        if ($viewer->getIdentity()) {
          $this->_redirectCustom(array('route' => 'inviter_members'));
        } else {
          $this->_redirectCustom(array('route' => 'inviter_contacts'));
        }
      }
      else
      {
        $this->view->form->addError($error_msg);
      }
    } else {
      $this->_redirectCustom(array('route' => 'inviter_general'));
    }
  }

  public function uploadContactsAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $translate->_('INVITER_Invalid request method');
      return;
    }

    $values = $this->getRequest()->getPost();
    if (empty($values['Filename'])) {
      $this->view->status = false;
      $this->view->error = $translate->_('INVITER_No file');
      return;
    }

    if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
      $this->view->status = false;
      $this->view->error = $translate->_('INVITER_Invalid Upload');
      return;
    }

    /* try
    {*/
    $uploaded_file = $_FILES['Filedata']['tmp_name'];
    $this->view->contacts = $contactRows = $this->_helper->api()->inviter()->getContactsFromFile($uploaded_file);

    //            $this->view->status = false;
    //            $this->view->error = "<br>".$contactRows."<br><br>";
    //        return;

    if (empty($contactRows)) {
      $this->view->status = false;
      $this->view->error = $translate->_('INVITER_No contacts found in uploaded file.');
      return;
    }

    $session = new Zend_Session_Namespace('inviter');

    $contactRows = Engine_Api::_()->getApi('openinviter', 'inviter')->structContacts($contactRows);


    if (empty($contactRows['contacts']) && empty($contactRows['members'])) {
      $this->view->status = false;
      $this->view->error = $translate->_("INVITER_You're already in friendship with all of your uploaded contacts.");
      return;
    }

    if (!empty($contactRows['members'])) {
      $session->__set('members', $contactRows['members']);
    }

    if (!empty($contactRows['contacts'])) {
      $session->__set('contacts', $contactRows['contacts']);
    }

    $session->__set('uploaded_contacts', true);
    $this->view->status = true;
    /* }

    catch (Exception $e)
    {
        $this->view->status = false;
        $this->view->error = $translate->_("INVITER_An error occurred.");
        // throw $e;
        return;
    }*/
  }

  public function writeContactsAction()
  {
    $recipients = $this->_getParam('recipients');
    $message = $this->_getParam('message');

    if (is_string($recipients)) {
      $recipients = preg_split("/[\s,]+/", $recipients);
    }

    if (is_array($recipients)) {
      $recipients = array_map('strtolower', array_unique(array_filter(array_map('trim', $recipients))));
    }

    $validate = new Zend_Validate_EmailAddress();
    $contacts = array();

    foreach ($recipients as $recipient)
    {
      $exploded = explode('@', $recipient);

      if ($validate->isValid($recipient) && is_array($exploded) && count($exploded) == 2) {
        $contacts[$recipient] = trim($exploded[0]);
      }
    }

    $translate = Zend_Registry::get('Zend_Translate');

    if (empty($contacts)) {
      $this->view->status = 0;
      $this->view->message = $translate->_(array(
        "INVITER_Failed!, incorrect email adress has been written.",
        "Failed!, incorrect email adresses have been written.",
        count($recipients)));
      return;
    }

    $viewer = $this->_helper->api()->user()->getViewer();
    $session = new Zend_Session_Namespace('inviter');
    $session->__set('sender', $viewer->email);
    $session->__set('contacts', $contacts);

    $page_id = $this->_getParam('page_id', null);
    if ($page_id) {
      $sent = (int)Engine_Api::_()->getApi('openinviter', 'inviter')->sendPageEmails($session, $message, $contacts, $page_id);
    } else {
      $sent = (int)Engine_Api::_()->getApi('openinviter', 'inviter')->sendEmails($session, $message, $contacts);
    }

    if ($sent > 0) {
      $this->view->status = 1;
      $this->view->message = $translate->_(array("INVITER_Invitation has been sent successfully.", "Invitations have been sent successfully.", $sent));
      $session->unsetAll();
      return;
    }

    $this->view->status = 2;
    $this->view->message = ($page_id)
      ? $translate->_("PAGE_INVITER_Written contact's already member.")
      : $translate->_(array("INVITER_Written contact's already member.", "Written contacts're already members.", count($recipients)));
    return;
  }

  public function membersAction()
  {
    $session = new Zend_Session_Namespace('inviter');
    $viewer = $this->_helper->api()->user()->getViewer();

    /**
     * @var $providerApi Inviter_Api_Provider
     */
    $providerApi = Engine_Api::_()->getApi('provider', 'inviter');

    if ($viewer->getIdentity()) {
      $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('inviter_profile', array(), 'inviter_profile_invite');
    }

    if ($this->_getParam('provider', false)) {
      $session->__set('provider', $this->_getParam('provider'));
    }

    $provider = $session->__isset('provider') ? $session->__get('provider') : '';
    $provider = $providerApi->checkProvider($provider);
    $provider = strtolower(str_replace('.', '', $provider));
    $provider = strtolower(str_replace('!', '', $provider));

    if ($provider && $providerApi->checkIntegratedProvider($provider)) {
      /**
       * @var $tokensTbl Inviter_Model_DbTable_Tokens
       */
      $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');
      $token = $tokensTbl->getUserToken($viewer->getIdentity(), $provider);

      $contacts = $providerApi->getNoneFriendContacts($token, $provider, 5000);

      if ($contacts === false) {
        $this->_redirectCustom(array('route' => 'inviter_general'));
      }

      if ($contacts && isset($contacts['se_users']) && $contacts['se_users']) {
        $this->view->members = Zend_Paginator::factory($contacts['se_users']);
      }
      else
      {
        $this->_redirectCustom(array('route' => 'inviter_contacts'));
      }

      return;
    }

    if (!$session->__isset('members')) {
      $this->_redirectCustom(array('route' => 'inviter_contacts'));
    }

    $members = $session->__get('members');
    $members_str = "'" . implode("','", array_keys($members)) . "'";

    $userTb = Engine_Api::_()->getItemTable('user');

    $userSl = $userTb->select()->where("email IN ({$members_str})");

    $members = Zend_Paginator::factory($userSl);

    if ($members->count() > 0) {
      $this->view->members = $members;
    }
    else
    {
      $this->_redirectCustom(array('route' => 'inviter_contacts'));
    }
  }

  public function contactsAction()
  {
    $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    $providerApi = Engine_Api::_()->getApi('provider', 'inviter');
    $viewer = $this->_helper->api()->user()->getViewer();
    if ($viewer->getIdentity()) {
      $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('inviter_profile', array(), 'inviter_profile_invite');
    }

    $provider = '';
    $session = new Zend_Session_Namespace('inviter');

    if ($session->__isset('provider')) {
      $this->view->provider = $provider = $session->__get('provider');
    }
    elseif (isset($_REQUEST['provider']) && $_REQUEST['provider'] == 'facebook') {
      $session->__set('provider', $_REQUEST['provider']);
      $this->view->provider = $provider = $session->__get('provider');
    }

    $facebook = Inviter_Api_Provider::getFBInstance();
    $provider = $providerApi->checkProvider($provider);
    $provider = strtolower(str_replace('.', '', $provider));
    $provider = strtolower(str_replace('!', '', $provider));

    $this->view->provider = $provider;
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->host = $_SERVER['HTTP_HOST'];

    if ($provider == 'facebook') {

      //            $this->view->app_id = $settings->getSetting('inviter.facebook.consumer.key', false);
      //            $redirect_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'index', 'action' => 'facebookaftersend'), 'default');
      //            $this->view->redirect_url = $redirect_url;
      //            $this->view->invite_code = $invite_code = substr(md5(rand(0, 999) . time() . $provider), 10, 7);
      //            $this->view->invitation_url = $providerApi->getInvitationUrl($invite_code, null, null);
      //$this->view->picture = $_SERVER['HTTP_HOST'] . $this->view->baseUrl() . '/application/modules/Inviter/externals/images/inviter_icon.png';
      //        $this->view->host = 'kontroler.kg';
    } elseif ($provider == 'myspace') {
      $this->view->consumer_key = $settings->getSetting('inviter.myspace.consumer.key', false);
      $this->view->host = $host_url;
    }

    if ($providerApi->checkIntegratedProvider($provider)) {
      /**
       * @var $tokensTbl Inviter_Model_DbTable_Tokens
       */
      $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');
      $token = $tokensTbl->getUserToken($viewer->getIdentity(), $provider);

      if ($token === false && $session->__isset('account_info')) {
        $access_token_params = Zend_Json::decode($session->__get('account_info'), Zend_Json::TYPE_ARRAY);
        $token = $tokensTbl->getUserTokenByArray($access_token_params);
      }

      $contact_list = $providerApi->getNoneMemberContacts($token, $provider, 5000);

      if ($contact_list === false) {
        $this->_redirectCustom(array('route' => 'inviter_general'));
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

//        case 'facebook':
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
        $contact_info['email'] = $contact_info[$email];
        $contacts[$contact_info[$key]] = $contact_info;
      }

      if (count($contacts) == 0) {
        $this->_redirectCustom(array('route' => 'inviter_general'));
      }
      $this->view->contacts = $contacts;

      return;
    }

    if (!$session->__isset('contacts')) {
      $this->_redirect('inviter');
    }

    $contacts = $session->__get('contacts');

    if (count($contacts) > 0) {
      $this->view->contacts = $contacts;
    }
    else
    {
      $this->_redirect('inviter');
    }
  }

  public function invitationsendAction()
  {
    $contact_ids = $this->_getParam('contact_ids');
    $contact_ids = (is_array($contact_ids)) ? $contact_ids : explode(',', $contact_ids);

    $message = $this->_getParam('message');
    $translate = Zend_Registry::get('Zend_Translate');
    $session = new Zend_Session_Namespace('inviter');
    $viewer = $this->_helper->api()->user()->getViewer();
    /**
     * @var $providerApi Inviter_Api_Provider
     */
    $providerApi = Engine_Api::_()->getApi('provider', 'inviter');

    if (!Engine_Api::_()->authorization()->isAllowed('inviter', null, 'use') && count($contact_ids) == 0) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('INVITER_No contacts specified');
      return;
    }

    $this->view->form = $form = new Inviter_Form_Send(array('params' => false));
    $form->addElement('Hidden', 'contact_ids', array('value' => implode(',', $contact_ids)));
    $form->addElement('Hidden', 'message_box', array('value' => $message));


    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $translate->_('INVITER_No action taken');
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = $translate->_('INVITER_Invalid data');
      return;
    }

    $session->__set('contact_ids', $contact_ids);
    $session->__set('message', $message);

    $provider = $session->__get('provider');
    $provider = $providerApi->checkProvider($provider);
    if ($providerApi->checkIntegratedProvider($provider)) {

      /**
       * @var $tokensTbl Inviter_Model_DbTable_Tokens
       */
      $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');
      $token = $tokensTbl->getUserToken($viewer->getIdentity(), $provider);

      if ($token === false && $session->__isset('account_info')) {
        $access_token_params = Zend_Json::decode($session->__get('account_info'), Zend_Json::TYPE_ARRAY);
        $token = $tokensTbl->getUserTokenByArray($access_token_params);
      }

      if ($provider == 'twitter') {
        $valid_msg_length = $providerApi->checkTwitterMessageLength($message);

        if (!$valid_msg_length) {
          $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 7000,
            'messages' => array($translate->_('INVITER_There was an error sending your message: The text length of your message is over the limit.'))
          ));
          return;
        }
      }
      $captcha_value = $this->getRequest()->getParam('captcha_value', false);
      $captcha_token = $this->getRequest()->getParam('captcha_token', false);

      $error_msg = $providerApi->sendInvites($token, $provider, $contact_ids, null, $captcha_value, $captcha_token);

      if ($provider == 'twitter') {
        if (isset($error_msg['twitter_step'])) {
          while (isset($error_msg['twitter_step'])) {
            $error_msg = $providerApi->sendInvites($token, $provider, $contact_ids, null, $captcha_value, $captcha_token);
          }
        }
      }

      if (isset($error_msg['captcha_token']) && $provider == 'orkut') {
        $this->view->img_url = $img_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->baseUrl() . $providerApi->getOrkutCaptcha($token, $error_msg['captcha_url']);
        $this->view->captcha_token = $error_msg['captcha_token'];
        return;
      }
    }
    else
    {
      $error_msg = Engine_Api::_()->getApi('openinviter', 'inviter')->sendInvitations();
    }

    if (true !== $error_msg) {
      $session->__set('success', 1);
      $session->__set('message', $translate->_('INVITER_Invitations hasn\'t been sent to your contacts.'));
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRedirect' => $this->view->url(array('module' => 'inviter', 'controller' => 'index', 'action' => 'index'), 'default', true),
        'messages' => $error_msg
      ));
    }
    else
    {
      $session->__set('success', 2);
      $session->__set('message', $translate->_('INVITER_Invitations sucessfully have been sent to your contacts.'));
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRedirect' => $this->view->url(array('module' => 'inviter', 'controller' => 'index', 'action' => 'index'), 'default', true),
        'messages' => array($translate->_('INVITER_Invitations sucessfully have been sent to your contacts.'))
      ));
    }
  }

  public function friendrequestAction()
  {
    $user_ids = $this->_getParam('user_ids');
    $user_ids = (is_array($user_ids)) ? $user_ids : explode(',', $user_ids);
    $translate = Zend_Registry::get('Zend_Translate');
    $session = new Zend_Session_Namespace('inviter');

    if (!$this->_helper->requireUser()->isValid() && count($user_ids) == 0) {
      $this->view->status = false;
      $this->view->error = $translate->_('INVITER_No members specified');
      return;
    }

    $this->view->form = $form = new Inviter_Form_Friendrequest(array('params' => array('user_ids' => $user_ids)));

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $translate->_('INVITER_No action taken');
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = $translate->_('INVITER_Invalid data');
      return;
    }

    $session->__set('user_ids', $user_ids);
    $error = Engine_Api::_()->getApi('openinviter', 'inviter')->sendRequests();

    $this->view->status = true;

    if (!$error) {
      $message = $translate->_('INVITER_Your friend request has been sent.');
    }
    else
    {
      $message = $translate->_('INVITER_Friend request was not sent to some of selected members.');
    }

    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRedirect' => $this->view->url(array(), 'inviter_contacts', true),
      'messages' => array($message)
    ));
  }

  public function pageinviterAction()
  {
    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('page')) {
      $this->_redirect(array('route' => 'default'));
    }

    $page_id = $this->_getParam('page_id');
    if (!$page_id) {
      $this->_redirect('browse-pages');
    }
    $this->view->page = Engine_Api::_()->getItem('page', $page_id);

    $this->view->viewer = $viewer = $this->_helper->api()->user()->getViewer();
    $session = new Zend_Session_Namespace('inviter');

    // save oauth session
    $oauth_account_info = array();
    if ($this->getRequest()->isPost() && $session->__isset('account_info')) {
      $oauth_account_info = $session->__get('account_info');
    }

    $session->unsetAll();

    $this->view->providers = Engine_Api::_()->inviter()->getIntegratedProviders();
    $this->view->count = count($this->view->providers);
    if ($viewer->getIdentity()) {
      $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('inviter_profile', array(), 'inviter_profile_invite');
    }

    //GET INVITER
    $form = new Inviter_Form_Widget_PageImport();
    $form->getElement('page_id')->setValue($page_id);
    $this->view->form = $form;
    $viewer = $this->_helper->api()->user()->getViewer();

    if ($viewer->getIdentity()) {
      $this->view->form_upload = $form_upload = new Inviter_Form_Upload();
      $this->view->form_write = $form_write = new Inviter_Form_Widget_PageWrite();
    }

    //        $this->view->providers = $providers = Engine_Api::_()->inviter()->getProviders(false, 15);
    $all_providers = Engine_Api::_()->inviter()->getProviders(true);

    //GET SEARCH FORM
    $this->view->form_search = $form_search = new User_Form_Search(array(
      'type' => 'user'
    ));
    $form_search->setAction($this->view->url(array(), 'user_general'));

    $form_search->addAttribs(array('name' => 'field_search_criteria'));

    if ($this->_getParam('success', 0)) {
      $form->addNotice('INVITER_Invites sent successfully!');
    }

    //check form
    $params = $this->_getAllParams();

    if ($this->getRequest()->isPost() && $params['provider_box']) {
      $sel_provider = $all_providers->getRowMatching(array('provider_title' => $params['provider_box']));

      // check supported provider
      if (!$sel_provider) {
        $form->addError('Please select your provider from list');
        $form->markAsError();
      }
    }

    $page_id = $this->_getParam('page_id', null);
    $post_params = array();
    if ($this->getRequest()->isPost()) {
      $post_params = $this->getRequest()->getPost();
      $providerApi = Engine_Api::_()->getApi('provider', 'inviter');
      $post_params['provider_box'] = $providerApi->checkProvider($post_params['provider_box']);
    }

    if ($this->getRequest()->isPost() && $form->isValid($post_params)) {
      $providerApi = Engine_Api::_()->getApi('provider', 'inviter');

      if ($providerApi->checkIntegratedProvider($params['provider_box'])) {
        $session->__set('provider', $params['provider_box']);
        $session->__set('account_info', $oauth_account_info);

        if ($viewer->getIdentity()) {
          $this->_redirectCustom($this->view->url(array('page_id' => $page_id), 'page_inviter_members', true));
        } else {
          $this->_redirectCustom($this->view->url(array('page_id' => $page_id), 'page_inviter_contacts', true));
        }
      }
      elseif (true === ($error_msg = Engine_Api::_()->getApi('openinviter', 'inviter')->getContacts($form)))
      {
        if ($viewer->getIdentity()) {
          $this->_redirectCustom($this->view->url(array('page_id' => $page_id), 'page_inviter_members', true));
        } else {
          $this->_redirectCustom($this->view->url(array('page_id' => $page_id), 'page_inviter_contacts', true));
        }
      }
      else
      {
        $this->view->form->addError($error_msg);
      }
    }

    //GET SUGGESTS
    if ($this->_helper->api()->user()->getViewer()->getIdentity()) {
      $suggest_array = Engine_Api::_()->getDbtable('nonefriends', 'inviter')->getSuggests();

      $current_suggests = array();

      $noneFriendCount = $suggest_array['noneFriendCount'];
      $suggest_array = $suggest_array['suggests'];

      foreach ($suggest_array as $suggest)
      {
        $current_suggests[$suggest->getIdentity()]['user_id'] = $suggest->getIdentity();
      }

      $this->view->noneFriendCount = $noneFriendCount;
      $this->view->current_suggests = $current_suggests;
      $this->view->suggests = $suggests = $this->view->suggests(array('suggests' => $suggest_array));
    }

    //      $this->view->html = $this->view->render('index / page . tpl');
  }

  public function pagemembersAction()
  {
    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('page')) {
      $this->_redirect(array('route' => 'default'));
    }
    $page_id = $this->_getParam('page_id', null);
    if (!$page_id) {
      $this->_redirect('browse-pages');
    }
    $session = new Zend_Session_Namespace('inviter');
    $viewer = $this->_helper->api()->user()->getViewer();

    /**
     * @var $providerApi Inviter_Api_Provider
     */
    $providerApi = Engine_Api::_()->getApi('provider', 'inviter');

    if ($viewer->getIdentity()) {
      $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('inviter_profile', array(), 'inviter_profile_invite');
    }

    if ($this->_getParam('provider', false)) {
      $session->__set('provider', $this->_getParam('provider'));
    }

    $provider = $session->__isset('provider') ? $session->__get('provider') : '';
    $provider = $providerApi->checkProvider($provider);
    $provider = strtolower(str_replace(' . ', '', $provider));
    $provider = strtolower(str_replace('!', '', $provider));

    if ($provider && $providerApi->checkIntegratedProvider($provider)) {
      /**
       * @var $tokensTbl Inviter_Model_DbTable_Tokens
       */
      $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');
      $token = $tokensTbl->getUserToken($viewer->getIdentity(), $provider);
      $contacts = $providerApi->getNoneFriendContacts($token, $provider, 5000);

      if ($contacts === false) {
        $this->_redirectCustom(array('route' => 'page_inviter'));
      }

      if ($contacts && isset($contacts['se_users']) && $contacts['se_users']) {
        $this->view->members = Zend_Paginator::factory($contacts['se_users']);
      }
      else
      {
        $this->_redirectCustom($this->view->url(array('page_id' => $page_id), 'page_inviter_contacts', true));
      }

      return;
    }

    if (!$session->__isset('members')) {
      $this->_redirectCustom($this->view->url(array('page_id' => $page_id), 'page_inviter_contacts', true));
    }

    $members = $session->__get('members');
    $members_str = "'" . implode("','", array_keys($members)) . "'";

    $userTb = Engine_Api::_()->getItemTable('user');

    $userSl = $userTb->select()->where("email IN ({$members_str})");

    $members = Zend_Paginator::factory($userSl);

    if ($members->count() > 0) {
      $this->view->members = $members;
    }
    else
    {
      $this->_redirectCustom($this->view->url(array('page_id' => $page_id), 'page_inviter_contacts', true));
    }
  }

  public function pagecontactsAction()
  {
    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('page')) {
      $this->_redirect(array('route' => 'default'));
    }
    $this->view->page_id = $page_id = $this->_getParam('page_id', null);
    if (!$page_id) {
      $this->_redirect('browse-pages');
    }

    $this->view->page = Engine_Api::_()->getItem('page', $page_id);

    $viewer = $this->_helper->api()->user()->getViewer();

    if ($viewer->getIdentity()) {
      $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('inviter_profile', array(), 'inviter_profile_invite');
    }

    $provider = '';
    $session = new Zend_Session_Namespace('inviter');

    if ($session->__isset('provider')) {
      $this->view->provider = $provider = $session->__get('provider');
    }
    elseif (isset($_REQUEST['provider']) && $_REQUEST['provider'] == 'facebook') {
      $session->__set('provider', $_REQUEST['provider']);
      $this->view->provider = $provider = $session->__get('provider');
    }
    $providerApi = Engine_Api::_()->getApi('provider', 'inviter');
    $facebook = Inviter_Api_Provider::getFBInstance();
    $provider = $providerApi->checkProvider($provider);
    $provider = strtolower(str_replace(' . ', '', $provider));
    $provider = strtolower(str_replace('!', '', $provider));


    if ($provider == 'facebook') {
      $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
      $this->view->provider = $provider;
      $settings = Engine_Api::_()->getApi('settings', 'core');
      $this->view->app_id = $settings->getSetting('inviter.facebook.consumer.key', false);
      $redirect_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'index', 'action' => 'facebookaftersend'), 'default');
      $this->view->redirect_url = $redirect_url;
      $this->view->invite_code = $invite_code = substr(md5(rand(0, 999) . time() . $provider), 10, 7);
      $this->view->invitation_url = $host_url . $this->view->page->getHref();
      $this->view->page_id = $page_id;
      $this->view->host = $_SERVER['HTTP_HOST'];
      //        $this->view->host = 'kontroler.kg';
    }

    if ($providerApi->checkIntegratedProvider($provider)) {
      /**
       * @var $tokensTbl Inviter_Model_DbTable_Tokens
       */
      $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');
      $token = $tokensTbl->getUserToken($viewer->getIdentity(), $provider);

      if ($token === false && $session->__isset('account_info')) {
        $access_token_params = Zend_Json::decode($session->__get('account_info'), Zend_Json::TYPE_ARRAY);
        $token = $tokensTbl->getUserTokenByArray($access_token_params);
      }

      $contact_list = $providerApi->getNoneMemberContacts($token, $provider, 5000);

      if ($contact_list === false) {

        $this->_redirectCustom(array('route' => 'page_inviter'));
      }

      switch ($provider) {
        case 'twitter':
          $key = 'id';
          $email = 'email';
          break;
        case 'yahoo':
        case 'hotmail':
        case 'gmail':
        case 'linkedin':
        case 'lastfm':
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
        $contact_info['email'] = $contact_info[$email];
        $contacts[$contact_info[$key]] = $contact_info;
      }

      if (count($contacts) == 0) {

        $this->_redirectCustom(array('route' => 'page_inviter'));
      }

      $this->view->contacts = $contacts;
      return;
    }

    if (!$session->__isset('contacts')) {
      $this->_redirect('page-inviter');
    }

    $contacts = $session->__get('contacts');

    if (count($contacts) > 0) {
      $this->view->contacts = $contacts;
    }
    else
    {
      $this->_redirect('page-inviter');
    }
  }

  public
  function pageinvitationsendAction()
  {
    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('page')) {
      $this->_redirect(array('route' => 'default'));
    }
    $this->view->page_id = $page_id = (int)$this->_getParam('page_id', 0);

    if (!$page_id) {
      $this->_redirect('browse-pages');
    }

    //      $this->_helper->layout->disableLayout();
    $contact_ids = $this->_getParam('contact_ids');
    $contact_ids = (is_array($contact_ids)) ? $contact_ids : explode(',', $contact_ids);

    $message = $this->_getParam('message');
    $translate = Zend_Registry::get('Zend_Translate');
    $session = new Zend_Session_Namespace('inviter');
    $viewer = $this->_helper->api()->user()->getViewer();

    /**
     * @var $providerApi Inviter_Api_Provider
     */
    $providerApi = Engine_Api::_()->getApi('provider', 'inviter');

    if (!Engine_Api::_()->authorization()->isAllowed('inviter', null, 'use') && count($contact_ids) == 0) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('PAGE_INVITER_No contacts specified');
      return;
    }

    $this->view->form = $form = new Inviter_Form_Widget_PageSend(array('params' => false));
    $form->addElement('Hidden', 'contact_ids', array('value' => implode(',', $contact_ids)));
    $form->addElement('Hidden', 'message_box', array('value' => $message));


    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $translate->_('PAGE_INVITER_No action taken');
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = $translate->_('PAGE_INVITER_Invalid data');
      return;
    }

    $session->__set('contact_ids', $contact_ids);
    $session->__set('message', $message);

    $provider = $session->__get('provider');
    $provider = strtolower(str_replace('.', '', $provider));
    $provider = strtolower(str_replace('!', '', $provider));


    if ($providerApi->checkIntegratedProvider($provider)) {
      /**
       * @var $tokensTbl Inviter_Model_DbTable_Tokens
       */
      $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');
      $token = $tokensTbl->getUserToken($viewer->getIdentity(), $provider);

      if ($token === false && $session->__isset('account_info')) {
        $access_token_params = Zend_Json::decode($session->__get('account_info'), Zend_Json::TYPE_ARRAY);
        $token = $tokensTbl->getUserTokenByArray($access_token_params);
      }

      if ($provider == 'twitter') {
        $valid_msg_length = $providerApi->checkTwitterMessageLength($message, $page_id);

        if (!$valid_msg_length) {
          $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 7000,
            'messages' => array($translate->_('PAGE_INVITER_There was an error sending your message: The text length of your message is over the limit.'))
          ));
          return;
        }
      }
      $error_msg = $providerApi->sendInvites($token, $provider, $contact_ids, $page_id);

    }
    else
    {
      $error_msg = Engine_Api::_()->getApi('openinviter', 'inviter')->sendInvitations($page_id);
    }
    $page = Engine_Api::_()->getItem('page', $page_id);
    if (true !== $error_msg) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRedirect' => $this->view->url(array('page_id' => $page->url), 'page_view', true),
        'messages' => $error_msg
      ));
    }
    else
    {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRedirect' => $this->view->url(array('page_id' => $page->ukonrl), 'page_view', true),
        'messages' => array($translate->_('PAGE_INVITER_Invitations sucessfully have been sent to your contacts.'))
      ));
    }
  }

  public
  function facebookaftersendAction()
  {
    $code = $this->_getParam('code', '');
    $name = $this->_getParam('name', '');
    if (!$code) return false;
    $codes_tbl = Engine_Api::_()->getDbTable('codes', 'inviter');
    $user_id = $codes_tbl->getUserId($code);
    if (!$user_id) return false;
    $user = Engine_Api::_()->getItem('user', $user_id);
    if (!$user) return false;
    if (!$name)
      $name = ($user->displayname) ? $user->displayname : $user->getTitle();

    $message = $this->_getParam('message', '');

    $invitesTbl = Engine_APi::_()->getDbTable('invites', 'inviter');
    $invitesTbl->insertInvitation(array(
      'user_id' => $user_id,
      'sender' => $name,
      'recipient' => '',
      'code' => $code,
      'message' => $message,
      'sent_date' => new Zend_Db_Expr('NOW()'),
      'provider' => 'facebook',
      'recipient_name' => ''
    ));
    if ($code != '') {
      $user->invites_used++;
      $user->save();
    }
    $translate = Zend_Registry::get('Zend_Translate');
    $session = new Zend_Session_Namespace('inviter');
    $session->__set('success', 2);
    $session->__set('message', $translate->_('INVITER_Invitations sucessfully have been sent to your contacts.'));
    $this->_redirect('inviter');
  }

  private
  function _facebookContacts()
  {
    $inviterApi = Engine_Api::_()->getApi('core', 'inviter');
    $facebook = Inviter_Api_Provider::getFBInstance();
    $viewer = $this->_helper->api()->user()->getViewer();

    $this->view->appId = $facebook->getAppId();
    $this->view->init_fb_app = $inviterApi->checkInitFbApp();

    $this->view->provider = 'facebook';

    if (!$this->view->appId) {
      return;
    }

    $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

    $skip = $this->_getParam('skip', false);
    $fb_user_ids = $this->_getParam('ids', false);
    $invite_code = $this->_getParam('code', '');

    if ($fb_user_ids) {
      $inviterApi->sendFacebookInvites($fb_user_ids, $invite_code);
    }

    if ($skip) {
      $this->_redirect('inviter');
    }

    if ($viewer->getIdentity() == 0) {
      $this->view->action_url = $host_url . $this->view->url(array(), 'inviter_contacts', true);
      $this->view->invite_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'signup'), 'default', true);
      return;
    }

    $invite_code = substr(md5(rand(0, 999) . $viewer->getIdentity()), 10, 7);

    $this->view->action_url = $host_url . $this->view->url(array('skip' => 1, 'code' => $invite_code), 'inviter_contacts', true);
    $this->view->invite_url = $host_url . $this->view->url(array('skip' => 1, 'module' => 'inviter', 'controller' => 'signup', 'code' => $invite_code), 'default', true);

    $this->view->exclude_ids = '';

    $fb_user_id = Inviter_Api_Provider::getFBUserId();
    if ($fb_user_id) {
      $exclude_ids = $inviterApi->getAlreadyMemberFbFriends($fb_user_id);
      $this->view->exclude_ids = ($exclude_ids) ? implode(',', $exclude_ids) : '';
    }
  }

  private
  function _page_facebookContacts($page_id)
  {
    $inviterApi = Engine_Api::_()->getApi('core', 'inviter');
    $facebook = Inviter_Api_Provider::getFBInstance();
    $viewer = $this->_helper->api()->user()->getViewer();

    $this->view->appId = $facebook->getAppId();
    $this->view->init_fb_app = $inviterApi->checkInitFbApp();

    $this->view->provider = 'facebook';

    if (!$this->view->appId) {
      return;
    }

    $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

    $skip = $this->_getParam('skip', false);
    $fb_user_ids = $this->_getParam('ids', false);
    $invite_code = $this->_getParam('code', '');

    if ($fb_user_ids) {
      $inviterApi->sendFacebookInvites($fb_user_ids, $invite_code);
    }

    if ($skip) {
      $this->_redirect('page-inviter');
    }


    $page = Engine_Api::_()->getItem('page', $page_id);
    if ($page) {
      $this->view->action_url = $host_url . $page->getHref();
      $this->view->invite_url = $host_url . $page->getHref();
    } else {
      if ($viewer->getIdentity() == 0) {
        $this->view->action_url = $host_url . $this->view->url(array('page_id' => $page_id), 'page_inviter_contacts', true);
        $this->view->invite_url = $host_url . $this->view->url(array('module' => 'inviter', 'controller' => 'signup'), 'default', true);
        return;
      }
      $invite_code = substr(md5(rand(0, 999) . $viewer->getIdentity()), 10, 7);

      $this->view->action_url = $host_url . $this->view->url(array('page_id' => $page_id), 'page_inviter_contacts', true);
      $this->view->invite_url = $host_url . $this->view->url(array('skip' => 1, 'module' => 'inviter', 'controller' => 'signup', 'code' => $invite_code), 'default', true);
    }

    $this->view->exclude_ids = '';

    $fb_user_id = Inviter_Api_Provider::getFBUserId();
    if ($fb_user_id) {
      $exclude_ids = $inviterApi->getAlreadyMemberFbFriends($fb_user_id);
      $this->view->exclude_ids = ($exclude_ids) ? implode(',', $exclude_ids) : '';
    }
  }

  public
  function referralAction()
  {
    $this->_helper->contextSwitch->addActionContext('date', 'json')->initContext();
    $code = $this->_getParam('code');
    if (!$code) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    $invites_tbl = Engine_Api::_()->getDbTable('invites', 'inviter');
    $codes_tbl = Engine_Api::_()->getDbTable('codes', 'inviter');
    $sender_id = $codes_tbl->getUserId($code);
    if (!$sender_id) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    $invite = array(
      'user_id' => $sender_id,
      'referred_date' => new Zend_Db_Expr('NOW()')
    );
    $invite_code = false;
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $invitation = array(
        'user_id' => $sender_id,
        'code' => trim($code),
        'provider' => 'facebok',
        'referred_date' => new Zend_Db_Expr('NOW()')
      );

      $invitation_id = $invites_tbl->insertReferralInvitation($invitation);

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
    }
    if (!$invitation_id) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    return $this->_helper->redirector->gotoRoute(array('module' => 'inviter', 'controller' => 'signup', 'code' => $code, 'sender' => $invitation_id), 'default', true);
    //Full texts 	invite_id 	user_id 	sender 	recipient 	code 	sent_date 	message 	new_user_id 	provider 	recipient_name 	referred_date
  }

}
