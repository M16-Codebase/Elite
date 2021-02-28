<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 02.09.14
 * Time: 19:08
 */

namespace Models\AuthenticationManagement;


class FacebookAuth extends SocialAuth{
    protected static $i = NULL;

    const NETWORK_KEY = 'facebook';
    const NETWORK_NAME = 'Facebook';
    const ACCESS_TOKEN_URL = 'https://graph.facebook.com/oauth/access_token';
    const API_URL = 'https://graph.facebook.com/me';
    const CLIENT_ID = '763908677001938';//*/'1495257077397598';
    const CLIENT_SECRET = '5879253eb0c68238221062a5eb444ed6';//*/'75017e8706b6e96c3f00d496b0cc1032';
    const REDIRECT_URI = '/login/?auth_type=facebook';
    const AUTHORIZE_URL = 'https://www.facebook.com/dialog/oauth';

    protected $config_fields = array(
        'client_id' => 'Client ID',
        'client_secret' => 'Client Secret'
    );

    public function getAuthLink(){
        return self::AUTHORIZE_URL . '?client_id=' . $this->client_id . '&redirect_uri=' . urlencode(self::getSiteUrlForAuthLink() . self::REDIRECT_URI) . '&response_type=code&scope=public_profile,email';
    }

    public function getUserData(array $request_data, &$error = NULL){
        if (empty($request_data['code'])){
            $error = 'code empty';
            return NULL;
        }
        $request = $this->executeCurlRequest(self::ACCESS_TOKEN_URL . '?client_id=' . $this->client_id . '&client_secret=' . $this->client_secret . '&code=' . $request_data['code'] . '&redirect_uri=' . urlencode(self::getSiteUrlForAuthLink() . self::REDIRECT_URI));
        $error = json_decode($request, true);
        if (!empty($error)){
            $error = $error['error']['type'] . ':' . $error['error']['code'];
            return NULL;
        }
        parse_str($request, $request);
        if (empty($request['access_token'])){
            $error = 'token empty';
            return NULL;
        }
        $user_data = json_decode($this->executeCurlRequest(self::API_URL . '?access_token=' . $request['access_token']), true);
        return $this->makeUserData($user_data);
    }

    private function makeUserData($user_data){
        return array(
            'network' => self::NETWORK_KEY,
            'identity' => $user_data['id'],
            'email' => !empty($user_data['email']) ? $user_data['email'] : NULL,
            'name' => $user_data['first_name'],
            'surname' => $user_data['last_name']
        );
    }
} 