<?php
class Friendsinviter_Plugin_Signup_Invite extends Core_Plugin_FormSequence_Abstract
{

  protected $_name = 'invite';

  protected $_formClass = 'Friendsinviter_Form_Dummy';

  protected $_script = array('index/index.tpl', 'friendsinviter');

  protected $_adminFormClass = 'Friendsinviter_Form_Admin_Signup_Invite';

  protected $_adminScript = array('admin-signup/invite.tpl', 'user');

  protected $_skip;


  public function onSubmit(Zend_Controller_Request_Abstract $request)
  {

    if ($request->getParam("skip") == "skipForm" ) {
      $this->setActive(false);
      $this->onSubmitIsValid();
      $this->getSession()->skip = true;
      $this->_skip = true;
      return true;
    }
    
    
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
    if( null !== $viewRenderer && $viewRenderer->view instanceof Zend_View_Interface ) {
      $this->view = $viewRenderer->view;
    }
    




    $api = Engine_Api::_()->getApi('core', 'friendsinviter');

    $task = $request->getPost('task','');
    $is_error = 0;
    
    if ( $request->isPost() ) {
      

      // Default to show webcontacts screen
      $this->view->screen = $request->getPost('screen', 'webcontacts');
      
      // Default to show webmail box
      $this->view->provider = $request->getPost('provider', 'auto');
      
      $this->view->login = $post_login = $request->getPost('user');
      $this->view->domain = $request->getPost('domain');
      $this->view->typed_domain = $request->getPost('domain_type');
      $this->view->render_captcha = false;


      if($task == "doinvitefriends") {

        $fi_result = $api->fi_fetch_contacts( array('viewer'            => Engine_Api::_()->user()->getViewer(),
                                                    'user'              => $this->view->login,
                                                    'pass'              => $request->getPost('pass', ''),
                                                    'domain'            => $this->view->domain,
                                                    'typed_domain'      => $this->view->typed_domain,
                                                    'provider'          => $this->view->provider,
                                                    'find_friends'      => $request->getPost('find_friends', 1),
                                                    'captcha_response'  => $request->getPost('captcha_response', null),
                                                    'session'           => $request->getPost('session', null),
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

        $this->getSession()->friendsinviter_invite = array(
                                                      'invite_emails'   => $request->getPost('invite_emails', array()),
                                                      'invite_message'  => $request->getPost('invite_message', ''),
                                                      'session'         => $request->getPost('session', null),
                                                      'invite_ids'      => $request->getPost('invite_ids', null),
                                                      'social_contacts' => $request->getPost('social_contacts', 0),
                                                      'imported'        => $request->getPost('imported', null),
                                                     );

        // FINISHED
        //$this->setActive(false);
        $this->onSubmitIsValid();

        //$this->getSession()->skip = true;
        //$this->_skip = true;
        
        parent::onSubmit($request);
        return true;
      
      }


      // CHALLENGE RESPONSE
      if(($task == "challenge_response") && ($is_error != 1)) {

        $fi_result = $api->fi_challenge_response(   array(
                                                          'captcha_response'  => $request->getPost('captcha_response', array()),
                                                          'session'           => $request->getPost('session', null),
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
      
        $fi_result = $api->fi_uploadcsv( Engine_Api::_()->user()->getViewer(), $request->getPost('find_friends', 1) );
        if($fi_result['err']) {
          $is_error = 1;
          $this->view->error_message  = $fi_result['err_msg'];
        }
      
        foreach($fi_result as $key => $value) {
          $this->view->$key = $value;
        }
        
      }
      


    }






















      $this->getSession()->active = true;
      $this->onSubmitNotIsValid();
      return false;
    
      //parent::onSubmit($request);

  }

  public function onView()
  {
    
    
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
    if( null !== $viewRenderer && $viewRenderer->view instanceof Zend_View_Interface ) {
      $this->view = $viewRenderer->view;
    }
    
    $this->view->action = $this->view->url(array('module' => 'core', 'controller' => 'signup'), 'default', true);

    $api = Engine_Api::_()->getApi('core', 'friendsinviter');
    $this->view->login = '';
    $this->view->domain = '';
    $this->view->typed_domain = '';
    $this->view->provider = 'auto';
    $this->view->screen = 'webcontacts';
    $this->view->find_friends = 0;
    
    $this->view->show_skip_option = true;
    
    $this->view->render_captcha = false;
    
    // TOP/AVAILABLE DOMAINS, SERVICES
    list($this->view->domains, $this->view->services) = $api->fi_get_top_services();    

    // @todo     $this->addElement('Hash', 'token');
    
    

    
    
  }

   public function onProcess()
   {

      $invite_data = $this->getSession()->friendsinviter_invite;
      $invite_data['user'] = Engine_Api::_()->user()->getViewer();

      // @tbd captcha during signup invite
      $fi_result = Engine_Api::_()->getApi('core', 'friendsinviter')->fi_invite_contacts( $invite_data );

      
      $befriend = $this->getSession()->friendsinviter_befriend;
      if(!empty($befriend)) {
        Engine_Api::_()->getApi('core', 'friendsinviter')->fi_befriend_contacts( $befriend );
      }
          
   }


   public function onAdminProcess($form)
   {
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $step_table = Engine_Api::_()->getDbtable('signup', 'user');
    $step_row = $step_table->fetchRow($step_table->select()->where('class = ?', 'Friendsinviter_Plugin_Signup_Invite'));
    $step_row->enable = $form->getValue('enable') && ($settings->getSetting('user.signup.inviteonly') != 1);
    $step_row->save();
   }

}