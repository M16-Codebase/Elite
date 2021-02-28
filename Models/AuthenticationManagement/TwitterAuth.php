<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 03.09.14
 * Time: 16:49
 */

namespace Models\AuthenticationManagement;


use App\Builder;

class TwitterAuth extends SocialAuth{
    protected static $i = NULL;

    protected $consumer_key = NULL;
    protected $consumer_secret = NULL;

    const NETWORK_KEY = 'twitter';
    const NETWORK_NAME = 'Twitter';
    const URL_ACCESS_TOKEN = 'https://api.twitter.com/oauth/access_token';
    const URL_ACCOUNT_DATA = 'https://api.twitter.com/1.1/users/show.json';
    const URL_AUTHORIZE = 'https://api.twitter.com/oauth/authorize';
    const URL_REQUEST_TOKEN =  'https://api.twitter.com/oauth/request_token';
    const CONSUMER_KEY = 'gafTWTbtuimV1uhtpTexRNMIs';
    const CONSUMER_SECRET = 'XqHYWOwzlA4vGIMp0PbOXmGOkHEn9swLwoBJG0P7EQaQ2wTk5v';
    const REDIRECT_URI = '/login/?auth_type=twitter';
    const OAUTH_VERSION = '1.0';

    protected $config_fields = array(
        'consumer_key' => 'Consumer Key',
        'consumer_secret' => 'Consumer Secret'
    );

    public function getAuthLink(){
        return self::URL_AUTHORIZE . '?oauth_token=' . $this->getRequestToken();
    }

    protected function setNetworkConfig($config_data){
        if (!empty($config_data)){
            $this->consumer_key = $config_data['consumer_key'];
            $this->consumer_secret = $config_data['consumer_secret'];
            return $config_data['enable'] ? TRUE : FALSE;
        } else {
            return FALSE;
        }
    }

    private function getRequestToken(){
        $params = array(
            'oauth_callback' => self::getSiteUrlForAuthLink() . self::REDIRECT_URI,
        );
        $response = self::executeCurlRequest(self::URL_REQUEST_TOKEN . '?' . http_build_query($this->getRequestParams($params, self::URL_REQUEST_TOKEN)));
        parse_str($response, $response);
        if (!empty($response['oauth_token']) && !empty($response['oauth_token_secret'])){
            $s = Builder::getInstance()->getCurrentSession();
            $s->set('oauth_token', $response['oauth_token']);
            $s->set('oauth_token_secret', $response['oauth_token_secret']);
        }
        return !empty($response['oauth_token']) ? $response['oauth_token'] : NULL;
    }

    /**
     * формирует параметры запроса с подписью
     * @param array $params массив уникальных аттрибутов запроса
     * @param string $url URL на который будет отправляться запрос
     * @param string $secret_key oauth_token_secret, нужен для запросов, требующих предварительного получения токена
     * @param string $method POST, GET
     * @return mixed
     */
    private function getRequestParams(array $params, $url, $secret_key = '', $method = 'GET'){
        // Добавляем к параметрам запроса аттрибуты подписи
        $params['oauth_consumer_key'] = $this->consumer_key;
        $params['oauth_nonce'] = md5(uniqid(rand(), true));
        $params['oauth_signature_method'] = 'HMAC-SHA1';
        $params['oauth_timestamp'] = time();
        $params['oauth_version'] = self::OAUTH_VERSION;
        ksort($params); // Параметры должны идти в алфавитном порядке
        $oauth_base_text = $method . '&' . urlencode($url) . '&' . urlencode(http_build_query($params));
        // Генерируем подпись
        // ключ шифрования состоит из секретного ключа приложения, амперсанда, и,
        // для запросов к api, oauth_token_secret, получаемого при аутентификации
        $params['oauth_signature'] = base64_encode(hash_hmac('sha1', $oauth_base_text, $this->consumer_secret.'&'.$secret_key, true));
        return $params;
    }

    public function getUserData(array $request_data, &$error = NULL){
        if (empty($request_data['oauth_token']) || empty($request_data['oauth_verifier'])){
            $error = 'oauth error';
            return NULL;
        }
        $params = $this->getRequestParams(array(
            'oauth_token' => $request_data['oauth_token'],
            'oauth_verifier' => $request_data['oauth_verifier']
        ), self::URL_ACCESS_TOKEN);
        $result = $this->executeCurlRequest(self::URL_ACCESS_TOKEN . '?' . http_build_query($params));
        parse_str($result, $result);
        $params = $this->getRequestParams(array(
            'oauth_token' => $result['oauth_token'],
            'user_id' => $result['user_id']
        ), self::URL_ACCOUNT_DATA, $result['oauth_token_secret']);
        $result = json_decode(self::executeCurlRequest(self::URL_ACCOUNT_DATA . '?' . http_build_query($params)), true);
        return $this->makeUserData($result);
    }

    private function makeUserData($user_data){
        $name_pieces = array_filter(explode(' ', $user_data['name']));
        return array(
            'network' => self::NETWORK_KEY,
            'identity' => $user_data['id'],
            'email' => !empty($user_data['email']) ? $user_data['email'] : NULL,
            'name' => count($name_pieces) == 2 ? $name_pieces[0] : $user_data['name'],
            'surname' => count($name_pieces) == 2 ? $name_pieces[1] : $user_data['name']
        );
    }

} 