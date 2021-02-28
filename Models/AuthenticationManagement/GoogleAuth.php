<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 04.09.14
 * Time: 18:47
 */

namespace Models\AuthenticationManagement;


class GoogleAuth extends SocialAuth{
    protected static $i = NULL;

    const NETWORK_KEY = 'google';
    const NETWORK_NAME = 'Google+';
    const ACCESS_TOKEN_URL = 'https://accounts.google.com/o/oauth2/token';
    const API_URL = 'https://www.googleapis.com/oauth2/v1/userinfo';
    const CLIENT_ID = '901057459103-v78ui153e9k25c3lupb0u789q10vo9th.apps.googleusercontent.com';
    const CLIENT_SECRET = 'y1QMXzK_ihfl6j1q4loK7BRx';
    const REDIRECT_URI = '/login/?auth_type=google';
    const AUTHORIZE_URL = 'https://accounts.google.com/o/oauth2/auth';

    protected $config_fields = array(
        'client_id' => 'Client ID',
        'client_secret' => 'Client Secret'
    );

    public function getAuthLink(){
        return self::AUTHORIZE_URL . '?client_id=' . $this->client_id . '&scope=' . urlencode('email profile') . '&redirect_uri=' . urlencode(self::getSiteUrlForAuthLink() . self::REDIRECT_URI) . '&response_type=code&access_type=offline';
    }

    public function getUserData(array $request_data, &$error = NULL){
        if (empty($request_data['code'])){
            $error = 'code empty';
            return NULL;
        }
        $token_request = json_decode(self::executeCurlRequest(self::ACCESS_TOKEN_URL, array(
            'code' => $request_data['code'],
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri' => self::getSiteUrlForAuthLink() . self::REDIRECT_URI,
            'grant_type' => 'authorization_code'
        )), true);
        if (empty($token_request['access_token'])){
            $error = 'token empty';
            return NULL;
        }
        $user_data = json_decode(self::executeCurlRequest(self::API_URL . '?access_token=' . $token_request['access_token']), true);
        return $this->makeUserData($user_data);
    }

    private function makeUserData($user_data){
        return array(
            'network' => self::NETWORK_KEY,
            'identity' => $user_data['id'],
            'email' => !empty($user_data['email']) ? $user_data['email'] : NULL,
            'name' => $user_data['given_name'],
            'surname' => $user_data['family_name']
        );
    }

} 