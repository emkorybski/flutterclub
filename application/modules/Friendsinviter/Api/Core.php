<?php

global $contacts_importer_errors;

$contacts_importer_errors[1] = 100010011;
//$contacts_importer_errors[5] = "Invalid API key";
$contacts_importer_errors[8] = 100010012;
$contacts_importer_errors[9] = 100010013;
$contacts_importer_errors[100] = 100010014;
$contacts_importer_errors[101] = 100010015;
$contacts_importer_errors[102] = 100010016;
$contacts_importer_errors[103] = 100010017;
$contacts_importer_errors[104] = 100010018;
$contacts_importer_errors[105] = 100010019;
$contacts_importer_errors[107] = 100010020;
$contacts_importer_errors[1000] = 100010021;


class Friendsinviter_Api_Core extends Core_Api_Abstract
{
  protected $_name = 'users';

 
  function fi_check_patterns( $email, $patterns ) {
    foreach($patterns as $pattern) {
      if(preg_match('/'.$pattern.'/i', $email)) {
        return true;
      }
    }
    return false;
  }


  public function fi_get_top_services() {

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $domains = explode(',', $settings->getSetting('friendsinviter.invite_topdomains','') );
    $services = $settings->getSetting('friendsinviter.invite_topnetworks','a:0:{}');
      
    return array( $domains, $services );
  }
  
  
  
  
  public function fi_get_api_params() {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    
    $api_key = $settings->getSetting('friendsinviter.invite_api_key','');
    $secret = $settings->getSetting('friendsinviter.invite_secret','');
    
    return ( !empty($api_key) && !empty($secret) ) ? array( 'api_key' => $api_key, 'secret' => $secret ) : null;
  }



  function fi_fetch_contacts( $params = array() ) {
  
    $user = isset($params['viewer']) ? $params['viewer'] : '';
    $login = isset($params['user']) ? $params['user'] : '';
    $password = isset($params['pass']) ? $params['pass'] : '';
    $domain = isset($params['domain']) ? $params['domain'] : '';
    $typed_domain = isset($params['typed_domain']) ? $params['typed_domain'] : '';
    $provider = isset($params['provider']) ? $params['provider'] : 'auto';
    $find_friends = isset($params['find_friends']) ? $params['find_friends'] : 0;
  

    $captcha_response = isset($params['captcha_response']) ? $params['captcha_response'] : null;
    $session = isset($params['session']) ? $params['session'] : null;

    $import_result = $this->fi_import_contacts($login, $password, $domain, $typed_domain, $provider, $find_friends, $user, $session, $captcha_response);
  
    $is_error = 0;
    $error_message = '';
    $captcha_required = false;
    $importing = false;
  
    if(isset($import_result['error_code']) || isset($import_result['error_message'])) {

      if(isset($import_result['captcha_url'])) {
        
        $returns['captcha_url'] = $import_result['captcha_url'];
        $returns['captcha_required'] = true;
        $session = $import_result['session'];
        $captcha_required = true;
      }

      $is_error = 1;

      $error_message = $import_result['error_message'];

    } else {

      $importing = true;
      $returns['contacts'] = $contacts = isset($import_result['contacts']) ? $import_result['contacts'] : array();
      $returns['unfound_friends'] = count($contacts);
      $returns['friends'] = $friends = isset($import_result['friends']) ? $import_result['friends'] : array();
      $returns['found_friends'] = count($friends);
      $returns['social_contacts'] = isset($import_result['social_contacts']) ? $import_result['social_contacts'] : false;
      $session = $import_result['session'];

    }

    $returns['captcha_required'] = $captcha_required;
    $returns['session'] = $session;
    $returns['importing'] = $importing;
    $returns['domain'] = $domain;
    $returns['typed_domain'] = $typed_domain;
    $returns['login'] = $login;


    $returns['err'] = $is_error;
    $returns['err_msg'] = $error_message;
    $returns['captcha_required'] = $captcha_required;
    
    return $returns;
  
  }



  function fi_import_contacts($login, $password, $domain, $typed_domain, $provider, $findfriends = false, $friends_with = 0, $session = null, $captcha_response = null) {
    global $contacts_importer_errors;
  
    $result = array();
    $contacts = array();
    $friends = array();
  
    for(;;) {
  
      $api_params = $this->fi_get_api_params();
      if(!$api_params || empty($api_params['api_key']) || empty($api_params['secret']) ) {
        $error_message = 100010005;
        break;
      }
  
      if(is_null($captcha_response)) {
  
        $actual_domain = empty($typed_domain) ? $domain : $typed_domain;
  
        if(empty($login) || empty($password) || (($provider == 'auto') && empty($actual_domain)) ) {
          $error_message = 100010007;
          break;
        }
  
      if(!empty($actual_domain))
          $login = $login . '@' . $actual_domain;
      }
      
      include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "contactsImporter.php";
  
      $importer = new ContactsImporter($api_params['api_key'], $api_params['secret']);
      $contacts = $importer->getContacts($login, $password, $provider, 1, $session, $captcha_response);
      
      $session = $importer->session;

      if($contacts === false) {
        $error_code = $importer->getErrorCode();

        $error_message = isset($contacts_importer_errors[$error_code]) ? $contacts_importer_errors[$error_code] : $contacts_importer_errors[1];
  
        // captcha
        if( $error_code == $importer->API_E_CAPTCHA_REQUIRED ) {
          $result['captcha_url'] = $importer->captcha_url;
        }
  
        break;
      }
  
      if(count($contacts) == 0) {
        $error_message = 100010006;
        break;
      }
  
  
      // SocialContacts don't have email
      $social_contacts = false;
      
      foreach($contacts as $contact) {
        if((isset($contact['pic_square'])) && ($contact['pic_square'] != '')) {
          $social_contacts = true;
          break;
        }
        if((!isset($contact['email'])) || ($contact['email'] == '')) {
          $social_contacts = true;
          break;
        }
      }
  
      // SocialContacts don't have email
      //if(isset($contacts[0]['email']) && $contacts[0]['email'] != '') {
  
        //$social_contacts = false;
  
      // filter folks already registered, by email
        if(!$findfriends) {
          $ff_error_reporting = error_reporting( E_ALL ^ E_NOTICE ^ E_WARNING );
          $unfound_friends = $this->filter_registered_emails($contacts, true);
          error_reporting( $ff_error_reporting );
          if($unfound_friends == 0) {
            $error_message = 100010008;
            break;
          }
        } else {
          $unfound_friends = $this->filter_registered_emails($contacts, true, $friends, $friends_with);
        }
  
      //} else {
      //  $social_contacts = true;
      //  $unfound_friends = count($contacts);
      //}
  
      // UPDATE STATISTICS LESS 'FOUND FRIENDS'
      $this->fi_update_stats( "imported_contacts", $unfound_friends );
  
      break;
    }
 
    isset($error_message) ? $result['error_message'] = $error_message : 0;
    isset($error_code) ? $result['error_code'] = $error_code : 0;
    !empty($contacts) ? $result['contacts'] = $contacts : 0;
    !empty($friends) ? $result['friends'] = $friends : 0;
    !empty($social_contacts) ? $result['social_contacts'] = $social_contacts : 0;
  
    $result['session'] = $session;
  
    return $result;
  }



  function filter_registered_emails(&$emails, $emails_are_contacts = false, &$friends, $friends_with ) {
    
  
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $excluded_emails = ($settings->getSetting('friendsinviter.invite_filteremails','') == '') ? array() : explode(',', $settings->getSetting('friendsinviter.invite_filteremails',''));
  
    $emails_for_query = array();
    $friends_splice = array();
    $ids_for_query = array();
    $friends_ids_splice = array();
    $social = Engine_Api::_()->getDbTable('modules','core')->hasModule('socialdna');
  
    if(!is_array($emails)) {
      $emails = explode(',', $emails);
    }
  
    if($emails_are_contacts) {
      $contacts_new = array();
      foreach($emails as $contact) {
        if(isset($contact['email']) && ($contact['email'] != '')) {
        $contact['email'] = trim(strtolower($contact['email']));
        if(($this->is_email_address($contact['email'])) && !$this->fi_check_patterns($contact['email'], $excluded_emails)) {
          $emails_for_query[] = "'" . $contact['email'] . "'";
          $contacts_new[] = $contact;
        }
        } else {
            $ids_for_query[] = "'" . $contact['uid'] . "'";
            $contacts_new[] = $contact;
        }
      }
      $emails = $contacts_new;
    } else {
      foreach($emails as $key => $email) {
        $email = trim(strtolower($email));
        $emails[$key] = $email;
        if(($this->is_email_address($email)) && !$this->fi_check_patterns($email, $excluded_emails)) {
          $emails_for_query[] = "'" . $email . "'";
          $clean_emails[] = $email;
        }
      }
    }

    if((count($emails_for_query) == 0) && (!$social || (count($ids_for_query) == 0))) {
      $emails = array();
      return 0;
    }
  
  
  
    if(count($emails_for_query) != 0) {
  
    $emails_for_query_string = implode(",", $emails_for_query);
  
    // CHECK FOR UNSUBSCRIBED EMAILS

    $table  = Engine_Api::_()->getDbTable('unsubscribes', 'friendsinviter');
    $select = $table->select()
                    ->where("unsubscribe_user_email IN ($emails_for_query_string)");
                    //->where('unsubscribe_user_email IN (?)', $emails_for_query_string);
                    
    $rows =  $table->fetchAll($select);                   

    if(count($rows) > 0) {
      foreach($rows as $row) {
        $filtered_emails[] = "'" . $row->unsubscribe_user_email . "'";
        $clean_emails_filter[] = $row->unsubscribe_user_email;
      }
    
      // FILTER EMAILS
      if($emails_are_contacts) {
          // dilute contacts
          // would be better to use array_diff, but structure is incompatible
          $contacts_diluted = array();
          foreach($emails as $contact) {
              if(isset($contact['email']) && ($contact['email'] != '')) {
                if(!in_array($contact['email'], $clean_emails_filter) && $this->is_email_address($contact['email'])) {
                  $contacts_diluted[] = $contact;
                }
              } else {
              $contacts_diluted[] = $contact;
          }
            }
          $emails = $contacts_diluted;
      } else {
          $emails = array_diff($clean_emails, $clean_emails_filter);
          $clean_emails = $emails;
      }
    
    
      $emails_for_query = array_diff( $emails_for_query, $filtered_emails );
      $emails_for_query_string = implode(",", $emails_for_query);
    }
    
    }

    
    if((count($emails_for_query) == 0) && (!$social || (count($ids_for_query) == 0))) {
      $emails = array();
      return 0;
    }
    
  
  if(!is_null($friends)) {
    if($friends_with) {
      $subselect_table  = Engine_Api::_()->getDbTable('membership', 'user');

      $subselect = $subselect_table->select()
                    ->from($subselect_table->info('name'), 'user_id')
                    ->where('resource_id = ?', $friends_with->getIdentity());
                    
      $table = Engine_Api::_()->getItemTable('user');

      $dbr_friends = $table->select()
                    //->where("user_id NOT IN (?)",$subselect->__toString())
                    ->where("user_id NOT IN (?)",$subselect)
                    ->where("email IN ($emails_for_query_string)")
                    ->where("user_id != {$friends_with->getIdentity()}");
      
      $dbr_friends = $table->fetchAll($dbr_friends);
      
    } else {

      $table  = Engine_Api::_()->getDbTable('users', 'friendsinviter');
      $dbr_friends = $table->select()
                    ->where("email IN ($emails_for_query_string)")
                    ->fetchAll();

    }

    $found_friends = count($dbr_friends);

    if($found_friends > 0 ) {

      $friends = array();
      foreach($dbr_friends as $row) {
        $friends[] = $row;
        $friends_splice[] = $row->email;
      }
    }

    
    if($social && !empty($ids_for_query)) {

      if($friends_with) {

        $ids_for_query_string = implode(",", $ids_for_query);
        
        $subselect_table  = Engine_Api::_()->getDbTable('membership', 'user');
  
        $subselect = $subselect_table->select()
                      ->from($subselect_table->info('name'), 'user_id')
                      ->where('resource_id = ?', $friends_with->getIdentity());
                      
        $table = Engine_Api::_()->getItemTable('user');
        $tableName = $table->info('name');
        $socialTable = Engine_Api::_()->getDbTable('users', 'socialdna');
        $socialTableName = $socialTable->info('name');
  
        $dbr_friends = $table->select()
                      ->setIntegrityCheck(false)
                      ->from($tableName,'*')
                      ->join($socialTableName, "$socialTableName.openid_user_id = $tableName.user_id",'*')
                      //->where("user_id NOT IN (?)",$subselect->__toString())
                      ->where("$tableName.user_id NOT IN (?)",$subselect)
                      //->where("$tableName.email NOT IN ($emails_for_query_string)")
                      ->where("$tableName.user_id != {$friends_with->getIdentity()}")
                      ->where("$socialTableName.openid_user_key IN ($ids_for_query_string)");
                      //openid_service_id

        if(count($emails_for_query) != 0) {
          $dbr_friends->where("$tableName.email NOT IN ($emails_for_query_string)");
        }
        
        $dbr_friends = $table->fetchAll($dbr_friends);

        if(count($dbr_friends) > 0 ) {
          
          $found_friends += count($dbr_friends);
    
          if(!is_array($friends)) {
            $friends = array();
          }
          foreach($dbr_friends as $row) {
            $friends[] = $row;
            $friends_ids_splice[] = $row->openid_user_key;
          }
        }
        
      }
      
    }

  } else {

      $table  = Engine_Api::_()->getDbTable('users', 'friendsinviter');
      $dbr_friends = $table->select()
                    ->from($table->info('name'), array('GROUP_CONCAT(email) as emails'))
                    ->where("email IN ($emails_for_query_string)");
                    
      $dbr_friends = $table->fetchRow($dbr_friends);

      if(!empty($dbr_friends->emails)) {
        $friends_splice = explode(',', $dbr_friends->emails);
      }
                    
  }

    // filter out users that are already registered
    // this is duplicate code block, but it's better to duplicate code then run unnecessary query
    if(!is_null($friends) && $friends_with) {
      $friends_splice = array();

      $table  = Engine_Api::_()->getDbTable('users', 'friendsinviter');
      $dbr_friends = $table->select()
                    ->from($table->info('name'), 'GROUP_CONCAT(email) as emails')
                    ->where("email IN ($emails_for_query_string)");
                    
      $dbr_friends = $table->fetchRow($dbr_friends);

      if(!empty($dbr_friends->emails)) {
        $friends_splice = explode(',', $dbr_friends->emails);
      }
      
      if($social && (count($ids_for_query) > 0)) {
        $socialTable = Engine_Api::_()->getDbTable('users', 'socialdna');

        $dbr_friends = $socialTable->select()
                      ->from($socialTable->info('name'),'GROUP_CONCAT(openid_user_key) as openid_user_keys')
                      ->where("openid_user_key IN ($ids_for_query_string)");
                      //openid_service_id

        $dbr_friends = $socialTable->fetchRow($dbr_friends);
  
        if(!empty($dbr_friends->openid_user_keys)) {
          $friends_ids_splice = explode(',', $dbr_friends->openid_user_keys);
        }
         
      }

    }

    if($emails_are_contacts) {

      // dilute contacts
      // would be better to use array_diff, but structure is incompatible
      $contacts_diluted = array();

      foreach($emails as $contact) {

        if(isset($contact['email']) && ($contact['email'] != '')) {

          if(!in_array($contact['email'], $friends_splice)) {
          $contacts_diluted[] = $contact;
      }

        } else {

          if(!in_array($contact['uid'], $friends_ids_splice)) {
            $contacts_diluted[] = $contact;
          }
          
        }
        
      }

      $emails = $contacts_diluted;

    } else {

      $emails = array_diff($clean_emails, $friends_splice);

    }

    return count($emails);
  }





  function fi_update_stats($type, $amount = 1) {
    
    Engine_Api::_()->getDbtable('statistics', 'core')->increment('friendsinviter.' . $type, $amount);

  }



  function fi_invite_contacts( $params ) {

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $invite_emails = isset($params['invite_emails']) ? $params['invite_emails'] : '';
    $invite_message = isset($params['invite_message']) ? $params['invite_message'] : '';
    $session = isset($params['session']) ? $params['session'] : null;
    $invite_ids = isset($params['invite_ids']) ? $params['invite_ids'] : null;
    $social_contacts = isset($params['social_contacts']) ? $params['social_contacts'] : 0;
    $imported = isset($params['imported']) ? $params['imported'] : null;
    $user = $params['user'];
    
    $invite_ids = !empty($invite_ids) ? $invite_ids : array();
    $invite_emails = !empty($invite_emails) ? $invite_emails : array();
  
    if(!is_array($invite_emails)) {
      $invite_emails = explode(',', $invite_emails);
    }

    if(!is_array($invite_ids)) {
      $invite_ids = explode(',', $invite_ids);
    }
    
    // see if some of them are emails
    if($social_contacts) {

      foreach($invite_ids as $key => $invite_id) {
        if($this->is_email_address($invite_id)) {
          unset($invite_ids[$key]);
          $invite_emails[] = $invite_id;
        }
      }
    }
  
    $invited_count = 0;
    
    //if(!$social_contacts) {
    if(!empty($invite_emails)) {
      $ff_error_reporting = error_reporting( E_ALL ^ E_NOTICE ^ E_WARNING );
      $this->filter_registered_emails( $invite_emails );
      error_reporting( $ff_error_reporting );
      $invited_count = count($invite_emails);
    }
    //  else {
    //  $invited_count = count($invite_ids);
    //}
    
    if(!empty($invite_ids)) {
      $invited_count += count($invite_ids);
    }
  
    $is_error = 0;
    $error_message = '';
    $captcha_required = false;
  
    //if( (!$social_contacts && empty($invite_emails)) || ($social_contacts && empty($invite_ids)) ) {
    if( empty($invite_emails) && empty($invite_ids) ) {
  
        $is_error = 1;
        $error_message = 100010234;
  
    } else {

  
      // STATS FOR IMPORTED CONTACTS VS ACTUALLY INVITED ONES
      if($imported || $social_contacts) {
        $this->fi_update_stats("invited_contacts", $invited_count);
      }
  
      if(!empty($invite_emails)) {

        if($settings->getSetting('user.signup.inviteonly') == 0) {
          $this->fi_send_invitation($user, $invite_emails, $invite_message);
        } else {
          $this->fi_send_invitecode($user, $invite_emails, $invite_message);
        }
        
        // done with emails
        $invite_emails = '';
      }
      
      if(!empty($invite_ids)) {
  
        if($settings->getSetting('user.signup.inviteonly') == 0) {
          $result = $this->fi_send_social_invitation($user, $invite_ids, $invite_message, $session);
        } else {
          $result = $this->fi_send_social_invitecode($user, $invite_ids, $invite_message, $session);
        }
        
        if(isset($result['captcha_url'])) {

          $returns['captcha_url'] = $result['captcha_url'];
          $returns['captcha_required'] = true;
          $returns['captcha_task'] = 'challenge_response';
          $returns['session'] = $session;
          $captcha_required = true;
  
          $is_error = 1;
          $error_message = $result['err_msg'];
        }
  
        }
  
      //if($social_contacts) {
      //
      //  if($settings->getSetting('user.signup.inviteonly') == 0) {
      //    $result = $this->fi_send_social_invitation($user, $invite_ids, $invite_message, $session);
      //  } else {
      //    $result = $this->fi_send_social_invitecode($user, $invite_ids, $invite_message, $session);
      //  }
      //  
      //  if(isset($result['captcha_url'])) {
      //
      //    $returns['captcha_url'] = $result['captcha_url'];
      //    $returns['captcha_required'] = true;
      //    $returns['captcha_task'] = 'challenge_response';
      //    $returns['session'] = $session;
      //    $captcha_required = true;
      //
      //    $is_error = 1;
      //    $error_message = $result['err_msg'];
      //  }
      //
      //} else {
      //
      //  if($settings->getSetting('user.signup.inviteonly') == 0) {
      //    $this->fi_send_invitation($user, $invite_emails, $invite_message);
      //  } else {
      //    $this->fi_send_invitecode($user, $invite_emails, $invite_message);
      //  }
      //
      //}
  
    }
  
    // push back in case of error
    
    $returns['invite_message'] = $invite_message;
    $returns['invite_emails'] = $invite_emails;
  
    $returns['err'] = $is_error;
    $returns['err_msg'] = $error_message;
    $returns['captcha_required'] = $captcha_required;

    return $returns;
  
  }



  function fi_challenge_response( $params ) {
    global $contacts_importer_errors;

    $captcha_response = isset($params['captcha_response']) ? $params['captcha_response'] : null;
    $session = isset($params['session']) ? $params['session'] : null;
  
    $api_params = $this->fi_get_api_params();
  
    include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "contactsImporter.php";
  
    $importer = new ContactsImporter($api_params['api_key'], $api_params['secret']);
  
    $returns['err'] = 0;
    $returns['err_msg'] = '';
    
    $response = $importer->inviteContacts($session, '', '', '', 0, $captcha_response);
  
    if($response === false) {
  
      $error_code = $importer->getErrorCode();
      $error_message = isset($contacts_importer_errors[$error_code]) ? $contacts_importer_errors[$error_code] : $contacts_importer_errors[1];
  
      // captcha
      if( $error_code == $importer->API_E_CAPTCHA_REQUIRED ) {
      
        $returns['captcha_url'] = $importer->captcha_url;
        $returns['captcha_required'] = true;
        $returns['captcha_task'] = 'challenge_response';
        $returns['session'] =  $session;
        $captcha_required = true;
  
        $returns['err'] = 1;
        $returns['err_msg'] = $error_message;
        $returns['captcha_url'] = $importer->captcha_url;
      }
    }
  
    return $returns;
  
  }


  function fi_uploadcsv( $user, $find_friends = true ) {

    $settings = Engine_Api::_()->getApi('settings', 'core');
  
    $excluded_emails = ($settings->getSetting('friendsinviter.invite_filteremails') == '') ? array() : explode(',', $settings->getSetting('friendsinviter.invite_filteremails'));
  
    $contacts = array();
    $friends = array();
  
    $is_error = 0;
    $error_message = '';
  
    $importing = false;
    $unfound_friends = 0;
    $friends = array();
    $found_friends = 0;
  
    $social_contacts = 0;
    $session = '';
    $emails = array();
    
    for(;;) {
      
      $uploaded_file = $_FILES['csvfile']['tmp_name'];
  
      if(is_uploaded_file($uploaded_file)) {
  
        $fh = fopen($uploaded_file, "r");
        while( ($row = fgetcsv($fh, 1024, ',')) != false ) {
          foreach($row as $value) {
            if(($this->is_email_address($value)) && !$this->fi_check_patterns($value, $excluded_emails)) {
              $emails[] = $value;
            }
          }
        }
        fclose($fh);
      }
      
      if(empty($emails)) {
        $is_error = 1;
        $error_message = 100010006;
        break;
      }
    

      $emails = array_map("strtolower", $emails);
      $emails = array_unique($emails);
      sort($emails, SORT_STRING);

      foreach($emails as $email) {
        $contacts[] = array('email' => $email,
                            'name'  => ''
                           );
      }
    
      // filter folks already registered, by email
      if(!$find_friends) {
        
        $ff_error_reporting = error_reporting( E_ALL ^ E_NOTICE ^ E_WARNING );
        $unfound_friends = $this->filter_registered_emails($contacts, true);
        error_reporting( $ff_error_reporting );
        if($unfound_friends == 0) {
          $is_error = 1;
          $error_message = 100010008;
          break;
        }
        
      } else {
  
        $unfound_friends = $this->filter_registered_emails($contacts, true, $friends, $user);
        
      }
  
      $importing = true;
      $found_friends = count($friends);
      
      break;
    
    }


    $returns['social_contacts'] = $social_contacts;
    $returns['session'] = $session;
    $returns['importing'] = $importing;
    $returns['contacts'] = $contacts;
    $returns['friends'] = $friends;
    $returns['found_friends'] = $found_friends;
    $returns['unfound_friends'] = $unfound_friends;

    $returns['err'] = $is_error;
    $returns['err_msg'] = $error_message;

    return $returns;
  
  }


  function fi_send_invitation($user, $invite_emails, $invite_message = "", $resend = false) {

    $invites_count = $this->fi_preprocess_invitation($user, $invite_emails, null, $resend);
    
    $settings    = Engine_Api::_()->getApi('settings', 'core');

    $translate   = Zend_Registry::get('Zend_Translate');

    $recipients  = $invite_emails;
    
    // Check recipients
    if( is_string($recipients) ) {
      $recipients = preg_split("/[\s,]+/", $recipients);
    }
    if( is_array($recipients) ) {
      $recipients = array_map('strtolower', array_unique(array_filter(array_map('trim', $recipients))));
    }
    if( !is_array($recipients) || empty($recipients) ) {
      return 0;
    }


    $message     = $invite_message;
    $message     = trim($message);
    $emailsSent = 0;
    $inviteOnlySetting = $settings->getSetting('user.signup.inviteonly', 0);

      // Initiate objects to be used below
      $table       = Engine_Api::_()->getDbtable('invites', 'invite');

    // Iterate through each recipient

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {

      foreach ($recipients as $recipient) {
          
          do {
            $invite_code  = substr(md5(rand(0,999).$recipient), 10, 7);
        } while( null !== $table->fetchRow(array('code = ?' => $invite_code)) );

        $invite_url     =  ( _ENGINE_SSL ? 'https://' : 'http://' )
                        . $_SERVER['HTTP_HOST']
                          . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                              'module'     => 'invite',
                                'controller' => 'signup',
                              ), 'default', true)
                        . '?'
                        . http_build_query(array('ref' => $user->getIdentity(), 'email' => $recipient, 'code' => $invite_code));
          
        $unsubscribe_link = ( _ENGINE_SSL ? 'https://' : 'http://' )
                        . $_SERVER['HTTP_HOST']
                          . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                                'module'      => 'core',
                                'controller'  => 'invite',
                                'action'      => 'unsubscribe',
                              ), 'default', true);
                          
                          
          // insert the invite into the database
            $row = $table->createRow();
            $row->user_id   = $user->getIdentity();
            $row->recipient = $recipient;
            $row->code      = $invite_code;
        //$row->timestamp = date('Y-m-d H:i:s');
        $row->timestamp = new Zend_Db_Expr('NOW()');
            $row->message   = $message;
            $row->save();

        try {
    
          // Send mail
  
          $mailType = ( $inviteOnlySetting == 2 ? 'invite_code' : 'invite' );
    
            $mail_settings =   array(
  
                'host'          => $_SERVER['HTTP_HOST'],
                   'email' => $recipient,
                'date'          => time(),
                'sender_email'  => $user->email,
                'sender_title'  => $user->getTitle(),
                'sender_link'   => $user->getHref(),
                'sender_photo'  => $user->getPhotoUrl('thumb.icon'),
                   'message' => $message,
                'object_link'   => $invite_url,
                'code'          => $invite_code,
                   'unsubscribe_link' => $unsubscribe_link
            
                   );
  
              Engine_Api::_()->getApi('mail', 'core')->sendSystem(
                $recipient,
            $mailType,
                 $mail_settings 
              );
          
  
        } catch( Exception $e ) {
          
          // Silence
          if( APPLICATION_ENV == 'development' ) {
            throw $e;
            }
          continue;
          
            }

        $emailsSent++;

      }
    
      $user->invites_used += $emailsSent;
      $user->save();
            
            $db->commit();

    } catch( Exception $e ) {

            $db->rollBack();
      if( APPLICATION_ENV == 'development' ) {
        throw $e;
      }
          }
    
    return $emailsSent;    
    
  }

  function fi_send_social_invitation($user, $invite_ids, $invite_message = "", $session) {
    
    global $contacts_importer_errors;

    $invites_count = $this->fi_preprocess_invitation($user, $invite_ids);

    if($invites_count == 0) {
      return;
    }

    $invite_ids = implode(',', $invite_ids);
    

    $settings    = Engine_Api::_()->getApi('settings', 'core');
    $translate   = Zend_Registry::get('Zend_Translate');

    $message     = $invite_message;
    $message     = trim($message);
    $inviteOnlySetting = $settings->getSetting('user.signup.inviteonly', 0);

    // Initiate objects to be used below
    $table       = Engine_Api::_()->getDbtable('invites', 'invite');

    do {
      $invite_code  = substr(md5( uniqid( microtime() ) ), 10, 7);
    } while( null !== $table->fetchRow(array('code = ?' => $invite_code)) );

    $invite_url     =  ( _ENGINE_SSL ? 'https://' : 'http://' )
                    . $_SERVER['HTTP_HOST']
                    . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                          'module'     => 'invite',
                          'controller' => 'signup',
                          ), 'default', true)
                    . '?'
                    . http_build_query(array('ref' => $user->getIdentity(), 'code' => $invite_code, 'social' => 1));

    $unsubscribe_link = ( _ENGINE_SSL ? 'https://' : 'http://' )
                    . $_SERVER['HTTP_HOST']
                    . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                          'module'      => 'core',
                          'controller'  => 'invite',
                          'action'      => 'unsubscribe',
                          ), 'default', true);

    // insert the invite into the database
    $row = $table->createRow();
    $row->user_id   = $user->getIdentity();
    $row->recipient = 'social';
    $row->code      = $invite_code;
    $row->timestamp = new Zend_Db_Expr('NOW()');
    $row->message   = $message;
    $row->save();
                    
    $mailType = ( $inviteOnlySetting == 2 ? 'invite_code' : 'invite' );


    $mail_settings =   array(

          'host'          => $_SERVER['HTTP_HOST'],
          'email'         => '',
          'date'          => time(),
          'sender_email'  => $user->email,
          'sender_title'  => $user->getTitle(),
          'sender_link'   => $user->getHref(),
          'sender_photo'  => $user->getPhotoUrl('thumb.icon'),
          'message' => $message,
          'object_link'   => $invite_url,
          'code'          => $invite_code,
          'unsubscribe_link' => $unsubscribe_link
      
           );
    

    // Core_Api_Mail::sendSystemRaw();
    
    //$type = 'core_invite';
    $type = $mailType;
    $params = $mail_settings;

    $mailTemplateTable = Engine_Api::_()->getDbtable('MailTemplates', 'core');
    $mailTemplate = $mailTemplateTable->fetchRow($mailTemplateTable->select()->where('type = ?', $type));
    if( null === $mailTemplate ) {
      return;
    }

    // Build subject/body
    $translate = Zend_Registry::get('Zend_Translate');

    $subjectKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_SUBJECT');
    $bodyTextKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_BODY');
    $bodyHtmlKey = strtoupper('_EMAIL_' . $mailTemplate->type . '_BODYHTML');


    // Copy params
    $rParams = $params;

    $isMember = false;
    
    $recipientEmail = '';
    $recipientName = '';

    // Detect language
    if( !empty($rParams['language']) ) {
      $recipientLanguage = $rParams['language'];
    //} else if( !empty($recipient->language) ) {
    //  $recipientLanguage = $recipient->language;
    } else {
      $recipientLanguage = $translate->getLocale();
    }
    if( !Zend_Locale::isLocale($recipientLanguage) ||
        $recipientLanguage == 'auto' ||
        !in_array($recipientLanguage, $translate->getList()) ) {
      $recipientLanguage = $translate->getLocale();
    }

    // add automatic params
    $rParams['email'] = $recipientEmail;
    $rParams['recipient_email'] = $recipientEmail;
    $rParams['recipient_title'] = $recipientName;
    $rParams['recipient_link'] = '';
    $rParams['recipient_photo'] = '';

    

    // Get subject and body
    $subjectTemplate  = (string) $this->_translate($subjectKey,  $recipientLanguage);
    $bodyTextTemplate = (string) $this->_translate($bodyTextKey, $recipientLanguage);
    $bodyHtmlTemplate = (string) $this->_translate($bodyHtmlKey, $recipientLanguage);

    if( !($subjectTemplate) ) {
      throw new Engine_Exception(sprintf('No subject translation available for system email "%s"', $type));
    }
    if( !$bodyHtmlTemplate && !$bodyTextTemplate ) {
      throw new Engine_Exception(sprintf('No body translation available for system email "%s"', $type));
    }

    // Get headers and footers
    $headerPrefix = '_EMAIL_HEADER_' . ( $isMember ? 'MEMBER_' : '' );
    $footerPrefix = '_EMAIL_FOOTER_' . ( $isMember ? 'MEMBER_' : '' );
    
    $subjectHeader  = (string) $this->_translate($headerPrefix . 'SUBJECT',   $recipientLanguage);
    $subjectFooter  = (string) $this->_translate($footerPrefix . 'SUBJECT',   $recipientLanguage);
    $bodyTextHeader = (string) $this->_translate($headerPrefix . 'BODY',      $recipientLanguage);
    $bodyTextFooter = (string) $this->_translate($footerPrefix . 'BODY',      $recipientLanguage);
    $bodyHtmlHeader = (string) $this->_translate($headerPrefix . 'BODYHTML',  $recipientLanguage);
    $bodyHtmlFooter = (string) $this->_translate($footerPrefix . 'BODYHTML',  $recipientLanguage);
    
    // Do replacements
    foreach( $rParams as $var => $val ) {
      $raw = trim($var, '[]');
      $var = '[' . $var . ']';
      //if( !$val ) {
        //$val = $var;
      //}
      $subjectTemplate = str_replace($var, $val, $subjectTemplate);
      $bodyTextTemplate = str_replace($var, $val, $bodyTextTemplate);
      $bodyHtmlTemplate = str_replace($var, $val, $bodyHtmlTemplate);
      $subjectHeader    = str_replace($var, $val, $subjectHeader);
      $subjectFooter    = str_replace($var, $val, $subjectFooter);
      $bodyTextHeader   = str_replace($var, $val, $bodyTextHeader);
      $bodyTextFooter   = str_replace($var, $val, $bodyTextFooter);
      $bodyHtmlHeader   = str_replace($var, $val, $bodyHtmlHeader);
      $bodyHtmlFooter   = str_replace($var, $val, $bodyHtmlFooter);
    }

    // Do header/footer replacements
    $subjectTemplate  = str_replace('[header]', $subjectHeader, $subjectTemplate);
    $subjectTemplate  = str_replace('[footer]', $subjectFooter, $subjectTemplate);
    $bodyTextTemplate = str_replace('[header]', $bodyTextHeader, $bodyTextTemplate);
    $bodyTextTemplate = str_replace('[footer]', $bodyTextFooter, $bodyTextTemplate);
    $bodyHtmlTemplate = str_replace('[header]', $bodyHtmlHeader, $bodyHtmlTemplate);
    $bodyHtmlTemplate = str_replace('[footer]', $bodyHtmlFooter, $bodyHtmlTemplate);

    // Check for missing text or html
    if( !$bodyHtmlTemplate ) {
      $bodyHtmlTemplate = nl2br($bodyTextTemplate);
    } else if( !$bodyTextTemplate ) {
      $bodyTextTemplate = strip_tags($bodyHtmlTemplate);
    }



    $user->invites_used += $invites_count;
    $user->save();




    $api_params = $this->fi_get_api_params();

    include dirname(__FILE__) . DIRECTORY_SEPARATOR . "contactsImporter.php";

    $importer = new ContactsImporter($api_params['api_key'], $api_params['secret']);

    $result['err'] = 0;
    $result['err_msg'] = '';

    $response = $importer->inviteContacts($session, $invite_ids, $subjectTemplate, $bodyTextTemplate, 0);

    if($response === false) {
      $error_code = $importer->getErrorCode();
      $error_message = isset($contacts_importer_errors[$error_code]) ? $contacts_importer_errors[$error_code] : $contacts_importer_errors[1];

      // captcha
      if( $error_code == $importer->API_E_CAPTCHA_REQUIRED ) {
        $result['err'] = 1;
        $result['err_msg'] = $error_message;

        $result['captcha_url'] = $importer->captcha_url;
        
      }

    }

    return $result;

  }

  // Core_Api_Mail::_translate
  protected function _translate($key, $locale, $noDefault = false)
  {
    $translate = Zend_Registry::get('Zend_Translate');
    $value = $translate->translate($key, $locale);
    if( $value == $key || '' == trim($value) ) {
      if( $noDefault ) {
        return false;
      } else {
        $value = $translate->translate($key);
        if( $value == $key || '' == trim($value) ) {
          return false;
        }
      }
    }
    return $value;
  }


  function fi_send_social_invitecode($user, $invite_ids, $invite_message = "", $session) {
    $this->fi_send_social_invitation($user, $invite_ids, $invite_message, $session );
  }


  function fi_send_invitecode($user, $invite_emails, $invite_message="", $resend = false) {
    return $this->fi_send_invitation($user, $invite_emails, $invite_message , $resend );
  }


  function is_email_address($email)
  {
    $regexp = "/^[a-z0-9]+([a-z0-9_\+\\.-]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
    return (bool) preg_match($regexp, $email);
  }

  function make_page($total_items, $items_per_page, $p)
  {
    if( !$items_per_page ) $items_per_page = 1;
    $maxpage = ceil($total_items / $items_per_page);
    if( $maxpage <= 0 ) $maxpage = 1;
    $p = ( ($p > $maxpage) ? $maxpage : ( ($p < 1) ? 1 : $p ) );
    $start = ($p - 1) * $items_per_page;
    return array($start, $p, $maxpage);
  }



  function fi_preprocess_invitation($user, &$invite_emails, $invites_left = null, $resend = false) {

    $invite_emails = is_array($invite_emails) ? $invite_emails : explode(",", $invite_emails);

    $invites_count = count($invite_emails);

    // MAKE SURE THERE ARE NO MORE THAN $invites_left EMAILS
    if(!is_null($invites_left) && !$resend)
      $invites_count = min( $invites_left , $invites_count );

    // MAKE SURE THERE ARE NO MORE THAN 'setting_max_invites_per_day' EMAILS
    $invites_sent_counter = 0;
    $invites_sent_last = 0;

    // STOP HERE IF NO INVITES LEFT
    if($invites_count == 0)
      return 0;

    $invite_emails = array_slice($invite_emails, 0, $invites_count);

    if(!$resend) {

      // USER STATS
      $table = Engine_Api::_()->getDbTable('stats', 'friendsinviter');
      $db = $table->getAdapter();
      $tableName = $table->info("name");

      $sql = "INSERT INTO $tableName (user_id, invites_sent, invites_sent_counter, invites_sent_last)
                VALUES ( ?, ?, ?, FROM_UNIXTIME(?) )
                ON DUPLICATE KEY UPDATE
                user_id = ?,
                invites_sent = invites_sent + ?,
                invites_sent_counter = ?,
                invites_sent_last = FROM_UNIXTIME(?)";
      
      $values = array('user_id'               => $user->getIdentity(),
                      'invites_sent'          => $invites_count, 
                      'invites_sent_counter'  => $invites_sent_counter,
                      'invites_sent_last'     => $invites_sent_last
                      );
      
      $db->query($sql, array_merge(array_values($values), array_values($values)));      

      // GLOBAL STATS
      $this->fi_update_stats("invites", $invites_count);

      // USER POINTS, IF NOT ADMIN
      //if(($user_info['user_id'] != 0) && function_exists("userpoints_update_points"))
      //userpoints_update_points( $user_info['user_id'], "invite", $invites_count );
      Engine_Hooks_Dispatcher::_()->callEvent('onFriendsinviterStats', array('user'  => $user, 'invites_count'  => $invites_count));
      

    }

    return $invites_count;
  }



  function fi_befriend_contacts($befriend) {

    $viewer = Engine_Api::_()->user()->getViewer();

    $befriend_arr = explode( ',', $befriend );
    $friendships_count = 0;
    foreach($befriend_arr as $befriend) {
      $user = Engine_Api::_()->user()->getUser($befriend);

      // check that user is not trying to befriend 'self'
      if( $viewer->isSelf($user) ){
        continue;
      }
  
      // check that user is already friends with the member
      if( $viewer->membership()->isMember($user)){
        continue;
      }
  
      // check that user has not blocked the member
      if( $viewer->isBlocked($user)){
        continue;
      }

      // Process
      $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
      $db->beginTransaction();
  
      try
      {
  
        // check friendship verification settings
        // add membership if allowed to have unverified friendships
        //$user->membership()->setUserApproved($viewer);
  
        // else send request
        $user->membership()->addMember($viewer)->setUserApproved($viewer);
  
  
        // send out different notification depending on what kind of friendship setting admin has set
        /*('friend_accepted', 'user', 'You and {item:$subject} are now friends.', 0, ''),
          ('friend_request', 'user', '{item:$subject} has requested to be your friend.', 1, 'user.friends.request-friend'),
          ('friend_follow_request', 'user', '{item:$subject} has requested to add you as a friend.', 1, 'user.friends.request-friend'),
          ('friend_follow', 'user', '{item:$subject} has added you as a friend.', 1, 'user.friends.request-friend'),
         */
        
  
        // if one way friendship and verification not required
        if(!$user->membership()->isUserApprovalRequired()&&!$user->membership()->isReciprocal()){
          // Add activity
          Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $viewer, 'friends_follow', '{item:$object} is now following {item:$subject}.');
  
          // Add notification
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_follow');
  
          //$message = "You are now following this member.";
        }
  
        // if two way friendship and verification not required
        else if(!$user->membership()->isUserApprovalRequired()&&$user->membership()->isReciprocal()){
          // Add activity
          Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.');
          Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.');
  
          // Add notification
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_accepted');
          //$message = "You are now friends with this member.";
        }
  
        // if one way friendship and verification required
        else if(!$user->membership()->isReciprocal()){
          // Add notification
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_follow_request');
          //$message = "Your friend request has been sent.";
        }
  
        // if two way friendship and verification required
        else if($user->membership()->isReciprocal())
        {
          // Add notification
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_request');
          //$message = "Your friend request has been sent.";
        }
  
  
        //$this->view->status = true;
  
        $db->commit();
  
  
        //$this->view->message = Zend_Registry::get('Zend_Translate')->_('Your friend request has been sent.');
        //$this->_forward('success', 'utility', 'core', array(
        //    'smoothboxClose' => true,
        //    'parentRefresh' => true,
        //    'messages' => array($message)
        //));
      }
      catch( Exception $e )
      {
        $db->rollBack();
  
        //$this->view->status = false;
        //$this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
        //$this->view->exception = $e->__toString();

        $friendships_count--;
      }
      
      $friendships_count++;
      
    }
    
    return $friendships_count;    
    
  }


  public function findIdsByEmail($emails)
  {
    $table  = Engine_Api::_()->getDbTable('users', 'invite');
    $select = $table->select()
                    ->where('email IN (?)', $emails);
    $results = array();
    foreach ($table->fetchAll($select) as $row)
    {
      $results[ $row->email ] = $row->user_id;
    }
    return $results;
  }
  
}