<?php
/**
 * Настройки сайта
 *
 * @author pochka
 */
namespace Models;
class TechnicalConfig implements \ArrayAccess{
    const KEY_SITE_BROKEN = 'slomalsja';
    const TABLE = 'config';

    const FIELD_TYPE_TEXT = 'text';
    const FIELD_TYPE_CHECKBOX = 'checkbox';
    const FIELD_TYPE_TEXTAREA = 'textarea';
    const FIELD_TYPE_RADIO = 'radio';
    const FIELD_TYPE_SELECT = 'select';
    const FIELD_TYPE_SERIALIZED = 'serialized';

    private $siteConfig = array();
    private $siteConfigByType = array();

    private static $instance;
    /**
     *
     * @return self
     */
    public static function getInstance(){
        if (empty(self::$instance)){
            self::$instance = new TechnicalConfig();
        }
        return self::$instance;
    }
    private $db = null;
    public function __construct(){
        $this->db = \App\Builder::getInstance()->getDB();
    }
    /**
     *
     * @param string $key если не задан, вернёт весь массив значений
     * @return null|array
     */
    public function get($key = NULL, $type = NULL){
        if (empty($this->siteConfig)){
            $this->siteConfigByType = $this->db->query('SELECT `key`, `description`, `value`, `type`, `data_type` FROM `'.self::TABLE.'`')->select('type', 'key');
            $this->siteConfig = array();
            foreach($this->siteConfigByType as $config){
                $this->siteConfig = array_merge($this->siteConfig, $config);
            }
        }
        if (isset($key)){
            if (isset($this->siteConfig[$key])){
                return ($this->siteConfig[$key]['data_type'] == 'serialized') ? unserialize($this->siteConfig[$key]['value']) : $this->siteConfig[$key]['value'];
            }
            return NULL;
        }
        if (!empty($type)){
            return isset($this->siteConfigByType[$type]) ? $this->siteConfigByType[$type] : NULL;
        }
        return $this->siteConfig;
    }

    public function getParamType($key){
        $config = $this->get();
        return isset($config[$key]) ? $config[$key]['type'] : NULL;
    }

    /**
     *
     * @param string $key ключ параметра
     * @param string $type раздел конфига (basic, notification, contacts, seo)
     * @param string $value значение
     * @param string $description комментарий
     * @param string $data_type тип данных (text,checkbox,textarea,radio,select,serialized)
     * @param null $error
     * @return bool
     */
    public function set($key, $type, $value, $description = '', $data_type = 'text', &$error = NULL){
        if (empty($key)){
            $error = 'Ключ должен быть заполнен';
            return FALSE;
        }
        if (preg_match('~[^_a-zA-Z0-9]~', $key)){
            $error = 'Неверный формат ключа. Можно использовать только латинские буквы, цифры и нижнее подчеркивание';
            return FALSE;
        }
        $site_config = $this->get();
        $old_data = !empty($site_config[$key]) ? $site_config[$key] : NULL;
        $value = ($data_type == 'serialized') ? serialize($value) : $value;
        $this->db->query('REPLACE INTO `'.self::TABLE.'` SET `key`=?s, `type`=?s, `value`=?s, `description`=?s, `data_type` = ?s', $key, $type, $value, $description, $data_type);
        if (empty($old_data)){
            Logger::add(array(
                'type' => Logger::LOG_TYPE_CREATE,
                'entity_type' => 'config',
                'entity_id' => 0,
                'attr_id' => $key,
                'additional_data' => array('key' => $key, 'value' => $value, 'description' => $description, 'type' => $type)
            ));
        } else {
            if ($value != $old_data['value'] || $description != $old_data['description']){
                $additional_data = array(
                    'key' => $key,
                    'type' => $type
                );
                if ($value != $old_data['value']){
                    $additional_data['value'] = array(
                        'old' => $old_data['value'],
                        'new' => $value
                    );
                }
                if ($description != $old_data['description']){
                    $additional_data['description'] = array(
                        'old' => $old_data['description'],
                        'new' => $description
                    );
                }
                Logger::add(array(
                    'type' => Logger::LOG_TYPE_EDIT,
                    'entity_type' => 'config',
                    'entity_id' => 0,
                    'attr_id' => $key,
                    'additional_data' => $additional_data
                ));
            }
        }
        return TRUE;
    }
    /**
     *
     * @param string $key
     * @return bool
     */
    public function del($key){
        $site_config = $this->get();
        $additional_data = !empty($site_config[$key]) ? $site_config[$key] : NULL;
        if ($this->db->query('DELETE FROM `'.self::TABLE.'` WHERE `key`=?', $key)){
            Logger::add(array(
                'type' => Logger::LOG_TYPE_DEL,
                'entity_type' => 'config',
                'entity_id' => 0,
                'attr_id' => $key,
                'additional_data' => $additional_data
            ));
            return TRUE;
        }
        return FALSE;
    }

    /******************************* ArrayAccess *****************************/

    public function offsetExists ($offset){
        $value = $this->get($offset);
        if (!empty($value)){
            return true;
        }
        return null;
    }

    /**
     * @param string $offset
     * @return mixed
     * @throws \Exception
     */
    public function offsetGet ($offset){
        return $this->get($offset);
    }

    public function offsetSet ($offset, $value){
        throw new \Exception(get_class($this).' has only immutable Array Access');
    }

    public function offsetUnset ($offset){
        throw new \Exception(get_class($this).' has only immutable Array Access');
    }
}