<?php
//
// +---------------------------------------------------------------------------+
// | Contacts Importer PHP4/5 client                                           |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2008 FriendsInviter.com                                     |
// | All rights reserved.                                                      |
// |                                                                           |
// |                                                                           |
// | Version 0.5 10-Jan-2008                                                   | 
// +---------------------------------------------------------------------------+
//


class ContactsImporter {
  var $api_key;
  var $secret;

  var $errID;
  var $errMsg;
  
  var $encoding = 'utf-8';        // result will be returned in this encoding

  var $statsUser;                 // for statistics
  var $session;                   // importing session
  var $captcha_url;               // captcha image


  function ContactsImporter($api_key, $secret) {
    $this->api_key      = $api_key;
    $this->secret       = $secret;
    $this->server_addr  = 'http://api.friendsinviter.com/restserver.php';
  }


  /**
   * Returns contacts imported from specified service.
   * @param string $username : Username for specified service, including domain, unless $service is specified
   * @param string $password : Password for specified service
   * @param string $service Optional : service to import contacts from. 
   *   Empty parameter will enable autodetection of the service from the domain part of the $username parameter
   * @param string $result_structure Optional (Default: 0) : format to return the result in. Currently two formats
   *    are supported: 0 - array of names, emails, [...] arrays with matching ids
   *                 example: Array
   *                           (
   *                            [0] => Array
   *                              (
   *                                [0] => First Last
   *                                [1] => John Doe
   *                              )
   *
   *                            [1] => Array
   *                              (
   *                                [0] => first@example.com
   *                                [1] => john@example.com
   *                              )
   *                           )
   *               1 - array of contacts
   *                 example:
   *                 Array
   *                (
   *                    [0] => Array
   *                        (
   *                            [name] => First Last
   *                            [email] => first@example.com
   *                        )
   *                        
   *                    [1] => Array
   *                        (
   *                            [name] => John Doe
   *                            [email] => john@example.com
   *                        )
   *                )
   *
   * @return mixed : array containing name, email, [...] according to $result_structure parameter
   *  or false if error occured
   *
   *  NOTE: It's possible empty fields (name) will be returned.
   *        Emails will always be non-empty field
   */
  function getContacts($username, $password, $service = 'auto', $result_structure = 0, $session = null, $captcha_response = null ){
    
    $params = array(    'u' => $username,
                        'p' => $password,
                        'svc' => $service );

    if(!is_null($session))
     $params['session'] = $session;
     
    if(!is_null($captcha_response))
      $params['captcha_response'] = $captcha_response;
    
    if(!empty($this->encoding))
        $params['e'] = $this->encoding;
        
    if(!empty($this->statsUser))
        $params['s_u'] = $this->statsUser;
        
    $data = $this->convert_array_to_params( $params );
    
    $c = $this->encrypt($data);

    $result = $this->call_method
          ('getContacts',
           array('b' => $data,
                 'c' => $c ));

    // error occured
    if($result === null) {
        return false;
    }

    // 0 contacts
    if($result === '') {
        return array();
    }
    
    if(isset($result['session'])) {
      $this->session = $result['session'];
    }
      
    $contacts = isset($result['contacts']) ? $result['contacts'] : array();
    $this->convertEncoding( $contacts );
    
    if($result_structure == 0){
        foreach($contacts as $contact) {
            $names[] = $contact['name'];
            $emails[] = $contact['email'];
        }
        $result_array = array($names, $emails);
    } else {
        $result_array = $contacts;
    } 
    
    return $result_array;
  }

  function inviteContacts($session, $invite_ids, $subject, $message, $message_type = 0, $captcha_response = null) {

    $params = array(    'user_ids' => $invite_ids,
                        'subject' => $subject,
                        'message' => $message,
                        'message_type'  => $message_type );

    if(!is_null($captcha_response))
      $params['captcha_response'] = $captcha_response;

    $data = $this->convert_array_to_params( $params );
    
    $c = $this->encrypt($data);

    $result = $this->call_method
          ('inviteContacts',
           array('b' => $data,
                 'c' => $c,
                 'session' => $session ));

    // error occured
    if($result === null) {
        return false;
    }
    
    return true;
  }

  function setEncoding($encoding) {
    $this->encoding = $encoding;
  }

  function setStatsUser($statsUser) {
    $this->statsUser = $statsUser;
  }

  function getErrorCode() {
    return $this->errID;
  }

  function getErrorMessage() {
    return $this->errMsg;
  }
  
  function clearError() {
    $this->errID = 0;
    $this->errMsg = '';
  }

  function call_method($method, $params) {
    $this->clearError();
    $xml = $this->post_request($method, $params);
    if(is_null($xml))
      return null;

    $result = $this->load_and_parse_xml( $xml );

    if (is_array($result) && isset($result['error_code'])) {
        $this->errMsg = $result['error_msg'];
        $this->errID = $result['error_code'];
        isset($result['session']) ? $this->session = $result['session'] : 0;
        isset($result['captcha_url']) ? $this->captcha_url = $result['captcha_url'] : 0;
        return null;
    }
    return $result;
  }
  
  function load_and_parse_xml($xml) {
    if(function_exists('simplexml_load_string')) {
      $sxml = simplexml_load_string($xml);
      return $this->convert_simplexml_to_array( $sxml );
    } else {

      include_once 'simplexml44-0_4_4/class/IsterXmlSimpleXMLImpl.php';
      
      $impl = new IsterXmlSimpleXMLImpl();
      $sxml = $impl->load_string($xml);
      $result = array();
      $children = $sxml->children();
      return $this->convert_simplexml44_to_array($children[0]);
    
    }

    
  }
  

  function post_request($method, $params) {
    $params['method'] = $method;
    $params['api_key'] = $this->api_key;
    if (!isset($params['v'])) {
      $params['v'] = '0.5';
    }

    $post_string = $this->convert_array_to_params( $params, true );

    @set_time_limit(60);

    // Use CURL if installed
    if (function_exists('curl_init'))
      return $this->post_request_with_curl( $post_string );
    else {
      $result = $this->post_request_without_curl( $post_string );

      // no url wrappers / allow_url_fopen is disabled
      if(($result == null) && ($this->errID == 1001)) {
        $this->errID = 0;
        $this->errMsg = '';
        $result = $this->post_request_without_curl_php4( $post_string );
      }
      
      return $result;
    }
    
  }
  
  function post_request_with_curl($data) {
      $ch = curl_init();

      // PHP5 only
      // curl_setopt_array( $ch 
      
      curl_setopt( $ch, CURLOPT_URL, $this->server_addr );
      curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
      curl_setopt( $ch, CURLOPT_ENCODING, '' );
      curl_setopt( $ch, CURLOPT_USERAGENT, 'ContactsImporter API PHP5 Client 0.5 (curl) ' . phpversion() );
                                    
      $result = curl_exec($ch);
      if(curl_errno($ch)) {
        $this->errMsg = 'HTTP Error: ' . curl_error( $ch );
        $this->errID = $this->API_E_HTTP;
        return null;
      }
      curl_close($ch);
      return $result;
  }

  function post_request_without_curl($data) {
      $context_opts =
        array('http' =>
              array('method' => 'POST',
                    'header' => 'Content-Type: application/x-www-form-urlencoded' . "\r\n" .
                                'User-Agent: ContactsImporter API PHP5 Client 0.5 (non-curl) '. phpversion() . "\r\n" .
                                'Content-Length: ' . strlen($data),
                    'content' => $data));
      $context = stream_context_create($context_opts);
      $fp = @fopen($this->server_addr, 'r', false, $context);
      if (!$fp) {
        $this->errMsg = 'HTTP Error';
        $this->errID = 1001;
        return null;
      }
      $result = @stream_get_contents($fp);
      if( $result === false ) {
        $this->errMsg = 'HTTP Error';
        $this->errID = $this->API_E_HTTP;
        return null;
      }
      return $result;
  }

  function post_request_without_curl_php4($data) {
    // url MUST have scheme
	$start = strpos( $this->server_addr, '//' ) + 2;
	$end = strpos( $this->server_addr, '/', $start );
	$host = substr( $this->server_addr, $start, $end - $start );
	$post_path = substr( $this->server_addr, $end );
    $fp = @fsockopen( $host, 80 );
    if (!$fp) {
      $this->errMsg = 'HTTP Error';
      $this->errID = $this->API_E_HTTP;
      return null;
    }
    fputs( $fp, "POST $post_path HTTP/1.0\n" .
                "Host: $host\n" . 
                'User-Agent: ContactsImporter API PHP4 Client 0.5 (non-curl) '. phpversion() . "\n" . 
                "Content-Type: application/x-www-form-urlencoded\n" .
                "Content-Length: " . strlen($data) . "\n\n" . 
                "$data\n\n" );
	$response = '';
	while(!feof($fp)) {
		$response .= fgets($fp, 4096);
	}
	fclose ($fp);
    // get response code
    preg_match( '/^\S+\s(\S+)/', $response, $matches );
    if( $matches[1] != "200" ) {
      $this->errMsg = 'HTTP Error';
      $this->errID = $this->API_E_HTTP;
      return null;
    }
    // get response body
    preg_match( '/\r?\n\r?\n(.*?)$/sD', $response, $matches );
    $response = $matches[1];
	return $response;
  }

  function convert_array_to_params($params, $addSig = false) {
    $post_params = array();
    foreach ($params as $key => $val) {
      if (is_array($val)) $val = implode(',', $val);
      $post_params[] = $key.'='.urlencode($val);
    }
    if($addSig) {
        $secret = $this->secret;
        $post_params[] = 'sig='.$this->generate_sig($params, $secret);
    }

    return implode('&', $post_params);
  }
  
  function convert_simplexml_to_array($sxml) {
    $arr = array();
    if ($sxml) {
      foreach ($sxml as $k => $v) {
        if ($sxml['list']) {
          $arr[] = $this->convert_simplexml_to_array($v);
        } else {
          $arr[$k] = $this->convert_simplexml_to_array($v);
        }
      }
    }
    if (sizeof($arr) > 0) {
      return $arr;
    } else {
      return (string)$sxml;
    }
  }
  
  function convert_simplexml44_to_array($sxml) {
    if ($sxml) {
      $arr = array();
      $attrs = $sxml->attributes();
      foreach ($sxml->children() as $child) {
        if (!empty($attrs['list'])) {
          $arr[] = $this->convert_simplexml44_to_array($child);
        } else {
          $arr[$child->___n] = $this->convert_simplexml44_to_array($child);
        }
      }
      if (sizeof($arr) > 0) {
        return $arr;
      } else {
        return (string)$sxml->CDATA();
      }
    } else {
      return '';
    }
  }

  function generate_sig($params_array, $secret) {
    $str = '';
    ksort($params_array);
    foreach ($params_array as $k=>$v) {
      $str .= "$k=$v";
    }
    $str .= $secret;
    return md5($str);
  }

  function encrypt(&$data) {
    return $this->crypt_internal($data);

    if( extension_loaded('mcrypt') ) 
        return $this->crypt_mcrypt($data);
    else
        return $this->crypt_internal($data);
  }
  
  function crypt_mcrypt(&$data) {
    $data = bin2hex( mcrypt_ecb (MCRYPT_BLOWFISH, $this->secret, $data, MCRYPT_ENCRYPT) );
    return 1;
  }

  function crypt_internal(&$data) {
        
    $key = $this->secret;
    $s = array();
    $len= strlen($key);
    for ($i = 0; $i < 256; $i++) {
        $s[$i] = $i;
    }

    $j = 0;
    for ($i = 0; $i < 256; $i++) {
        $j = ($j + $s[$i] + ord($key[$i % $len])) % 256;
        $t = $s[$i];
        $s[$i] = $s[$j];
        $s[$j] = $t;
    }
    $i = $j = 0;

    $len= strlen($data);
    for ($c= 0; $c < $len; $c++) {
        $i = ($i + 1) % 256;
        $j = ($j + $s[$i]) % 256;
        $t = $s[$i];
        $s[$i] = $s[$j];
        $s[$j] = $t;

        $t = ($s[$i] + $s[$j]) % 256;

        $data[$c] = chr(ord($data[$c]) ^ $s[$t]);
    }
    // required?
    $data = bin2hex($data);
    return 2;
  }

  // converts response client encoding
  // for now, converts only name
  function convertEncoding(&$contacts) {
    if(0 == strcasecmp( $this->encoding, 'utf-8'))
        return;
    
    foreach($contacts as $key => $contact) {
        $contact['name'] = @iconv( "UTF-8", $this->encoding . "//IGNORE", $contact['name'] );
        $contacts[$key] = $contact;
    }
  }

  
  function getErrorDescription($error_id) {
    if(is_empty($this->api_error_descriptions))
      $this->api_error_descriptions = array(
        $this->API_E_HTTP                      => 'HTTP Error',
        $this->API_E_HTTP_FOPEN                => 'HTTP Error',
        $this->API_E_SUCCESS                   => 'Success',
        $this->API_E_UNKNOWN                   => 'Unknown error occurred',
        $this->API_E_METHOD                    => 'Unknown method',
        $this->API_E_SIGNATURE                 => 'Signature verification failed',
        $this->API_E_PARAMS                    => 'Incomplete/Invalid parameters received',
        $this->API_E_API_KEY                   => 'Invalid API key',
        $this->API_E_TOO_MANY_CALLS            => 'Request limit reached',
        $this->API_E_BAD_IP                    => 'Unauthorized IP address',
        $this->API_E_NO_SERVICE                => 'Service temporarily unavailable',
        $this->API_E_NOT_SUBSCRIBED            => 'Not subscribed for this service',
        $this->API_E_AUTH                      => 'Authentication Failed',
        $this->API_E_EMPTY_USER_OR_PASSWORD    => 'Empty User or Password fields',
        $this->API_E_CANT_DETECT_SERVICE       => 'Unable to decide which service to use',
        $this->API_E_UNSUPPORTED_SERVICE       => 'Unsupported service',
        $this->API_E_SERVICE_UNREACHABLE       => 'Service is unreachable at this moment',
        $this->API_E_SERVICE_ERROR             => 'Error during communication to service',
        $this->API_E_CAPTCHA_REQUIRED          => 'Captcha Required'
      );
      
      return $this->api_error_descriptions[$error_id];
  }


  /* Error codes and descriptions */


  var $API_E_SUCCESS = 0;

  var $API_E_HTTP = 1000;
  var $API_E_HTTP_FOPEN = 1001;

  /* Generic Errors */
  
  var $API_E_UNKNOWN = 1;
  var $API_E_METHOD = 2;
  var $API_E_SIGNATURE = 3;
  var $API_E_PARAMS = 4;
  var $API_E_API_KEY = 5;
  var $API_E_TOO_MANY_CALLS = 6;
  var $API_E_BAD_IP = 7;
  var $API_E_NO_SERVICE = 8;
  var $API_E_NOT_SUBSCRIBED = 9;

  /* Service Errors */
  
  var $API_E_AUTH = 100;
  var $API_E_EMPTY_USER_OR_PASSWORD = 101;
  var $API_E_CANT_DETECT_SERVICE = 102;
  var $API_E_UNSUPPORTED_SERVICE = 103;
  var $API_E_SERVICE_UNREACHABLE = 104;
  var $API_E_SERVICE_ERROR = 105;
  var $API_E_CAPTCHA_REQUIRED = 107;
  

  var $api_error_descriptions = array();

}


?>