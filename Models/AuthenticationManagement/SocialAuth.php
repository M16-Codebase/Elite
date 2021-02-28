<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 02.09.14
 * Time: 15:13
 */

namespace Models\AuthenticationManagement;

abstract class SocialAuth {
    protected static $i = NULL;

    protected $client_id = NULL;
    protected $client_secret = NULL;
    protected $enable = FALSE;

    protected $config_fields = array();

    private static $allowed_networks = array(
        VkAuth::NETWORK_KEY => 'VkAuth',
        FacebookAuth::NETWORK_KEY => 'FacebookAuth',
        OdnoklassnikiAuth::NETWORK_KEY => 'OdnoklassnikiAuth',
        TwitterAuth::NETWORK_KEY => 'TwitterAuth',
        GoogleAuth::NETWORK_KEY => 'GoogleAuth'
    );

    /**
     * @param $network_key
     * @return SocialAuth
     */
    final public static function getAuthModule($network_key){
        return !empty(self::$allowed_networks[$network_key]) ? call_user_func(__NAMESPACE__ . '\\' . self::$allowed_networks[$network_key] . '::getInstance') : NULL;
    }

    /**
     * Возвращает список поддерживаемых сетей и их параметры для конфига
     * @return array
     */
    final public static function getNetworksConfigList(){
        $result = array();
        foreach(self::$allowed_networks as $key => $class_name){
            $result[$key] = self::getAuthModule($key)->getConfigFieldsList();
        }
        return $result;
    }

    /**
     * @return static
     */
    public static function getInstance(){
        if (empty(static::$i)){
            static::$i = new static();
        }
        return static::$i;
    }

    private function __construct(){
        $config_data = \Models\TechnicalConfig::getInstance()->get(static::NETWORK_KEY, \Modules\Site\Config::PARAMS_KEY_SOCIAL_AUTH);
        $this->enable = $this->setNetworkConfig($config_data);
    }

    protected function setNetworkConfig($config_data){
        if (!empty($config_data)){
            $this->client_id = $config_data['client_id'];
            $this->client_secret = $config_data['client_secret'];
            return $config_data['enable'] ? TRUE : FALSE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return bool
     */
    public function isEnable(){
        return $this->enable;
    }

    abstract public function getAuthLink();

    public function getConfigFieldsList(){
        return array(
            'name' => static::NETWORK_NAME,
            'fields' => $this->config_fields
        );
    }

    public static function getSocialLinksList(){
        $result = array();
        foreach(self::$allowed_networks as $network => $class_name){
            /** @var SocialAuth $i */
            $i = call_user_func(__NAMESPACE__ . '\\' . $class_name . '::getInstance');
            if ($i->isEnable()){
                $result[$network] = $network;
            }
        }
        return $result;
    }

    abstract public function getUserData(array $request_data, &$error = NULL);

    protected static function executeCurlRequest($url, $post_data = array()){
        $c = curl_init($url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_HEADER, 0);
        if (!empty($post_data)){
            curl_setopt($c, CURLOPT_POST, true);
            curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($post_data));
        }
        curl_setopt($c, CURLOPT_HTTPHEADER,array('Content-Type: application/x-www-form-urlencoded'));
        $result = curl_exec($c);
        curl_close($c);
        return $result;
    }

    protected static function getSiteUrlForAuthLink(){
        static $url = NULL;
        if (empty($url)){
            $url = 'http://' . \LPS\Config::getParametr('site', 'url');
        }
        return $url;
    }

} 