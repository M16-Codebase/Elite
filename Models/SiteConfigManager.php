<?php
/**
 * @TODO избавиться от этого класса, пусть выводится сразу айтем
 * перенести checkFlag в CatalogPosition
 * get - не нужен, т.к. и так при обращении к айтему мы заберем то же самое
 * Настройки сайта
 *
 * @author pochka
 */
namespace Models;

use Models\CatalogManagement\Positions\Settings;
use Models\CatalogManagement\Properties;

class SiteConfigManager implements \ArrayAccess{
	const KEY_SITE_BROKEN = 'slomalsja';
    /**
     * @var \Models\CatalogManagement\Positions\Settings
     */
	private $siteConfig = NULL;
    /**
     * @var \Models\CatalogManagement\Positions\Settings[]
     */
    private $siteConfigByType = array();

	private static $instance;
    private $segment_id;

    /**
     *
     * @param int|null $segment_id
     * @return SiteConfigManager
     */
	public static function getInstance($segment_id = null){
		if (empty(self::$instance)){
			self::$instance = new SiteConfigManager($segment_id);
		}
		return self::$instance;
	}
	public function __construct($segment_id){
        $this->segment_id = $segment_id;
		$this->siteConfig = CatalogManagement\Positions\Settings::getConfigByKey(\App\Configs\CatalogConfig::CONFIG_GLOBAL_KEY, $segment_id);
        $this->siteConfigByType[\App\Configs\CatalogConfig::CONFIG_GLOBAL_KEY] = $this->siteConfig;
	}

    /**
     * @param string $type
     * @return \Models\CatalogManagement\Positions\Settings
     * @throws \Exception
     */
    private function getConfigByType($type){
        if (!empty($type)){
            if (empty($this->siteConfigByType[$type])){
                $this->siteConfigByType[$type] = CatalogManagement\Positions\Settings::getConfigByKey($type, $this->segment_id);
            }
            return $this->siteConfigByType[$type];
        } else {
            return $this->siteConfig;
        }
    }

    /**
     *
     * @param string $key если не задан, вернёт весь массив значений
     * @param string|null $type — ключ конфига
     * @param int|null $segment_id
     * @param string $data_type — тип данный проперти (при создании, в остальных случаях игнорируется)
     * @return mixed|Settings
     * @throws \ErrorException
     */
	public function get($key = NULL, $type = NULL, $segment_id = NULL, $data_type = Properties\String::TYPE_NAME){
        $config = $this->getConfigByType($type);
        if (empty($segment_id) && \LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE) {
            $segment_id = $config['segment_id'];
        }
        if (!empty($key)){
            if (empty($config)) {
                return NULL;
            } elseif (empty($config['properties'][$key])) {
                $this->set($key, NULL, $segment_id, $data_type, $type);
                $config = $this->getConfigByType($type);
            }
            if (!empty($config['properties'][$key])){
                $segment_properties = $config->getSegmentProperties($segment_id);
                /** @var Properties\Property $property */
                $property = $config['properties'][$key]['property'];
                return !empty($segment_properties[$key]) ? $property->getCompleteValue($segment_properties[$key], $segment_id) : NULL;
            } else {
                return NULL;
            }
        }
		return $config;
	}

    /**
     * Устанавливает значение параметра каталога, при необходимости создает пропертю
     * @param string $key — ключ свойства
     * @param string|int|float $value — значение свойства
     * @param int|null $segment_id
     * @param string $data_type — тип данных свойства
     * @param string|null $config_key — ключ конфига, если пустой — используется глобальный конфиг
     * @return SiteConfigManager
     * @throws \ErrorException
     * @throws \Exception
     */
    public function set($key, $value, $segment_id = NULL, $data_type = Properties\String::TYPE_NAME, $config_key = NULL){
        $config = $this->getConfigByType($config_key);
        if (empty($config)) {
            throw new \ErrorException("Не найден конфиг с ключом #${config_key}");
        }
        if (empty($config['properties'][$key])){
            $property_params = array('title' => $key, 'key' => $key, 'data_type' => $data_type, 'type_id' => $config->getType()->getId());
            if (!empty($segment_id)){
                $property_params['segment'] = 1;
            }
            Properties\Property::create($property_params, $e);
            Settings::clearCache($config['id']);
            Properties\Factory::clearSearchDataCache();
            $config = Settings::getById($config['id']);
            $this->siteConfigByType[$config['key']] = $config;
            if ($config['key'] == \App\Configs\CatalogConfig::CONFIG_GLOBAL_KEY) {
                $this->siteConfig = $config;
            }
        }
        $segment_properties = $config->getSegmentProperties($segment_id);
        $val_id = !empty($segment_properties[$key]) ? $segment_properties[$key]['val_id'] : NULL;
        $old_value = !empty($segment_properties[$key]) ? $segment_properties[$key]['value'] : NULL;
        $params = array('val_id' => $val_id, 'value' => $value);
        if (empty($value) && !empty($val_id)) {
            $params['options'] = array('delete' => 1);
            $params['value'] = $old_value;
        }
        $params = array($key => array($params));
        $config->updateValues($params, $errors, $segment_id);
        if (!empty($errors)) {
            throw new \ErrorException("Не удалось сохранить свойство #${key} в конфиге #${config['key']}, errors: " . var_export($errors, true));
        }
        return $this;
    }

    public function checkFlag($key, $type = NULL){
        $config = $this->getConfigByType($type);
        return !empty($config) ? !empty($config['properties'][$key]['value']) : NULL;
    }
    /**
     * @TODO что это и зачем нужно?
     * @param mixed $key
     * @return mixed
     */
    public function getParamType($key){
        $config = $this->get();
        return isset($config[$key]) ? $config[$key]['type'] : NULL;
    }
    
    /******************************* ArrayAccess *****************************/
    /**
     * @TODO возможно имеет смысл отдавать не параметр, а сам конфиг ($offset = ключ типа)
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists ($offset){
        return isset($this->siteConfig[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed
     * @throws \Exception
     */
    public function offsetGet ($offset){
		return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws \Exception
     */
    public function offsetSet ($offset, $value){
        throw new \Exception(get_class($this).' has only immutable Array Access');
    }

    /**
     * @param mixed $offset
     * @throws \Exception
     */
    public function offsetUnset ($offset){
        throw new \Exception(get_class($this).' has only immutable Array Access');
    }
}