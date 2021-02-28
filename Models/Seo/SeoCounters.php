<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 22.04.15
 * Time: 15:51
 */

namespace Models\Seo;


abstract class SeoCounters implements \ArrayAccess {
    const PARAMS_KEY = '';
    const CODE_KEY = '';
    const PARAMS_DESCRIPTION = '';
    const CODE_DESCRIPTION = '';
    protected static $i = null;
    /**
     * @var \Models\SiteConfigManager
     */
    protected $config = null;
    protected $counter_params = NULL;

    protected $public_params = array();

    /**
     * @return static
     */
    public static function getInstance(){
        if (empty(static::$i)){
            static::$i = new static();
        }
        return static::$i;
    }

    protected function __construct(){
        $this->config = \Models\TechnicalConfig::getInstance();
        $this->counter_params = $this->getParams();
    }

    public function getParams(){
        return $this->config->get(static::PARAMS_KEY);
    }

    public function getCode(){
        return $this->config->get(static::CODE_KEY);;
    }

    public function update($params, &$errors = array()){
        if ($this->validate($params, $errors)){
            $this->config->set(static::PARAMS_KEY, 'seo', $params, static::PARAMS_DESCRIPTION, \Models\TechnicalConfig::FIELD_TYPE_SERIALIZED);
            $config_data = $this->compile($params);
            if (!empty($config_data)){
                foreach($config_data as $k=>$v){
                    $this->config->set($k, 'seo', $v, static::PARAMS_DESCRIPTION, \Models\TechnicalConfig::FIELD_TYPE_TEXTAREA);
                }
            }
        }
    }

    /**
     * @param string $prefix
     * @param array $errors
     * @param array $out_errors
     */
    public static function prepareErrors($prefix, $errors, &$out_errors = array()){
        foreach($errors as $k=>$v){
            $out_errors["{$prefix}[{$k}]"] = $v;
        }
    }

    /**
     * Проверка валидности параметров
     * @param array $params
     * @param array $errors
     * @return bool
     */
    abstract protected function validate($params, &$errors);

    /**
     * Компиляция кода счетчика
     * @param array $params
     * @return string[]
     */
    abstract protected function compile($params);

    public function getData($offset){
        if ($offset == static::CODE_KEY){
            return $this->getCode();
        } else {
            throw new \LogicException('No key ' . $offset . ' in ' . __CLASS__);
        }
    }

    /* ****************************** ArrayAccess **************************** */

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return isset($this->public_params[$offset]);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        $data = $this->getData($offset);
        return $data;
    }

    /**
     * @param string $offset
     * @param mixed $value
     * @throws \Exception
     */
    public function offsetSet($offset, $value) {
        throw new \Exception(get_class($this) . ' has only immutable Array Access');
    }

    /**
     * @param string $offset
     * @throws \Exception
     */
    public function offsetUnset($offset) {
        throw new \Exception(get_class($this) . ' has only immutable Array Access');
    }
}