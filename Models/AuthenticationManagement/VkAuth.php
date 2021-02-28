<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 02.09.14
 * Time: 15:16
 */

namespace Models\AuthenticationManagement;


class VkAuth extends SocialAuth{
    protected static $i = NULL;

    const NETWORK_KEY = 'vk';
    const NETWORK_NAME = 'Вкотнтакте';
    const ACCESS_TOKEN_URL = 'https://oauth.vk.com/access_token';
    const API_URL = 'https://api.vk.com/method/';
    const CLIENT_ID = '4536457';//'4533625';
    const CLIENT_SECRET = 'DBqElusOelpm0jMC2zRK';//'owyGVOjmI4Pb2RxPGPOq';
    const REDIRECT_URI = '/login/?auth_type=vk';
    const AUTHORIZE_URL = 'https://oauth.vk.com/authorize';

    protected $config_fields = array(
        'client_id' => 'Client ID',
        'client_secret' => 'Client Secret'
    );

    private static $fields = array('uid', 'first_name', 'last_name', 'screen_name', 'sex', 'bdate');

    public function getAuthLink(){
        return self::AUTHORIZE_URL . '?client_id=' . $this->client_id . '&scope=email&redirect_uri=' . urlencode(self::getSiteUrlForAuthLink() . self::REDIRECT_URI) . '&response_type=code&v=5.24';
    }

    public function getUserData(array $request_data, &$error = NULL){
        if (empty($request_data['code'])){
            $error = 'code empty';
            return NULL;
        }
        $request = json_decode(file_get_contents(self::ACCESS_TOKEN_URL . '?client_id=' . $this->client_id . '&client_secret=' . $this->client_secret . '&code=' . $request_data['code'] . '&redirect_uri=' . urlencode(self::getSiteUrlForAuthLink() . self::REDIRECT_URI)), true);
        if (!empty($request['error'])){
            $error = $request['error'] . ':' . $error['error_description'];
            return NULL;
        }
        if (empty($request['access_token'])){
            $error = 'token_empty';
            return NULL;
        }
        if (empty($request['user_id'])){
            $error = 'user_id';
            return NULL;
        }
        $user_data = json_decode(file_get_contents($this->makeUrl($request['access_token'], 'users.get', $this->makeUsersGetParams($request['user_id']))), true);
        $user_data = reset($user_data['response']);
        return $this->makeUserData($request, $user_data);
    }

    /**
     * @param $token
     * @param $method
     * @param $params
     * @return string
     */
    private function makeUrl($token, $method, $params){
        return self::API_URL . '/' . $method . '?' . http_build_query($params) . '&access_token=' . $token;
    }

    /**
     * @param $user_id
     * @return array
     */
    private function makeUsersGetParams($user_id){
        return array(
            'user_ids' => $user_id,
            'fields' => implode(',', self::$fields),
            'name_case' => 'nom'
        );
    }

    private function makeUserData($request, $user_data){
        return array(
            'network' => self::NETWORK_KEY,
            'identity' => $request['user_id'],
            'email' => !empty($request['email']) ? $request['email'] : NULL,
            'name' => $user_data['first_name'],
            'surname' => $user_data['last_name']
        );
    }
} 