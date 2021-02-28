<?php
/**
 * Класс настроек. Менять название надо очень аккуратно, много где используется прямое обращение к классу
 * User: Charles Manson
 * Date: 07.05.15
 * Time: 16:16
 */

namespace Models\CatalogManagement\Positions;


use App\Configs\CatalogConfig;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type;
use App\Configs\SharedMemoryConfig;

class Settings extends Item{
    private static $configs_cache = array();
    /**
     * Свои хелперы
     * @var array
     */
    static protected $dataProviders = array();
    static protected $dataProvidersByFields = array();

    /**
     * @param int $type_id
     * @param int $status
     * @param array $propValues
     * @param null $errors
     * @param null $segment_id
     * @param null $parent_id — ignored
     * @return int
     * @throws \Exception
     */
    public static function create($type_id, $status = self::S_TMP, $propValues = array(), &$errors = NULL, $segment_id = NULL, $parent_id = NULL, $insertId = NULL){
        $result = CatalogSearch::factory(CatalogConfig::CONFIG_KEY)->setTypeId($type_id)->searchItemIds();
        if ($result->count()){
            $type = Type::getById($type_id);
            throw new \LogicException("Конфиг «${type['title']}» уже существует");
        }
        return parent::create($type_id, $status, $propValues, $errors, $segment_id);
    }

    /**
     * @param $config_key
     * @param int|null $segment_id
     * @return Settings|NULL
     * @throws \Exception
     */
    public static function getConfigByKey($config_key, $segment_id = null){
        if (empty(self::$configs_cache[$config_key])){
            $keys = self::getCachedKeys();
            if (!empty($keys) && !empty($keys[$config_key])){
                $configs = Settings::factory($keys, $segment_id);
                $config = $configs[$keys[$config_key]];
            }else{
                $type = self::getConfigType($config_key, $segment_id);
                if (empty($type)){
    //                throw new \Exception("Неизвестный конфиг ${config_key}");
                    return NULL; // иначе невозможно автосоздание типов конфига
                }
                $config = CatalogSearch::factory(CatalogConfig::CONFIG_KEY, $segment_id)->setTypeId($type['id'])->searchItems()->getFirst();
                if (empty($config)){
                    $config_id = self::create($type['id'], self::S_PUBLIC, array(), $errors);
                    $config = self::getById($config_id);
                    $config->update(array('status'=>self::S_PUBLIC), array(), $errors, $segment_id);
                }
                $keys[$config_key] = $config['id'];
                self::setCachedKeys($keys);
            }
            self::$configs_cache[$config_key] = $config;
        }
        return self::$configs_cache[$config_key];
    }
    /**
     * Загружаем данные из кэша (сопоставление ключа каталога и id айтема)
     * @return void|array
     */
    private static function getCachedKeys(){
        //кэш в sharedMemory
        $memory_key = self::getMemoryKey();
        if (empty($memory_key)){
            return;
        }
        $data = \App\Builder::getInstance()->getSharedMemory()->get(self::getMemoryKey());
        \LPS\Components\Benchmark::get()->log('Get setting keys from shared memory ' . json_encode($data, JSON_UNESCAPED_UNICODE));
        return $data;
    }
    /**
     * Запихиваем данные в кэш (сопоставление ключа каталога и id айтема)
     * @param type $keys
     * @return type
     */
    public static function setCachedKeys($keys){
        //кэш в sharedMemory
        $memory_key = self::getMemoryKey();
        if (empty($memory_key)){
            return;
        }
        \App\Builder::getInstance()->getSharedMemory()->set(self::getMemoryKey(), NULL, $keys);
    }
    /**
     * взять ключ для обращения к памяти
     */
    private static function getMemoryKey(){
        return SharedMemoryConfig::getEntityKey(SharedMemoryConfig::SHM_KEY_SITE_CONFIG);
    }

    /**
     * @param $config_key
     * @param int|null $segment_id
     * @return mixed
     */
    private static function getConfigType($config_key, $segment_id){
        $catalog = Type::getByKey(CatalogConfig::CONFIG_KEY, Type::DEFAULT_TYPE_ID, $segment_id);
        $types = Type::search(array('key' => $config_key, 'parent_id' => $catalog['id'], 'allow_children' => 0), $segment_id);
        return reset($types);
    }

    /**
     * @param string $offset
     * @return mixed
     * @throws \Exception
     */
    public function offsetGet($offset){
        if ($offset == 'title') {
            $type = $this->getType();
            return $type['title'];
        }
        return parent::offsetGet($offset);
    }

    public function offsetExists ($offset){
        if ($offset == 'title'){
            return TRUE;
        }
        return parent::offsetExists($offset);
    }

}