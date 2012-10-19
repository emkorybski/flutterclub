<?php

class Friendsinviter_IndexController extends Core_Controller_Action_Standard
{

  public function init()
  {
    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext
      ->addActionContext('inviteresend', 'json')
      ->addActionContext('invitedelete', 'json')
      ->addActionContext('findfriends', 'json')
      ->addActionContext('getcontacts', 'json')
      ->addActionContext('hideteaser', 'json')
      ->initContext();
  }

  
  public function indexAction()
  {
    
    $api = Engine_Api::_()->getApi('core', 'friendsinviter');
    
    $settings = Engine_Api::_()->getApi('settings', 'core');
    if ($settings->getSetting('user.signup.inviteonly') == 1)
    {
      $this->_helper->requireAdmin();
    }

    $this->setupNavigation();
    

    // Make form
    $form = new Friendsinviter_Form_Invite();
    
    // Handling for users not logged in
    $this->_helper->requireUser()->isValid();

    $this->view->action = $this->view->url(array('module' => 'core', 'controller' => 'invite'), 'default', true);
    $this->view->nextStep = "";
    $this->view->show_skip_option = false;


    $this->view->login = $post_login = $this->getRequest()->getPost('user');
    $this->view->domain = $this->getRequest()->getPost('domain');
    $this->view->typed_domain = $this->getRequest()->getPost('domain_type');

    $this->view->provider = $this->getRequest()->getPost('provider', 'auto');
    $this->view->screen = $this->getRequest()->getPost('screen', 'webcontacts');
    $this->view->find_friends = 0;

    $this->view->captcha_id = '';
    $this->view->captcha_input = '';
    $this->view->render_captcha = Engine_Api::_()->getApi('settings', 'core')->core_spam_invite;

    // TOP/AVAILABLE DOMAINS, SERVICES
    list($this->view->domains, $this->view->services) = $api->fi_get_top_services();    

    $task = $this->getRequest()->getPost('task','');
    $is_error = 0;

    $this->view->invite_message = $this->getRequest()->getPost('invite_message', Zend_Registry::get('Zend_Translate')->_($settings->getSetting('invite.message', '')));

    if (Engine_Api::_()->getApi('settings', 'core')->core_spam_invite) {
    
      if($task == '') {
        $this->getSession('friendsinviter')->captcha_passed = false;
      }
  
      if ( $this->getRequest()->isPost() && !$this->getSession('friendsinviter')->captcha_passed  ) {
        
        if($form->isValid($this->getRequest()->getPost())) {
          $this->getSession('friendsinviter')->captcha_passed = true;
        } else {
          $is_error = 1;
          $this->view->error_message  = 100010152;
          $this->view->invite_emails = $this->getRequest()->getPost('invite_emails', array());
        }
        
      }
      
    }


    
    // TBD: check captcha
    if ( $this->getRequest()->isPost() && ($is_error == 0)) {

      if($task == "doinvitefriendsemail") {
        $email = $this->getRequest()->getPost('email', '');
        $email = explode('@', $email);
        $this->view->login = isset($email[0]) ? $email[0] : '';
        $this->view->typed_domain = isset($email[1]) ? $email[1] : '';
        $task = 'doinvitefriends';
      }
      


      if($task == "doinvitefriends") {

        $fi_result = $api->fi_fetch_contacts( array('viewer'            => Engine_Api::_()->user()->getViewer(),
                                                    'user'              => $this->view->login,
                                                    'pass'              => $this->getRequest()->getPost('pass', ''),
                                                    'domain'            => $this->view->domain,
                                                    'typed_domain'      => $this->view->typed_domain,
                                                    'provider'          => $this->view->provider,
                                                    'find_friends'      => $this->getRequest()->getPost('find_friends', 1),
                                                    'captcha_response'  => $this->getRequest()->getPost('captcha_response', null),
                                                    'session'           => $this->getRequest()->getPost('session', null),
                                                   )
                                            );

        if($fi_result['err'] && !$fi_result['captcha_required']) {
          $is_error = 1;
          $this->view->error_message  = $fi_result['err_msg'];
        }
        
        if($fi_result['captcha_required']) {
          $this->view->captcha_url = $fi_result['captcha_url'];
        }
        
        foreach($fi_result as $key => $value) {
          $this->view->$key = $value;
        }

      
      }

      // SEND INVITATIONS
      if(($task == "doinvite") && ($is_error != 1)) {

        $fi_result = $api->fi_invite_contacts(  array('user'            => Engine_Api::_()->user()->getViewer(),
                                                      'invite_emails'   => $this->getRequest()->getPost('invite_emails', array()),
                                                      'invite_message'  => $this->getRequest()->getPost('invite_message', ''),
                                                      'session'         => $this->getRequest()->getPost('session', null),
                                                      'invite_ids'      => $this->getRequest()->getPost('invite_ids', null),
                                                      'social_contacts' => $this->getRequest()->getPost('social_contacts', 0),
                                                      'imported'        => $this->getRequest()->getPost('imported', null),
                                                     )
                                              
                                              );
        if($fi_result['err']) {
      
          if(!$fi_result['captcha_required']) {
            $is_error = 1;
            $this->view->error_message  = $fi_result['err_msg'];
          } else {
            $this->view->captcha_url = $fi_result['captcha_url'];
            $this->view->captcha_required = true;
            $this->view->captcha_task = 'challenge_response';
            $this->view->session = $fi_result['session'];
          }
      
        } else {
          $this->view->result = 100010137;
          $invite_emails = '';
          $this->view->invite_emails = $invite_emails;
          $this->getSession('friendsinviter')->captcha_passed = false;
        }
      
      }


      // CHALLENGE RESPONSE
      if(($task == "challenge_response") && ($is_error != 1)) {

        $fi_result = $api->fi_challenge_response(   array(
                                                          'captcha_response'  => $this->getRequest()->getPost('captcha_response', array()),
                                                          'session'           => $this->getRequest()->getPost('session', null),
                                                     )
                                                 
                                                 );
        if($fi_result['err']) {
      
          if(!$fi_result['captcha_required']) {
            $is_error = 1;
            $this->view->error_message = $fi_result['err_msg'];
          } else {
            $this->view->captcha_url = $fi_result['captcha_url'];
            $this->view->captcha_required = true;
            $this->view->captcha_task = 'challenge_response';
            $this->view->session = $fi_result['session'];
          }
      
        } else {
          
          $this->view->result = 100010137;
          $invite_emails = '';
          $this->view->invite_emails = $invite_emails;
          
        }
      
      }
      
      
      
      // CSV
      if(($task == "douploadcsv") && ($is_error != 1)) {
      
        $fi_result = $api->fi_uploadcsv( Engine_Api::_()->user()->getViewer(), $this->getRequest()->getPost('find_friends', 1) );
        if($fi_result['err']) {
          $is_error = 1;
          $this->view->error_message  = $fi_result['err_msg'];
        }
      
        foreach($fi_result as $key => $value) {
          $this->view->$key = $value;
        }
        
      }

    }


    if($this->getSession('friendsinviter')->captcha_passed) {
      $this->view->render_captcha = false;
    }
      
    
    $this->view->form = $form;
    

  


  }
  
  
  
  
  
  
  public function pendingAction()
  {

    $this->_helper->requireUser->isValid();

    $api = Engine_Api::_()->getApi('core', 'friendsinviter');
    
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->setupNavigation();
    

    $table  = Engine_Api::_()->getDbTable('users', 'invite');

    $is_error = "";
    $result = "";
    
    $p = $this->getRequest()->get('p',1);
        

    $table  = Engine_Api::_()->getDbTable('invites', 'invite');

    $select = $table->select()
                    ->setIntegrityCheck(false)
                    ->from($table->info('name'), array(
                        'COUNT(DISTINCT recipient) AS count'))
                    ->where('user_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity());
                    
    $rows    = $table->fetchAll($select)->toArray();
    $invites_total = $rows[0]['count'];
    

    $invites_per_page = 50;
    $page_vars = $api->make_page($invites_total, $invites_per_page, $p);

    // @tbd - duplicate recipients - group by 
    $table  = Engine_Api::_()->getDbTable('invites', 'invite');
    $select = $table->select()
                    ->where('user_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())
                    ->order(array('timestamp DESC'))
                    ->limit($invites_per_page, $page_vars[0]);

    $invites = $table->fetchAll($select);
    
    $known_invites = array();
    $filtered_invites = array();
    foreach($invites as $key => $invite) {
      if(in_array($invite->recipient, $known_invites)) {
        continue;
      }
      $known_invites[] = $invite->recipient;
      $filtered_invites[] = $invite;
    }
    
    $invites = $filtered_invites;


    $this->view->invites = $invites;
    $this->view->invites_total = $invites_total;
    $this->view->p = $page_vars[1];
    $this->view->maxpage = $page_vars[2];
    $this->view->p_start = $page_vars[0]+1;
    $this->view->p_end = $page_vars[0]+count($invites);
    
    
  }




  public function statsAction()
  {

    $this->_helper->requireUser->isValid();

    $this->setupNavigation();

    $table = Engine_Api::_()->getDbTable('stats', 'friendsinviter');

    $select = $table->select()
                    ->where('user_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity());
                    
    $invites = $table->fetchRow($select);

    $invites_sent = !empty($invites) ? $invites->invites_sent : 0;
    $invites_converted = !empty($invites) ? $invites->invites_converted : 0;
    
    // ASSIGN VARIABLES AND INCLUDE FOOTER
    $this->view->invites_sent = $invites_sent;
    $this->view->invites_converted = $invites_converted;
    
  }  


  public function unsubscribeAction()
  {

    $email = strtolower( $this->_getParam('email') );
    $task = $this->_getParam('task');
    $api = Engine_Api::_()->getApi('core', 'friendsinviter');
    
    if($task == 'unsubscribe') {
      
      if(!$api->is_email_address( $email ) ) {
        
        $this->view->error_message = 100010272;
        
      } else {
  
        $table  = Engine_Api::_()->getDbTable('unsubscribes', 'friendsinviter');
        $select = $table->select()
                        ->where('unsubscribe_user_email = ?', $email);
    
        $rows =  $table->fetchAll($select);
        
        
        if(count($rows) == 0) {
  
          $table->insert(array(
            'unsubscribe_user_email' => $email,
          ));
  
          // Cleanup possibly queued emails
          // @todo
          //$database->database_query("DELETE FROM se_semods_email_queue WHERE to_email = '$email' AND type = 10");
        }
        
        $email = '';
        $this->view->result = 100010273;
        $this->view->hide_unsubscribe_form = true;
      }
      
    }
    
  }  


  public function reflinkAction()
  {

    $this->_helper->requireUser->isValid();

    $this->setupNavigation();

    $viewer = Engine_Api::_()->user()->getViewer();
    //$referrer_id = $viewer->username;
    $referrer_id = $viewer->user_id;


    $invite_url     = "http://{$_SERVER['HTTP_HOST']}"
                    . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                          'module'     => 'core',
                          'controller' => 'signup',
                          //'action' => null,
                          //'ref'     => $referrer_id), 'default');
                          ), 'default') . '?ref=' . $referrer_id;

    
    $this->view->reflink = $invite_url;

    
    
  }
  
  
  public function getNavigation() {

    $links = array( 
                array(
                      'label'      => 'Invite Friends',
                      'route'      => 'invite',
                      'action'     => 'index',
                      'controller' => 'index',
                      'module'     => 'friendsinviter'
                    ),
                array(
                      'label'      => 'Pending Invites',
                      'route'      => 'friendsinviter_pending',
                      'action'     => 'pending',
                      'controller' => 'index',
                      'module'     => 'friendsinviter'
                     ),
                array(
                      'label'      => 'Statistics',
                      'route'      => 'friendsinviter_stats',
                      'action'     => 'stats',
                      'controller' => 'index',
                      'module'     => 'friendsinviter'
                     ),
                array(
                      'label'      => 'My Referral Link',
                      'route'      => 'friendsinviter_reflink',
                      'action'     => 'reflink',
                      'controller' => 'index',
                      'module'     => 'friendsinviter'
                     ),
                );
    
    return $links;
    
  }
  
  public function setupNavigation() {

    $links = $this->getNavigation();

    $this->view->navigation = new Zend_Navigation();
    $this->view->navigation->addPages($links);

    
  }
















  public function inviteresendAction()
  {

    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = 'AUTH_FAIL';
      return;
    }


    $settings = Engine_Api::_()->getApi('settings', 'core');
    $api = Engine_Api::_()->getApi('core', 'friendsinviter');

    $inviteid = intval($this->_getParam('id'));

    $table = Engine_Api::_()->getDbtable('invites', 'invite');
    $select = $table->select()
          ->where('id = ?', $inviteid)
          ->where('user_id = ?', $viewer->getIdentity());
          
    $invite_data = $table->fetchRow($select);
    
    if(empty($invite_data)) {
      return;
    }
    

    if($settings->getSetting('user.signup.inviteonly') == 0) {
      $api->fi_send_invitation($viewer, $invite_data->recipient, $invite_data->message, true);
    } else {
      $api->fi_send_invitecode($viewer, $invite_data->recipient, $invite_data->message, true);
    }
    
  }
  
  
  public function invitedeleteAction()
  {

    $table = Engine_Api::_()->getDbtable('invites', 'invite');

    // Check viewer
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = 'AUTH_FAIL';
      return;
    }

    $id = $this->_getParam('id');
    
    $table = Engine_Api::_()->getDbtable('invites', 'invite');
    $table->delete(array(
      'id = ?' => $id,
      'user_id = ?' => $viewer->getIdentity()
    ));
    
  }
  

  public function findfriendsAction()
  {

    // Check viewer
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$viewer->getIdentity() ) {
      
      // if this is done via signup - save
      $session = new Zend_Session_Namespace('Friendsinviter_Plugin_Signup_Invite');
      $session->friendsinviter_befriend = $this->_getParam('befriend');
      
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = 'AUTH_FAIL';
      return;
    }
    
    $befriend = $this->_getParam('befriend');
    $friendships_count = Engine_Api::_()->getApi('core', 'friendsinviter')->fi_befriend_contacts( $befriend );

    
    echo  $friendships_count;
    
  }


  public function hideteaserAction() {
    
    $viewer = Engine_Api::_()->user()->getViewer();

    $table  = Engine_Api::_()->getDbTable('teasersettings', 'friendsinviter')->setEnabled($viewer, 0);
    
  }




  public function getcontactsAction() {

    $response = array('status'    => 0,
                     );


    $fi_username = $this->_getParam('fi_username','');
    $fi_password = $this->_getParam('fi_password','');
  
    $fi_domain = $this->_getParam('fi_domain','');
    $fi_typed_domain = $this->_getParam('fi_domain_type','');
    $fi_provider = $this->_getParam('fi_provider', 'auto');
  
    $fi_find_friends = (int) $this->_getParam('fi_find_friends', 0);
    $fi_captcha_response = $this->_getParam('fi_captcha_response');
    $fi_session = $this->_getParam('fi_session');

    $fi_captcha_required = false;
    $fi_social_contacts = null;
    $fi_session = '';
    $fi_importing = false;
    $fi_contacts = null;
    $fi_friends = null;
    $fi_found_friends = 0;
    $fi_unfound_friends = 0;
  
    if(empty($fi_session)) {
      $fi_session = null;
    }
  
    if(empty($fi_captcha_response)) {
      $fi_captcha_response = null;
    }
    
    $api = Engine_Api::_()->getApi('core', 'friendsinviter');
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $fi_import_result = $api->fi_import_contacts($fi_username, $fi_password, $fi_domain, $fi_typed_domain, $fi_provider, $fi_find_friends, $viewer, $fi_session, $fi_captcha_response);
  
    $is_error = 0;
    $error_message = '';
  
    $fi_captcha_required = false;
    $fi_captcha_url = '';
    
    if(isset($fi_import_result['error_code']) || isset($fi_import_result['error_message'])) {
  
      if(isset($fi_import_result['captcha_url'])) {
        $fi_captcha_url = $fi_import_result['captcha_url'];
        $fi_captcha_required = true;
        $fi_session = $fi_import_result['session'];
      }
  
      $is_error = 1;
      $error_message = $fi_import_result['error_message'];
  
    } else {
  
      $fi_importing = true;
      $fi_contacts = $fi_import_result['contacts'];
      $fi_unfound_friends = count($fi_contacts);
      $fi_friends = isset($fi_import_result['friends']) ? $fi_import_result['friends'] : array();
      $fi_found_friends = count($fi_friends);
      $fi_social_contacts = isset($fi_import_result['social_contacts']) ? $fi_import_result['social_contacts'] : array();
      $fi_session = $fi_import_result['session'];
  
      }
  
    $response['captcha_required'] = $fi_captcha_required;
    $response['social_contacts'] = $fi_social_contacts;
    $response['session'] = $fi_session;
    $response['importing'] = $fi_importing;
    $response['contacts'] = $fi_contacts;
    $response['friends'] = $fi_friends;
    $response['found_friends'] = $fi_found_friends;
    $response['unfound_friends'] = $fi_unfound_friends;
  
    $response['status'] = $is_error;
    $response['err_msg'] = Zend_Registry::get('Zend_Translate')->_($error_message);
    $response['captcha_required'] = $fi_captcha_required;
    $response['captcha_url'] = $fi_captcha_url;
    
    foreach($response as $key => $val) {
      $this->view->$key = $val;
    }
    
  }
  
}
