<?php

class Inviter_Plugin_MyMail {

    // 17018818635374722806 - Sergeev Sergei

    private $client_id = null;
    private $private = null;
    private $secret = null;

    private $auth_url = 'https://connect.mail.ru/oauth/authorize';
    private $oauth_url = 'https://connect.mail.ru/oauth/token';
    private $rest_url = 'http://www.appsmail.ru/platform/api';

    public function __construct($client_id = null, $private = null, $secret = null) {
//        if(!$client_id || !$private || !$secret)
//            return false;

        $this->client_id = $client_id;
        $this->private = $private;
        $this->secret = $secret;
    }

    public function init() {
        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $client_id = $settings->getSetting('inviter.mailru.id');
        $private = $settings->getSetting('inviter.mailru.private.key');
        $secret = $settings->getSetting('inviter.mailru.secret.key');

        $this->client_id = $client_id;
        $this->private = $private;
        $this->secret = $secret;
    }

    public function getAuthUrl($redirect_url = null) {
        if(!$redirect_url || !$this->client_id)
            return false;
        $url = $this->auth_url;
        $url .= '?client_id='.$this->client_id.'&response_type=code&redirect_uri='.$redirect_url.'&scope=messages';
        return $url;
    }

    public function getAccessToken ($redirect_url = null, $code = null) {
        if(!$redirect_url || !$code)
            return false;

        $params = array();
        $params['client_id'] = $this->client_id;
        $params['client_secret'] = $this->secret;
        $params['grant_type'] = 'authorization_code';
        $params['code'] = $code;
        $params['redirect_uri'] = $redirect_url;

        $sk = $this->request($this->oauth_url, $params);

        if(!$sk->error)
            return $sk->access_token;
        return false;
    }

    public function getFriends($session_key = null) {
        if(!$session_key)
            return false;
        $params = array();
        $params['method'] = 'friends.get';
        $params['ext'] = '1';
        $params['app_id'] = $this->client_id;
        $params['session_key'] = $session_key;
        $params['secure'] = 1;
        $params['sig'] = $this->signature($params, $this->secret);
        $friends = $this->request($this->rest_url, $params);

        if($friends->error)
            return false;
        $result = array();

        foreach($friends as $friend) {
            $fname = $friend->first_name;
            $lname = $friend->last_name;
            $tmp['id'] = $friend->uid;
            $tmp['name'] = $fname . ( (trim($fname)!='') ?' ':'' ). $lname;
            $tmp['email'] = $friend->uid;
            $result[] = $tmp;
        }
        return $result;
    }

    public function getUser($session_key = null) {
        if(!$session_key)
            return false;
        $params = array();
        $params['method'] = 'users.getInfo';
        $params['app_id'] = $this->client_id;
        $params['session_key'] = $session_key;
        $params['secure'] = 1;
        $params['sig'] = $this->signature($params, $this->secret);
        $user = $this->request($this->rest_url, $params);
        if(!$user->error)
            return $user[0];
        return false;
    }

    public function getUserInfo($user = null) {
        if(!$user)
            return false;
        $fname = $user->first_name;
        $lname = $user->last_name;
        $name = $fname . ( (trim($fname)!='') ?' ':'' ). $lname;
        $info = array();
        $info['object_id'] = $user->uid;
        $info['object_name'] = $name;
        $info['oauth_token_secret'] = $this->secret;
        return $info;
    }

    public function sendMessage($uid = null, $session_key = null, $message = null){
        if(!$uid || !$session_key || !$message)
            return false;
//$message .= 'http://wasm.ru';
        $params = array();
        $params['method'] = 'messages.post';
        $params['app_id'] = $this->client_id;
        $params['session_key'] = $session_key;
        $params['secure'] = 1;
        $params['uid'] = $uid;
        $params['message'] = $message;
        $params['sig'] = $this->signature($params, $this->secret);

        $res = $this->request($this->rest_url, $params);

        if(!$res->error)
            return true;
        return false;
    }

    private function request($url = null, $params = null) {
        if(!$url|| !$params)
            return false;

        $pstr = '';
        foreach($params as $key=>$value) {
            $pstr .= $key .'='. $value . '&';
        }

        $ch = curl_init();
        $options = array();
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_POST]= true;
        $options[CURLOPT_POSTFIELDS]= $pstr;

        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $response = json_decode($response);
        return $response;
    }

    private function signature ($params = array()) {
        if(count($params) < 1 || !$this->secret)
            return false;

        ksort($params);
        $sparams = '';
        foreach ($params as $key => $value) {
            $sparams .= "$key=$value";
          }
        $sig = md5($sparams . $this->secret);
        return $sig;
    }
}
