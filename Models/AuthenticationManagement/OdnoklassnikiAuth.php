<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 03.09.14
 * Time: 12:30
 */

namespace Models\AuthenticationManagement;


class OdnoklassnikiAuth extends SocialAuth{
    protected static $i = NULL;

    protected $application_id = NULL;
    protected $public_key = NULL;
    protected $client_secret = NULL;

    const NETWORK_KEY = 'odnoklassniki';
    const NETWORK_NAME = 'Одноклассники';
    const ACCESS_TOKEN_URL = 'http://api.ok.ru/oauth/token.do';
    const API_URL = 'http://api.ok.ru/fb.do';
    const CLIENT_ID = '1100337664';
    const PUBLIC_KEY = 'CBADNGJCEBABABABA';
    const CLIENT_SECRET = '25182D07CD60DC05AAB839D0';
    const REDIRECT_URI = '/login/?auth_type=odnoklassniki';
    const AUTHORIZE_URL = 'http://www.ok.ru/oauth/authorize';

    protected $config_fields = array(
        'application_id' => 'Application ID',
        'public_key' => 'Публичный ключ приложения',
        'client_secret' => 'Секретный ключ приложения'
    );

    protected function setNetworkConfig($config_data){
        if (!empty($config_data)){
            $this->application_id = $config_data['application_id'];
            $this->public_key = $config_data['public_key'];
            $this->client_secret = $config_data['client_secret'];
            return  $config_data['enable'] ? TRUE : FALSE;
        } else {
            return FALSE;
        }
    }

    public function getAuthLink(){
        return self::AUTHORIZE_URL . '?client_id=' . $this->application_id . '&response_type=code&redirect_uri=' . self::getSiteUrlForAuthLink() . self::REDIRECT_URI;
    }

    public function getUserData(array $request_data, &$error = NULL){
        if (empty($request_data['code'])){
            $error = 'code empty';
            return NULL;
        }
        $token_request = json_decode($this->executeCurlRequest(self::ACCESS_TOKEN_URL, array(
            'code' => $request_data['code'],
            'redirect_uri' => self::getSiteUrlForAuthLink() . self::REDIRECT_URI,
            'grant_type' => 'authorization_code',
            'client_id' => $this->application_id,
            'client_secret' => $this->client_secret
        )), true);
        if (empty($token_request['access_token'])){
            $error = 'token empty';
            return NULL;
        }
        $user_data = json_decode($this->executeCurlRequest(self::API_URL, array(
            'method'          => 'users.getCurrentUser',
            'access_token'    => $token_request['access_token'],
            'application_key' => $this->public_key,
            'format'          => 'json',
            'sig'             => md5('application_key=' . $this->public_key . 'format=jsonmethod=users.getCurrentUser' . md5($token_request['access_token'] . $this->client_secret))
        )), true);
        var_dump($user_data);
        return $this->makeUserData($user_data);
    }

    private function makeUserData($user_data){
        return array(
            'network' => self::NETWORK_KEY,
            'identity' => $user_data['uid'],
            'email' => !empty($user_data['email']) ? $user_data['email'] : NULL,
            'name' => $user_data['first_name'],
            'surname' => $user_data['last_name']
        );
    }

} 