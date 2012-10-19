<?php

class Inviter_Plugin_Lastfm {

    private $rest_url = 'http://ws.audioscrobbler.com/2.0/';

    public function sig($params = array(), $secret = '') {
        ksort($params);
        $signature = '';
        foreach($params as $key => $value)
            $signature .= $key . $value;
        $signature .= $secret;
        $signature = md5($signature);
        return $signature;
    }

    public function make_request($params) {
        $params_string = '';
        foreach($params as $key=>$value)
            $params_string .= $key . '=' . $value . '&';
        $options = array (
            CURLOPT_URL => $this->rest_url,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params_string,
            CURLOPT_RETURNTRANSFER => true
        );

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $xml = simplexml_load_string($response);
        return $xml;
    }

}
?>
