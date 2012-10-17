<?php

class Inviter_Plugin_fbApi
{

    var $app_id;
    var $secret;

    var $active = false;
    var $graph_api_url = 'https://graph.facebook.com/';

    function __construct()
    {
        //        if(!$app_id || !$secret) {
        //            return false;
        //        }
        //        $this->app_id = $app_id;
        //        $this->secret = $secret;
        //        $this->active = true;
    }

    function init($app_id, $secret)
    {
        if (!$app_id || !$secret) {
            return false;
        }
        $this->app_id = $app_id;
        $this->secret = $secret;
        $this->active = true;
    }

    public function getMe($access_token, $id = false)
    {

        $url = $this->graph_api_url . 'me?access_token=' . $access_token;
        $me = $this->request($url);
        if ($id)
            return $me->id;
        return $me;
    }

    public function getUser($access_token, $user_id)
    {

        $url = $this->graph_api_url . $user_id . '?access_token=' . $access_token . '&scope=email';
        $user = $this->request($url);

        return $user;
    }

    public function apprequest($access_token, $user)
    {
        $token_url = "https://graph.facebook.com/oauth/access_token?" .
            "client_id=" . $this->app_id .
            "&client_secret=" . $this->secret .
            "&grant_type=client_credentials";
        $app_token = $this->request($token_url, 'POST');
        $app_token = explode('=', $app_token);
        $app_token = $app_token[1];

        $message = "Message with space and with link - http://wasm.ru";
        $message = urlencode($message);

        $url = 'https://graph.facebook.com/' . $user . '/apprequests?' . 'message=' . $message . '&access_token=' . $app_token . '&method=post';
        $res = $this->request($url, 'POST');
        return $res;
    }

    public function message($access_token, $user)
    {
        $message = "Message with space and with link - http://wasm.ru";
        $message = urlencode($message);

        $url = 'https://graph.facebook.com/me/message?access_token=' . $access_token . '&message=' . $message . '&to=' . $user;
        $params['access_token'] = $access_token;
        $params['message'] = 'Test wall posting. Test link - '; // . $link;
        $res = $this->request($url, 'POST', $params);
        return $res;
    }

    public function postToWall($access_token, $user)
    {
        $message = "Message with space and with link - http://wasm.ru";
        $message = urlencode($message);

        $url = 'https://graph.facebook.com/' . $user . '/feed?access_token=' . $access_token . '&message=' . $message;
        $params['access_token'] = $access_token;
        $params['message'] = 'Test wall posting. Test link - '; // . $link;
        $res = $this->request($url, 'POST', $params);
        return $res;
    }

    public function getAccessToken($redirect_url, $code, $ttt = null)
    {
        if (!$this->active)
            return 'Not activated';

        if (!$redirect_url || !$code)
            return false;

        $url = 'https://graph.facebook.com/oauth/access_token?client_id=' . $this->app_id . '&redirect_uri=' . $redirect_url . '&client_secret=' . $this->secret . '&code=' . $code;
        $token = $this->request($url, 'get', 'access_token');

        $params = explode('&', $token);

        if (!empty($params)) {
            $token = explode('=', $params[0]);
            if (!empty($token))
                return $token[1];
        }

        return false;
    }

    public function getLoginUrl($redirect_url, $scope = null)
    {
        if (!$this->active)
            return 'Not activated';
        if (!$redirect_url)
            return false;
        $redirect_url = urlencode($redirect_url);
        $url = 'https://www.facebook.com/dialog/oauth?';
        $url .= 'display=popup';
        $url .= '&client_id=' . $this->app_id;
        $url .= '&redirect_uri=' . $redirect_url;
        if ($scope)
            $url .= '&scope=' . $scope;

        return $url;
    }

    public function getLogOutUrl($access_token, $redirect_url)
    {
        $redirect_url = urlencode($redirect_url);
        $url = 'https://www.facebook.com/logout.php?next=' . $redirect_url . '&access_token=' . $access_token;
        return $url;
    }

    public function getFriends($access_token, $ids = null)
    {
        $url = $this->graph_api_url . 'me/friends' . '?access_token=' . $access_token;
        $friends_object = $this->request($url);
        $friends_data = $friends_object->data;

        $friends = array();
        $friends_ids = array();
        foreach ($friends_data as $block) {
            //            $friends[] = array('name'=>$block->name, 'id'=>$block->id);
            //            $friends_ids[] = $block->id;
            $friends[$block->id] = $block->name;
            $friends_ids[] = $block->id;
        }
        $users = array();
        foreach ($ids as $id) {
            foreach ($friends as $key => $value) {
                if ($id == $key)
                    $users[$id] = $value;
            }
        }
        if ($ids)
            return $users;
        return $friends;
    }

    public function getUserInfo($user_id, $access_token)
    {

    }

    private function request($url = '', $method = 'get', $action = null)
    {
        $ch = curl_init();
        $options = array();
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        $options[CURLOPT_RETURNTRANSFER] = true;

        if ($method == 'get') {
            $options[CURLOPT_HTTPGET] = true;
        } else {
            $options[CURLOPT_CUSTOMREQUEST] = 'POST';
        }
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        if ($action == 'access_token') {
            return $response;
        }
        $response = json_decode($response);
        return $response;
    }

}

