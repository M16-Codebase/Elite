<?php
/**
 * Класс предметной области каталога (позиция каталога), служит для доступа и работы на чтение. Для особых манипуляций используется класс Catalog
 */
namespace Models\CatalogManagement;
use App\Configs\SeoConfig;
use Models\CatalogManagement\Properties\Factory AS PropertyFactory;
use Models\CatalogManagement\Properties\Property;
use Models\Seo\PageRedirect;
use Models\Validator;

abstract class CatalogPosition implements \ArrayAccess{
    const MAX_REGISTRY_LENGHT = 200;
    /**
     * Разрешается ли SQL кеширование
     */
    const CACHE_ENABLE = TRUE;
    const MAX_CACHE_STORE_COUNT = 1000;
    /**
     * констатны статусов
     */
    const S_TMP    = 1;
    const S_HIDE   = 2;
    const S_PUBLIC = 3;
    const S_DELETE = 4;
    const S_TEMPORARY_HIDE   = 5;
    /**
     * Имя основной таблицы
     */
    const TABLE = 'UNDECLARE';
	const TABLE_PROP_INT = 'UNDECLARE';
	const TABLE_PROP_FLOAT = 'UNDECLARE';
	const TABLE_PROP_STRING = 'UNDECLARE';
    /**
     * Таблица с кэшем
     */
    const TABLE_DATA_CACHE = 'catalog_data_cache';
    /**
     * Наименование поля id позиции (item_id или variant_id)
     */
	const TABLE_PROP_OBJ_ID_FIELD = 'UNDECLARE';
    /**
     * Наименование поля, в пределах которого номер сортировки должен быть уникальным для каждой позиции
     */
    const UNIQUE_POSITION_IN_FIELD = 'UNDECLARE';
    /**
     * фильтр свойств для объекта
     */
    const PROPERTY_FILTER = 'UNDECLARE';
    /**
     * Самоопределение вариант это или айтем
     */
    const CATALOG_IDENTITY_KEY = 'UNDECLARE';
    /**
     * ключ свойства, значение которого - наименование сущности
     */
    const ENTITY_TITLE_KEY = 'UNDECLARE';
    /**
     * Идентификатор
     * @var int
     */
    protected $id = null;
    /**
     * Внешние характеристики позиции
     * @var array[] ( property_key => array('id' => int, 'value' => mixed, 'complete_value' => string))
     */
    protected $properties = array();
    /**
     * Характеристики, разбитые по сегментам @see $this->makePropValue()
     * @var array
     */
    protected $propertiesBySegment = array();
    /**
     * Основные технические параметры, такие как статус, дата и т.д.
     * @var array
     */
    protected $data = array();
    /**
     * Сегмент по умолчанию (для вывода свойств)
     * @var int
     */
    protected $segment_id = NULL;
    /**
     * Записываем в эту переменную, надо ли сохранять сущность
     * @var boolean 
     */
    protected $need_save = NULL;
    /**
     * Проверка того, что свойства только что были проверены при создании, 
     * и при редактировании с теми же свойствами ничего проверять не надо
     * @var array (
     *      check - строка md5 от сериализованного массива значений свойств 
     *              + id только что созданного объекта + Item или Variant
     *      propKeys - key свойств, которые надо изменить
     *      errors - список ошибок
     * )
     */
    static private $props_checked = array();
    /**
     * Разрешенные параметры для различных сущностей системы
     * @see Catalog::checkAllowed()
     */
    static protected $allowParams = array(
        'recreate_view',
        'status', //@see const S_*
    );
    /**
      * Указатель на базу
	  * @var \MysqlSimple\DbSimple_Mysql
	  */
	static protected $db = null;
    /**
     * Колекция уже прочитанных данных. нужна для того чтобы можно было создать объекты быстро
     * @var array[] (data rows)
     */
    static protected $dataCache = array();
    /**
	 * сбор данных для записи в кэш, чтобы сохранить пачкой
	 * @var type 
	 */
	protected static $cacheDataToSave = NULL;

    static protected $loadFields = array();
    /**
     * Переменная, которая указывает, можно ли чистить в данный момент кэш
	 * используется self:: т.к. в редактировании Items могут принимать участие Variants
	 * поэтому при редактировании объекта кэш не чистится для всех типов объектов
     * @var bool
     */
    private static $clearCache = TRUE;

    /**
     * Колекция уже созданных CatalogPosition, чтобы они не терялись
     * @var CatalogPosition[]
     */
    static protected $registry = array();                   //переопределяемая переменная в классе наследнике
    static protected $dataProviders = array();              //переопределяемая переменная в классе наследнике
    static protected $dataProvidersByFields = array();      //переопределяемая переменная в классе наследнике
    /**
     * Проверка отдельного значения свойства в конкретном типе и сегменте 
     * (при массовом редактировании сэкономит время)
     * @var array 
     */
    static protected $checkedProperty = array();
    /**
     * Массив id шников которые желательно прочитать в кеш "за компанию, на всякий случай",
     * но не создавать из них объектов, когда будет какое-либо первое чтение данных.
     * Этот механизм позволяет накидать много id и считать их единым махом,
     * минимизируя таким образом будующие запросы в базу.
     *
     * @param int|int[] $ids
     * @param bool $clear
     * @throws \Exception
     */
    final public static function prepare($ids, $clear = FALSE){
        if (!is_array($ids) && !is_numeric($ids)){
            throw new \Exception('incorrect var type - $ids (int[] or int)');
        }
        if ($clear)
            static::$ids4Load[$ids] = array();
        if (!is_array($ids)){
            static::$ids4Load[$ids] = $ids;
        }else{
            foreach ($ids as $id){
                if (is_array($id)){
                    throw new \Exception('Передаваемый id не может быть массивом');
                }
                static::$ids4Load[$id] = $id;
            }
        }
    }
    /**
     * @param array $ids
     * @return array
     */
    final protected static function calculateCachedIdsList(array $ids){
        foreach ($ids as $id){
            if (is_array($id)){
                throw new \Exception('Передаваемый id не должен быть массивом');
            }
        }
        // объединяем запрашиваемое с тем что желательно тоже надо подготовить
        if (count($ids) + count(static::$ids4Load) < static::MAX_REGISTRY_LENGHT){
            $ids = array_unique(array_merge(static::$ids4Load, $ids));
            static::$ids4Load = array(); //отчистили
        }
        return array_diff($ids, array_keys(static::$dataCache), array_keys(static::$registry));
    }
    /**
     * Чтение кешированных данных
     * @param int[] $ids
     * @return int[]
     */
    final protected static function loadCachedData($ids){
        if (!static::CACHE_ENABLE)
            return $ids;
        if (empty($ids)){
            return array();
        }
        $cacheList = \App\Builder::getInstance()->getDB()->query(''
            . 'SELECT `id`, `cache` FROM `'.static::TABLE_DATA_CACHE.'` WHERE `id` IN (?i) AND `type` = "'.static::CATALOG_IDENTITY_KEY.'" '
            . '', $ids)->getCol('id', 'cache');
        foreach ($ids as $k => $id){
            if (isset($cacheList[$id])){
                $data = json_decode($cacheList[$id], TRUE);
                if (!empty($data)){
                    static::$dataCache[$id] = array('main' => NULL, 'properties' => NULL, 'cache' => $data);
                    unset($ids[$k]);
                }
            }
        }
        return $ids;
    }
    /**
     * упаковываем для кэша
     * @param type $data
     * @return type
     */
    final protected static function cachePack($data){
        $data_to_cache = array();
        foreach ($data as $s_id => $s_d){
            foreach ($s_d as $p_key => $d){
                if (!is_null($d['value']) && !is_null($d['val_id'])){
                    $data_to_cache[$s_id][$p_key] = array_values($d);
                }
            }
        }
        return $data_to_cache;
    }
    /**
     * распаковываем из кэша
     * @param type $data
     * @return type
     */
    final protected static function cacheUnpack($data){
        foreach ($data as &$s_d){
            foreach ($s_d as &$d){
                $d = self::propValuesKeys($d);
            }
        }
        return $data;
    }
    /**
     * Строка для определения те же свойства используются для создания и редактирования или нет
     * @param array $propValues
     * @param string $class
     * @param int $id
     * @return string
     */
    final protected static function getPropsCheckerString($propValues, $class, $id){
        return md5(serialize($propValues) . $class . $id);
    }
    /**
     * возвращает значения всех свойств
     * @param int[] $ids айди вариантов | айтемов (зависит от контекста)
     * @return array('item_id | variant_id' => values)
     */
    final protected static function getPropertiesFromDB(array $ids){
        $db = \App\Builder::getInstance()->getDB();
        $result = $db->query('
				SELECT `str_values`.`'.static::TABLE_PROP_OBJ_ID_FIELD.'` AS `obj_id`, `properties`.`key`,
					IF(`str_values`.`segment_id` IS NULL, 0, `str_values`.`segment_id`) AS `segment_id`,
					`str_values`.`value`, `str_values`.`id`, `str_values`.`position`
				FROM   `'.static::TABLE_PROP_STRING.'` AS `str_values`
				INNER JOIN `'.PropertyFactory::TABLE.'` AS `properties` ON `properties`.`id`=`str_values`.`property_id`
				WHERE  `str_values`.`'.static::TABLE_PROP_OBJ_ID_FIELD.'` IN (?i)
            UNION
				SELECT `int_values`.`'.static::TABLE_PROP_OBJ_ID_FIELD.'` AS `obj_id`, `properties`.`key`,
					IF(`int_values`.`segment_id` IS NULL, 0, `int_values`.`segment_id`) AS `segment_id`,
					`int_values`.`value`, `int_values`.`id`, `int_values`.`position`
				FROM   `'.static::TABLE_PROP_INT.'` AS `int_values`
				INNER JOIN `'.PropertyFactory::TABLE.'` AS `properties` ON `properties`.`id`=`int_values`.`property_id`
				WHERE  `int_values`.`'.static::TABLE_PROP_OBJ_ID_FIELD.'` IN (?i)
            UNION
				SELECT `float_values`.`'.static::TABLE_PROP_OBJ_ID_FIELD.'` AS `obj_id`, `properties`.`key`,
					IF(`float_values`.`segment_id` IS NULL, 0, `float_values`.`segment_id`) AS `segment_id`,
					`float_values`.`value`, `float_values`.`id`, `float_values`.`position`
				FROM   `'.static::TABLE_PROP_FLOAT.'` AS `float_values`
				INNER JOIN `'.PropertyFactory::TABLE.'` AS `properties` ON `properties`.`id`=`float_values`.`property_id`
				WHERE  `float_values`.`'.static::TABLE_PROP_OBJ_ID_FIELD.'` IN (?i)
			ORDER BY `position`',
            $ids, $ids, $ids
        )->select('obj_id', 'segment_id', 'key', 'id');
        return $result;
    }
    
    public static function getPropertyValueByPositionId(Properties\Property $property, $ids){
        if ($property['set'] == 1){
            throw new LogicExeption('Нельзя использовать множественное свойство');
        }
        $db = \App\Builder::getInstance()->getDB();
        return $db->query('SELECT `'.static::TABLE_PROP_OBJ_ID_FIELD.'` AS `position_id`, `value` FROM `'.$property['table'].'` WHERE `'.static::TABLE_PROP_OBJ_ID_FIELD.'` IN (?i)', $ids)->getCol(`position_id`, `value`);
    }

    /**
     * Фабрика объектов static::
     * @param int[] $ids
     * @return CatalogPosition[]
     */
    public static function factory($ids, $segment_id = NULL){
        $result = array();
        if (self::$clearCache && count($ids) + count(static::$registry) > static::MAX_REGISTRY_LENGHT){
            static::$registry = array(); //объекты не уничтожаются, а просто "отпускаются" и не лежат в реестре, так что сборщик мусора смог бы их убить все если они не используются более
        }
        static::cacheUpData($ids, $segment_id); //данные гаратированно в кеше
        if (empty($ids)){
            return array();
        }
        foreach ($ids as $id){
            if (!array_key_exists($id, static::$registry)){ //isset Неудобно обрабатывает null
                if (!empty(static::$dataCache[$id]['main']) || !empty(static::$dataCache[$id]['cache'])){
                    $type_id = !empty(static::$dataCache[$id]['main']) ? static::$dataCache[$id]['main']['type_id'] : static::$dataCache[$id]['cache'][0]['type_id'];
                    $type = Type::getById($type_id);//уже все в Type::prepare
                    if (empty($type)){
                        continue;
                    }
                    $catalog = $type->getCatalog();
                    $catalogPositionsClass = \App\Configs\CatalogConfig::getEntityClass($catalog['key'], static::CATALOG_IDENTITY_KEY);
                    if (empty($catalogPositionsClass) || !class_exists($catalogPositionsClass)){
                        throw new \Exception('Неверно задан ключ каталога: ' . $catalogPositionsClass);
                    }
                    static::$registry[$id] = new $catalogPositionsClass($id, $segment_id);
                }else{
                    static::$registry[$id] = NULL;
                }
            }
            unset (static::$dataCache[$id]); // данные использованы - объект создан и помещен в коллекцию объектов, более чистые данные не нужены
            /* @var $obj static */
            $obj = static::$registry[$id];
            if (!empty ($obj)){
                if ($obj->isNull()){
                    static::$registry[$id] = null;
                    $obj = null;
                }
            }
            $result[$id] = $obj;
        }
        return $result;
    }

    /**
     * Возвращает конкретный объект
     * @param int $id
     * @return static
     */
    public static function getById($id, $segment_id = NULL){
        if (empty($id)){
            return NULL;
        }
        if (isset(static::$registry[$id]))
            return static::$registry[$id];
        $objects = static::factory(array($id), $segment_id);
        return reset($objects);
    }
    /**
     * Проверяет является ли ключ зарезервированным словом (стандартные поля айтема и варианта + поля хелперов
     * @param string $key
     * @return bool TRUE для зарезервированных слов, FALSE для остальных
     */
    public static function checkIsKeyReserved($key){
        if (in_array($key, static::$loadFields) || !empty(static::$dataProvidersByFields[$key]) || in_array($key, array('collection_ids', 'item', 'properties', 'type_id', 'segment_id', 'timestamp'))){
            return TRUE;
        }
        return FALSE;
    }
    /**
     * Проверка ключа варианта на соответствие формату и уникальность в проеделах айтема/типа
     * @param string $key
     * @param int $item_id в зависимости от сущности либо item_id, либо type_id
     * @param array $err
     * @param int $ignore_id
     * @return bool
     * @throws \MysqlSimple\Exceptions\InvalidArgumentException
     */
    protected function checkKey($key, &$errors = array()){
        static::checkKeyFormat($key, $err);
        if (isset($err['key']) && $err['key'] == \Models\Validator::ERR_MSG_TOO_BIG){
            // слишком длинный ключ обрезаем
            $key = substr($key, 0, SeoConfig::ENTITY_KEY_MAX_LENGTH);
            trim($key, '-_');
            unset($err['key']);
        }
        if (empty($err)){
            $db = \App\Builder::getInstance()->getDB();
            $res = $db->query('SELECT `id` FROM `' . static::TABLE . '` WHERE `'.(($this instanceof Item && !empty($this['parent_id'])) ? 'parent_id' : static::UNIQUE_POSITION_IN_FIELD).'` = ?d AND `key` = ?s AND `id` != ?d',
                ($this instanceof Item && !empty($this['parent_id'])) ? $this['parent_id'] : $this[static::UNIQUE_POSITION_IN_FIELD],
                $key,
                $this['id'])
                ->getCell();
            if (!empty($res)){
                $i = 0;
                do{
                    $i++;
                    if ($i > 9){
                        $errors[] = array(
                            'key' => 'key',
                            'error' => \Models\Validator::ERR_MSG_EXISTS
                        );
                        return $key;
                    }
                } while($db->query('SELECT `id` FROM `' . static::TABLE . '` WHERE `'.(($this instanceof Item && !empty($this['parent_id'])) ? 'parent_id' : static::UNIQUE_POSITION_IN_FIELD).'` = ?d AND `key` = ?s AND `id` != ?d',
                    ($this instanceof Item && !empty($this['parent_id'])) ? $this['parent_id'] : $this[static::UNIQUE_POSITION_IN_FIELD],
                    $key . '_' . $i,
                    $this['id'])
                    ->getCell());
                $key .= '_' . $i;
            }
        } else {
            foreach($err as $k=>$v){
                $errors[] = array(
                    'key' => $k,
                    'error' => $v
                );
            }
        }
        return $key;
    }
    /**
     * Проверка ключа объекта на соответствие формату.
     * ВЫЗЫВАЕТСЯ ТОЛЬКО ИЗ self::checkKey
     * @param string $key
     * @param array $errors
     * @return bool
     */
    private static function checkKeyFormat($key, &$errors = array()){ 
        if (empty($key)){
            $errors['key'] = \Models\Validator::ERR_MSG_EMPTY;
        }elseif (preg_match('~[^a-zA-Z0-9\-_]~', $key)){
            $errors['key'] = \Models\Validator::ERR_MSG_INCORRECT_FORMAT;
        }elseif (strlen($key) > SeoConfig::ENTITY_KEY_MAX_LENGTH) {
            $errors['key'] = \Models\Validator::ERR_MSG_TOO_BIG;
        }
        return empty($errors);
    }
    /**
     * Проверка всех переданных свойств
     * @param int $type_id передаем id типа, чтобы забрать все нужные свойства
     * @param array() $propValues собственно значения для проверки. если передаем propList по id, то и  propValues должно быть по id. то же касается и передачи по key
     * @param array $errors ошибки
     * @param int $segment_id
     * @param int $id id элемента (товара или варианта) передается если элемент уже создан
     * @param int $item_id если создается вариант, то требуется знать, в каком товаре он создается
     * @return boolean
     * @throws \Exception
     */
    protected static function _checkProperties($type_id, &$propValues, &$errors, $segment_id = NULL, $id = NULL, $item_id = NULL, $max_recreate_view = Catalog::MAX_RECREATE_VIEWS_ON_UPDATE){
		if (empty($propValues)){
			return array();
		}
        if (is_null($segment_id)){
            $segment = \App\Segment::getInstance()->getDefault();
            $segment_id = !empty($segment) ? $segment['id'] : 0;
        }
        $propList = static::getProperties($type_id, 'key');
        //если такие свойства уже были прочеканы для id товара\варианта, то незачем проверять их снова  
        $checkString = md5(serialize($propValues) . __CLASS__ . $id . $segment_id);
        if (empty(self::$props_checked) || self::$props_checked['check'] != $checkString){
			$err = array();
            $changedPropKeys = array();//реально изменяемые свойства
            if(!empty($id)){
                $entity = static::getById($id, $segment_id);
            }
            //избавляемся от лишних значений
            $propValues = array_intersect_key($propValues, $propList);
            $segment_properties = !empty($entity) ? $entity->getSegmentProperties($segment_id) : array();
            //проверяем значения свойств
            foreach ($propValues as $pKey => $values){
                $property = $propList[$pKey];
                //надо сравнить порядок (может только его хотят сменить)
                $set_changes = FALSE;
                if ($property['set'] && !empty($entity) && !empty($segment_properties[$pKey])){
                    //сравниваем массивы val_ids, т.к. если у них сменился порядок или они добавились\удалились, тогда массивы не равны => нам это свойство надо обновлять
                    $set_changes = array_keys($segment_properties[$pKey]['value']) != array_column($values, 'val_id');
                }
                $check_set_necessary = $property['set'] && $property['necessary'];//если свойство множественное и обязательное, то надо узнать, не удалили ли все значения
                if ($check_set_necessary){
                    $checked_values = !empty($segment_properties[$pKey]) ? $segment_properties[$pKey]['value'] : array();
                }
                foreach ($values as $v_num => $val){
                    static::checkPropertyValue($property, $val, $err, $segment_id, $id, $type_id);
                    if (!array_key_exists('val_id', $val)){
                        throw new \Exception('Неверный формат передаваемых данных');
                    }
                    //если передан пустой val_id и пустое value, то ничего с этим делать не надо
                    if (empty($val['val_id']) && is_null($val['value'])){
                        continue;
                    }
                    $v_n = FALSE;
                    //отыскиваем id свойств, которые реально надо сохранять
                    if ($property['set'] && !empty($val['val_id']) && !empty($segment_properties[$pKey]['val_id'])){//при повторном сохранении, уже может и не быть значений
                        $v_n = array_search($val['val_id'], $segment_properties[$pKey]['val_id']);
                    }
                    if ($check_set_necessary){//если свойство множественное и обязательное, то надо узнать, не удалили ли все значения
                        if ((!empty($val['options']['delete']) || is_null($val['value'])) && array_key_exists($v_n, $checked_values)){
                            unset($checked_values[$v_n]);//если удаляют
                        }elseif (empty($val['val_id'])){
                            $checked_values[] = $val;//если добавляют
                        }//редактирование в данном случае ничего не меняет
                    }
                    if (!empty($err)){
                        continue;
                    }
                    if (empty($val['val_id'])//если не передан, значит хотим создать
                        || empty($entity) //при создании всё надо сохранять
                        //так же проверяем, отличается ли новое значение от старого
                        || ($v_n !== FALSE && $val['value'] != $segment_properties[$pKey]['value'][$v_n])
                        || (!$property['set'] && $val['value'] != $segment_properties[$pKey]['value'])
                        || !empty($val['options']['delete'])
                        || $set_changes){
                        $changedPropKeys[$property['key']] = $property['key'];
                    }
                }
                if ($check_set_necessary && empty($checked_values)){//если свойство множественное и обязательное, то надо узнать, не удалили ли все значения
                    $errors[] = array(
                        'segment_id' => $segment_id,
                        'key' => $property['key'],
                        'title' => $property['title'],
                        'error' => 'necessary'
                    );
                }
            }
            //теперь узнаем, надо ли проверить view свойства на уникальность (только если поменялось хотя бы одно свойство)
            if (!empty($changedPropKeys)){
                $needCheckView = FALSE;//надо ли проверять view свойства (только на уникальность)
                foreach ($propList as $pKey => $property){
                    if ($property instanceof Properties\View && $property['unique'] == 1){
                        $needCheckView = TRUE;
                        break;
                    }
                }
                if ($needCheckView){
                    static::checkViewPropertiesValue($type_id, $propValues, $err, $segment_id, $id, $item_id, $max_recreate_view);
                }
            }
            self::$props_checked = array(
                'check' => $checkString,
                'errors' => $err,
                'propKeys' => $changedPropKeys
            );
        }
        $errors = empty($errors) ? self::$props_checked['errors'] : (empty(self::$props_checked['errors']) ? $errors : array_merge(self::$props_checked['errors'], $errors));
        return self::$props_checked['propKeys'];
    }
    /**
     * Проверяет корректность составных свойств @see checkProperties
     * @param array $propList
     * @param array $propValues
     * @param string $errors
     * @param int $segment_id
     * @param int $id
     * @param int $type_id
     * @param int $item_id
     * @param int $max_recreate_view
     * @return boolean
     */
    private static function checkViewPropertiesValue($type_id, &$propValues, &$errors, $segment_id = NULL, $id = NULL, $item_id = NULL, $max_recreate_view = Catalog::MAX_RECREATE_VIEWS_ON_UPDATE){
        $result = Catalog::recreateAllViews($max_recreate_view, $segment_id);//пересчитываем все view свойства
        if (!$result){
            $errors['main'] = 'Система пересчитывает составные свойства, повторите запрос позже';
            return false;
        }
        if (!empty($item_id)){//значит сохраняем вариант и нам нужны свойства товара
            $item = Item::getById($item_id, $segment_id);
            $itemProperties = $item->getPropertyList('key', PropertyFactory::P_NOT_VIEW);
        }
        $propList = static::getProperties($type_id, 'key');
        //для композитных свойств нужны значения всех свойств, причем реальные.
        $all_properties = array();
        if(!empty($id)){
            //недостающие свойства из товара\варианта
            $unknown_properties = array_diff_key($propList, $propValues);
            $entity = static::getById($id);
            if (!empty($unknown_properties)){
                foreach ($unknown_properties as $prop){
                    if (!empty($entity['properties'][$prop['key']])){
                        $all_properties[$prop['key']] = $entity['properties'][$prop['key']]['real_value'];
                    }
                }
            }
            if (!empty($item_id)){
                //недостающие свойства из товара (если редактируем вариант)
                $unknown_properties = array_diff_key($itemProperties, $propValues);
                if (!empty($unknown_properties)){
                    foreach ($unknown_properties as $prop){
                        if (!empty($item['properties'][$prop['key']])){
                            $all_properties[$prop['key']] = $item['properties'][$prop['key']]['real_value'];
                        }
                    }
                }
            }
            //реальные значения проверяемых свойств
            foreach ($propValues as $pr_key => $prop_val){
                $mixed_property = !empty($propList[$pr_key]) ? $propList[$pr_key] : (!empty($itemProperties[$pr_key]) ? $itemProperties[$pr_key] : NULL);
                if ($mixed_property['set'] != 1){//свойства нужны для view а set там не участвует
                    foreach ($prop_val as $num => $data){
                        $all_properties[$pr_key] = !empty($mixed_property) ? $mixed_property->formatValue(is_array($data) ? $data['value'] : $data, false, false, $segment_id) : NULL;
                    }
                }
            }
        }
        //для всех view свойств ищем предположительное новое значение
        foreach ($propList as $k => $p){
            /* @var $p Property */
            if ($p instanceof Properties\View){
                $new_value = array();
                $new_value['value'] = $p->composeValue(
                    $all_properties,
                    $propList,
                    $p['segment'] ? $segment_id : NULL,
                    $p['multiple'] == 1 ? (!empty($item_id) ? $item_id : NULL) : NULL);
                static::checkPropertyValue($p, $new_value, $errors, $segment_id, $id, $type_id);
            }
        }
        static::cleanErrors($errors);
    }
    /**
     * Проверить значение свойства. ВНИМАНИЕ, проверяется только одно значение. т.е. если свойство множественное, и требуется проверка сразу всех значений, то надо это делать выше
     * @param Properties\Property $property
     * @param mixed $value
     * @param array $errors
     * @param int $segment_id
     * @param int $id Id элемента (для которого проверяем)
     * @return bool
     * @throws \Exception
     */
    private static function checkPropertyValue(Properties\Property $property, &$value, &$errors, $segment_id = NULL, $id = NULL, $type_id = NULL){
		$value = $property->prepareValueToSave($value);
        if (empty($type_id)){
            if (empty($id)){
                throw new \LogicException('Если не передается id типа, то обязательно надо передавать id элемента');
            }
            $entity = static::getById($id, $segment_id);
            $type_id = $entity['type_id']; 
        }
        $pKey = $property['key'];
        $checked_string = md5($property['id'].serialize($value).$segment_id.$type_id.$id);
        $check_value = $value['value'];
        if (array_key_exists($checked_string, static::$checkedProperty)){
            if (!empty(static::$checkedProperty[$checked_string])){
				$old_errors = static::$checkedProperty[$checked_string];
				foreach ($errors as $segment_id => &$err){
					if (!empty(static::$checkedProperty[$checked_string][$segment_id])){
						$err = array_merge($err + static::$checkedProperty[$checked_string][$segment_id]);
					}
				}
                return FALSE;
            }
        }else{
            // проверка на необходимость
            if($property->isRequired($type_id) && (is_null($check_value))){
				$errors[] = array(
                    'segment_id' => $segment_id,
                    'key' => $property['key'],
                    'title' => $property['title'],
                    'error' => 'necessary'
                );
                static::$checkedProperty[$checked_string] = $errors;
                return FALSE;
            }
            //проверка на корректность
            if(!is_null($check_value) && !$property->isValueFormatCorrect($check_value)){
                $errors[] = array(
                    'segment_id' => $segment_id,
                    'key' => $property['key'],
                    'title' => $property['title'],
                    'error' => 'incorrect'
                );
                static::$checkedProperty[$checked_string] = $errors;
                return FALSE;
            }
            //проверка на уникальность
            if ($property['unique'] == 1 && !is_null($check_value) && !static::checkUniqueValue($pKey, $check_value, $segment_id, $id, $type_id)){
                $errors[] = array(
                    'segment_id' => $segment_id,
                    'key' => $property['key'],
                    'title' => $property['title'],
                    'error' => 'unique'
                );
                static::$checkedProperty[$checked_string] = $errors;
                return FALSE;
            }
        }
        static::$checkedProperty[$checked_string] = NULL;
        return TRUE;
    }

    /**
     * Проверка на уникальность значения свойства для элемента
     * @param string $prop_key
     * @param string $value
     * @param int $segment_id
     * @param int $id Id элемента (для которого проверяем)
     * @return bool TRUE if value is unique
     */
    public static function checkUniqueValue($prop_key, $value, $segment_id = NULL, $id = NULL){}
	/**
     * @TODO рекурсия не?
	 * ошибки могут быть null, поэтому чистим массив
	 * @param array $errors
	 * @return type
	 */
	protected static function cleanErrors(&$errors){
		if (!empty($errors)){
			foreach($errors as $s_id => &$er){
				if(empty($er)){
					unset($errors[$s_id]);
					self::cleanErrors($er);
				}
			}
		}
		return $errors;
	}
    /**
     * вспомогательный массив с названиями свойств, входящих в состав view свойств (для вывода ошибок)
     * @param array $errors
     * @param int $type_id
     * @param array $propsByIds
     * @return array
     */
    public static function getServiceErrorProperties($errors, $type_id, $propsByIds){
        $serviceErrorProperties = array();//вспомогательный массив с названиями свойств, входящих в состав view свойств
        if (!empty($errors['unique'])){
            $all_variant_propertiesByKeys = PropertyFactory::search($type_id, PropertyFactory::P_ALL, 'key');//свойства по ключу
			if (!is_array($errors)){
				return $serviceErrorProperties;
			}
            foreach ($errors as $type => $props){
                if ($type == 'unique' && !empty($props)){
					if (!is_array($props)){
						continue;
					}
                    foreach ($props as $p_id => $title){
                        if (!empty($propsByIds[$p_id]) && $propsByIds[$p_id]['data_type'] == \Models\CatalogManagement\Properties\View::TYPE_NAME){
                            preg_match_all('~{([^}]*)}~', $propsByIds[$p_id]['values'], $out);
                            foreach ($out[1] as $key){
                                $serviceErrorProperties[$p_id][$all_variant_propertiesByKeys[$key]['id']] = $all_variant_propertiesByKeys[$key]['title'];
                            }
                        }
                    }
                }
            }
        }
        return $serviceErrorProperties;
    }

    /**
     * Сформировать значение позиции каталога. //@TODO возможно стоит сделать static, чтобы при использовании не думать о зависимостях
     * @param Properties\Property $property
     * @param array $data для пустых значений передаем NULL, для нормальных - array($val_id => array('id' => $val_id, 'value' => $val, 'position' => $pos))
     * @param int $segment_id
     * @return array
     * @throws \Exception
     * @internal
     */
    public function makePropValue(Properties\Property $property, $data, $segment_id = NULL){
        $value = NULL;
        $val_id = NULL;
        $position = NULL;
        $value_key = NULL;
        if (!is_array($data) && !is_null($data)){
            throw new \Exception('Неверно передан параметр $data');
        }
        /**
         * @TODO убрать безобразие с повторяющимся кодом, пусть ключи определяет метод проперти
         */
        if ($property['data_type'] == Properties\Enum::TYPE_NAME){
            $values = $property['values'];
            if (!is_null($data)){
                foreach( $data as $k => $v ){//переформировываем в удобный нам вид
                    $value[$k] = $v['value'];
                    $value_key[$k] = !empty($values[$v['value']]['key']) ? $values[$v['value']]['key'] : NULL;
                    $val_id[$k] = $v['id'];
                    $position[$k] = isset($v['position']) ? $v['position'] : NULL;
                }
            }
            $value = $property->valueTypeCast($value);
            if ($property['set'] != 1){
                $val_id = !is_null($val_id) ? reset($val_id) : NULL;
                $value = !is_null($value) ? reset($value) : NULL;
                $value_key = !is_null($value_key) ? reset($value_key) : NULL;
                $position = !is_null($position) ? reset($position) : NULL;
            }
        } else {
            if (!is_null($data)){
                foreach( $data as $k => $v ){//переформировываем в удобный нам вид
                    $value[$k] = $v['value'];
                    $val_id[$k] = $v['id'];
                    $position[$k] = isset($v['position']) ? $v['position'] : NULL;
                }
            }
            $value = $property->valueTypeCast($value);
            if ($property['set'] != 1){
                $val_id = !is_null($val_id) ? reset($val_id) : NULL;
                $value = !is_null($value) ? reset($value) : NULL;
                $position = !is_null($position) ? reset($position) : NULL;
            }
        }
        $propValue = array(
            $val_id,//id значения
            $value,//реальное значение из базы
            $value_key,
            $position,
            $property->formatValue($value, false, false, $segment_id),//получившееся значение без маски
            $property->formatValue($value, true, false, $segment_id),//конечное значение с маской
        );
        return self::propValuesKeys($propValue);
    }
    final private static function propValuesKeys($data){
        static $values_keys = array(
            'val_id',//id значения
            'value',//реальное значение из базы
            'value_key',
            'position',
            'real_value',//получившееся значение без маски
            'complete_value'//конечное значение с маской
        );
        return array_combine($values_keys, $data);
    }
    /**
     * Форматирует свойства должным образом. Массив свойств ($this->properties) имеет такую структуру для каждого ключа (property key): @see $this->makePropValue
     * <UL>
     * <LI> value => Значение свойства в исходном виде (из БД)
     * <LI> real_value => Значения без маски (отформатированные должным образом)
     * <LI> complete_value => Должным образом отформатированное предстваление значения для вывода
     * </UL>
     * для корректной работы должен быть передан массив $segment_properties с элементами value, функция создаст complete_value
     * @param array $segment_properties
     * @return NULL
     */
    protected function loadProperties($segment_properties){
        if (empty($segment_properties)){
            return;
        }
        $propList = $this->getPropertyList();
        if (empty($propList)){
            return;
        }
        foreach($propList as $property) {
            /* @var $property Properties\Property */
            $propKey = $property['key'];
            foreach ($segment_properties as $segment_id => $properties){//все свойства надо отформатировать
                if (($segment_id == 0 && $property['segment'] != 1) || $property['segment'] == 1){
                    $this->propertiesBySegment[$segment_id][$propKey] = $this->makePropValue(
                        $property, 
                        isset($properties[$propKey]) ? $properties[$propKey] : null,
                        $segment_id
                    );
                }
            }
            $property->onPropertyLoad($this, $this->propertiesBySegment);
        }
        foreach (static::$dataProviders as $p){
            $p->onPropertyLoad($this, $this->propertiesBySegment);
        }
        $this->properties = $this->getSegmentProperties($this->segment_id);
        $this->propertyValuesCache = null; //отчистка кеша
    }
    /**
     * @param int $id
     * @throws \LogicException
     * @see Catalog::factory()
     */
	protected function __construct($id, $segment_id = NULL){
        $this->id = $id;
        $data = static::$dataCache[$id];
        if (empty($segment_id)){
            $def_segment = \App\Segment::getInstance()->getDefault();
            $this->segment_id = $def_segment['id'];
        }else{
            $this->segment_id = $segment_id;
        }
        if (static::CACHE_ENABLE && !empty($data['cache'])) {
            list($this->data, $cached_properties) = $data['cache'];
            $this->propertiesBySegment = self::cacheUnpack($cached_properties);
            $this->properties = $this->getSegmentProperties($this->segment_id);
        }elseif (!empty($data['main'])) {
            $this->data = $data['main'];
            $this->loadProperties($data['properties']);
            $this->need_save['cache'] = TRUE;
            $this->prepareCache();//если небыло в кэше, загоняем
        }else{
            throw new \LogicException('Programm incorrect');
        }
        if (!empty($this->data['recreate_view'])){
            $this->recreateViews();
        }
        foreach (static::$dataProviders as $p){
            $p->onLoad($this, $this->propertiesBySegment);
        }
    }
    public function __destruct() {
        $this->save();
	}
    /**
     * Сохранение в БД всех подготовленных данных
     * @return type
     */
	public static function saveCacheData(){
		if (empty(self::$cacheDataToSave) || !self::CACHE_ENABLE){
			return;
		}
		$db = \App\Builder::getInstance()->getDB();
		foreach (self::$cacheDataToSave as $e_t => $data){
			//составляем один большой INSERT ... ON DUPLICATE KEY UPDATE
			foreach ($data as $id => $d){
				$ins[] = '("' . $e_t . '", ' . $id . ', ' . $db->escape_value($d) . ')';
			}
		}
		$ins = 'INSERT INTO `'.static::TABLE_DATA_CACHE.'` (`type`, `id`, `cache`) VALUES ' . implode(', ', $ins) . ' ON DUPLICATE KEY UPDATE `type` = VALUES(`type`), `id` = VALUES(`id`), `cache` = VALUES(`cache`)';
		try {
			$db->query($ins);
		} catch (\MysqlSimple\Exceptions\MySQLQueryException $e){
			$db->query($ins);//если опять не получилось, то "ку"
		}
		self::$cacheDataToSave = NULL;
	}
    /**
     * Подготовка данных для кэширования в БД
     */
    protected function prepareCache(){
        if (!self::CACHE_ENABLE){
			return;
		}
		$all_count = 0;
		if (!empty(self::$cacheDataToSave)){
			foreach (self::$cacheDataToSave as $t => $d){
				$all_count += count($d);
			}
		}
		if ($all_count >= self::MAX_CACHE_STORE_COUNT){
			self::saveCacheData();
		}
		self::$cacheDataToSave[static::CATALOG_IDENTITY_KEY][$this->id] = json_encode(array($this->data, self::cachePack($this->propertiesBySegment)), JSON_UNESCAPED_UNICODE);
	}
    /**
     * Функция для отчистки свойств
     * @param int[] $propIds
     */
    protected function deleteProps($propIds = null, $segment_id = null){
        $db = \App\Builder::getInstance()->getDB();
        foreach (array(static::TABLE_PROP_INT, static::TABLE_PROP_FLOAT, static::TABLE_PROP_STRING) as $table){
            $db->query('
                DELETE FROM ?#
                WHERE `'.static::TABLE_PROP_OBJ_ID_FIELD.'` = ?d
                    { AND `property_id` IN (?i)}
                    { AND (`segment_id` = ?d OR `segment_id` IS NULL)}',
                $table,
                $this->id,
                !empty($propIds) ? $propIds : $db->skipIt(),
                isset($segment_id) ? $segment_id : $db->skipIt()
            );
        }
    }
    /**
     * двигает позицию каталога
     * @param int $move на какой номер менять
     * @param array $errors
     * @return bool
     */
    public function move($move, &$errors = array()){
        if (!empty($move)){
            $db = \App\Builder::getInstance()->getDB();
            $old_position = $this->data['position'];
            if ($old_position > $move){
                $db->query('UPDATE `'.static::TABLE.'` SET `position`=`position`+1 WHERE `'.static::UNIQUE_POSITION_IN_FIELD.'`=?d AND `position`>=?d AND `position`<?d', $this->data[static::UNIQUE_POSITION_IN_FIELD], $move, $old_position);
            }else{
                $db->query('UPDATE `'.static::TABLE.'` SET `position`=`position`-1 WHERE `'.static::UNIQUE_POSITION_IN_FIELD.'`=?d AND `position`<=?d AND `position`>?d', $this->data[static::UNIQUE_POSITION_IN_FIELD], $move, $old_position);
            }
            $this->updateEntityParams(array('position' => $move), $errors);
            static::clearCache(null, $this->data[static::UNIQUE_POSITION_IN_FIELD], true);//кэш чистим, т.к. поменялись значения у нескольких сущностей сразу
            return true;
        }
        return false;
    }

    /**
     * Удаление
     * @param array $errors
     * @param bool $remove_from_db удалять ли из базы?
     * @return bool
     */
    public function delete(&$errors = array(), $remove_from_db = false){
        foreach (static::$dataProviders as $p){
            $p->preDelete($this, $errors);
        }
        if (!empty($errors)){
            return FALSE;
        }
        $id = $this->id;
        $old_entity = clone $this;//старые данные для хелперов
        $db = \App\Builder::getInstance()->getDB();
        if ($remove_from_db){
            $this->deleteProps();
            $db->query('DELETE FROM `'.static::TABLE.'` WHERE `id`=?d', $id);
            $this->properties = array();
            $this->propertiesBySegment = array();
            $this->data = array();
            $this->id = NULL;
        }else{
            $this->update(array('status' => static::S_DELETE, 'recreate_view' => 1));
        }
        foreach (static::$dataProviders as $p){
            $p->onDelete($id, $old_entity, $remove_from_db);
        }
        static::clearCache($id);
        return true;
    }
    /**
     * Подготовить данные для изменения конкретного айтема\варианта
     * Теперь данные должны передаваться в виде массива с val_id и value, поэтому, нужен механизм для перевода данных в новый формат
     * @param int $type_id
     * @param array $data (key свойства => характеристики значения[])
     * @param int $segment_id
     * @param array $entity_props старые значения сущности array('key свойства' => array(val_id, value, position))
     */
    public static function prepareUpdateData($type_id, $data, $segment_id = NULL, $entity_props = NULL){
        $props = static::getProperties($type_id, 'key', NULL, 'position', $segment_id);
        $update_data = array();
        foreach ($data as $pk => $d){
            if (is_array($d) && !isset($d['val_id'])){
                foreach ($d as $val){
                    if (!is_array($val) && !empty($entity_props[$pk])){
                        $k = array_search($val, $entity_props[$pk]['value']);
                        if ($k !== FALSE){
                            $val_id = $entity_props[$pk]['val_id'][$k];
                            $val = array($val_id => $val);
                        }
                    }
                    $update_data[$pk][] = static::getUpdateValue($props[$pk], $val, $segment_id, !empty($entity_props[$pk]) ? $entity_props[$pk] : NULL);
                }
            }else{
                $update_data[$pk][] = static::getUpdateValue($props[$pk], $d, $segment_id, !empty($entity_props[$pk]) ? $entity_props[$pk] : NULL);
            }
        }
        return $update_data;
    }
    /**
     * Обновить данные элемента
     * @param array $params
     * @see static::$allowParams
     * список допустимых параметров <ul>
        <li>int <b>status</b></ul>
        <li>int <b>recreate_view</b></ul>
     * @param array $propValues массив в формате (key свойства => характеристики значения[]), если передать NULL в качестве характеристик значения, то указанное свойство будет удалено
     * характеристики значения это массив со следующими ключами:
     * <UL>
     *      <LI>int    <B>val_id</B>
     *      <LI>string <B>value</B>
     *      <LI>mixed  <B>options</B>
     * </UL>
     * @param array $errors
     * @param int $segment_id
     * @return void
     */
    public function update(Array $params = NULL, Array $propValues = NULL, &$errors = array(), $segment_id = NULL){
        //уникальный ключ текущего обновления (т.к. внутри хелперов могут быть свои обновления)
        $updateKey = md5($this['id'] . static::CATALOG_IDENTITY_KEY . json_encode($params) . json_encode($propValues) . $segment_id . time());
        $oldClearCache = self::$clearCache;
        self::$clearCache = FALSE;//во время редактирования не надо чистить перезаполненный кэш, чтобы не затерлись кэши хелперов
        foreach (static::$dataProviders as $p){
            /** @var $p iItemDataProvider */
            $p->preUpdate($updateKey, $this, $params, $propValues, $segment_id, $errors);
        }
        $updatedProperties = NULL;//реально изменившиеся свойства
        if (!empty($propValues) && empty($errors)){
            $this->updatePropertiesValues($propValues, $errors, $segment_id, $updatedProperties);//кэш сохраняется после обновления свойств
            if (empty($errors)){
                $params['last_update'] = date('Y-m-d H:i:s');
            }
        }
        if (empty($errors)){
            $this->updateEntityParams($params, $errors);
        }
        if (empty($errors)){//либо надо передавать ошибки в хелперы, либо вообще их не запускать @TODO передавать будем?
			foreach (static::$dataProviders as $p){
				/** @var $p iItemDataProvider */
				$p->onUpdate($updateKey, $this, $segment_id, $updatedProperties);
			}
		}
		if (!is_null($this->need_save)){
			$this->prepareCache();//если что-то поменялось, подготовим кэш
		}
        self::$clearCache = $oldClearCache;
    }
    /**
     * Сохранение сущности в базу
     */
    public function save(){
        if (is_null($this->need_save)){
            return;
        }
        $db = \App\Builder::getInstance()->getDB();
        if (!empty($this->need_save['params'])){
            $update_data = array_intersect_key($this->data, array_flip(static::$loadFields));
            if (!empty($update_data) && !empty($this->id)) {
                \App\Builder::getInstance()->getDB()->query('UPDATE `'.  static::TABLE.'` SET ?a WHERE `id`=?d', $update_data, $this->id);
            }
        }
        if (!empty($this->need_save['props']['del'])){
            foreach ($this->need_save['props']['del'] as $table => $del_vals){
                $db->query('DELETE FROM ?# WHERE `id` IN (?i)', $table, $del_vals);
            }
        }
        if (!empty($this->need_save['props']['edit'])){
            foreach ($this->need_save['props']['edit'] as $table => $vals){
                $values_sql = array();
                foreach ($vals as $v_id => $v){
                    $val = $db->escape_value($v['value']);
                    $pos = !empty($v['position']) ? $v['position'] : 'NULL';
                    $values_sql[] = $v_id . ', ' . $val . ', 0, 0, ' . $pos;//хак - пишем 0 (NULL не катит), хотя в таблице есть значения, это для того, чтобы пакетно обновлять строки, и в данном случае у нас строки не добавляются
                }
                //одинм махом инсертим
                $db->nakedQuery('INSERT INTO `'.$table.'` (`id`, `value`, `'.static::TABLE_PROP_OBJ_ID_FIELD.'`, `property_id`, `position`) VALUES ('.implode('),(', $values_sql).') '
                    . 'ON DUPLICATE KEY UPDATE `value`=VALUES(`value`), `position`=VALUES(`position`)');
            }
        }
        self::saveCacheData();//сохраняем в кэш то, что поменяли (все данные уже подготовлены)
        $this->need_save = NULL;
    }
    /**
     * Сохранение параметров сущности
     * @param array $params
     * @param array $errors
     * @return boolean
     */
    protected function updateEntityParams($params, &$errors = NULL){
        $type = $this->getType();
        $catalog = $type->getCatalog();
        if ($catalog['allow_item_url']) {
            if (!empty($params['key']) && $params['key'] != $this['key']) {
                $params['key'] = $this->checkKey($params['key'], $errors);
            } elseif (empty($this['key']) || $this['key'] == $this['id']) {
                if (!empty($this['properties'][static::ENTITY_TITLE_KEY])) {
                    if ($this['properties'][static::ENTITY_TITLE_KEY]['property'] instanceof \Models\CatalogManagement\Properties\View) {
//                        $this->recreateViews();
                    }
                    $default_segment = \App\Segment::getInstance()->getDefault();
                    $segment_data = $this->getSegmentProperties($default_segment['id']);
                    $title = !empty($segment_data[static::ENTITY_TITLE_KEY]['complete_value']) ? $segment_data[static::ENTITY_TITLE_KEY]['complete_value'] : null;
                }
                $key = $this->checkKey(!empty($title) ? \LPS\Components\Translit::Supertag($title) : $this->id, $e);
                /** @TODO Почему отдельная переменная для ошибок? Была ли какая-то задумка? */
                if (empty($e)) {
                    $params['key'] = $key;
                }
            }
        } else {
            $params['key'] = NULL;
        }
        if (!empty($params)){
            $params = Catalog::checkAllowed($params, static::$allowParams);
            if (!$params){
                $errors['main'] = 'incorrect params';
            }elseif (empty($errors)){
				//проверим, может значения не поменялись, тогда базу и трогать не надо
				foreach ($params as $f => $v){
					if ($v == $this->data[$f]){
						unset($params[$f]);
					}
				}
				if (!empty($params)){
					$this->data =  $params + $this->data; //заменяем изменившиеся параметры
					$this->need_save['params'] = TRUE;
					return TRUE;
				}
            }
        }
        return FALSE;
    }

    /**
     * Изменить значения свойств элемента (все или частично)
     * @param array $propValues массив в формате (key свойства => характеристики значения[]), если передать NULL в качестве характеристик значения, то указанное свойство будет удалено
     * характеристики значения это массив со следующими ключами:
     * <UL>
     *      <LI>int    <B>val_id</B>
     *      <LI>string <B>value</B>
     *      <LI>mixed  <B>options</B>
     * </UL>
     * @param array $errors в формате $errors['error_type'][$pId] для того, чтобы можно было сразу разобрать сколько ошибок какого типа
     * @param int|null $segment_id
     * @param array $updatedPropKeys массив свойств, которые реально поменялись
     * @return bool
     * @throws \Exception
     */
	protected function updatePropertiesValues($propValues, &$errors = array(), $segment_id = NULL, &$updatedPropKeys = NULL){
		if (empty ($propValues)){
            return TRUE;
        }
        if (is_null($segment_id)){
            $segment = \App\Segment::getInstance()->getDefault();
            $segment_id = !empty($segment) ? $segment['id'] : 0;
        }
        $updatedPropKeys = $this->validateProperties($propValues, $errors, $segment_id);
        if (empty($updatedPropKeys)){
            return TRUE;
        }
        if (!empty($errors)){
            return FALSE;
        }
        //список доступных свойств
        $propListByKeys = $this->getPropertyList('key');
		$entity_prop_values = $this->getSegmentProperties($segment_id);
        foreach ($propValues as $pKey => $pData) {
            if (!isset($propListByKeys[$pKey]) || !array_key_exists($pKey, $updatedPropKeys)){
                continue;
            }
            /* @var $property Properties\Property */
            $property = $propListByKeys[$pKey];
            $position = $property['set'] ? 1 : NULL;
            $segment_properties = $this->getSegmentProperties($segment_id);
            $doubles_check = array();
            foreach ($pData as $data){//могут передаваться дубликаты, надо их игнорить
                $data_check = json_encode($data, JSON_UNESCAPED_UNICODE);
                if (in_array($data_check, $doubles_check)){
                    continue;
                }
                $doubles_check[] = $data_check;
                if (!$property['set'] && empty($data['val_id']) && !empty($segment_properties[$pKey])){//если не передали val_id, и свойство не множественное, то его можно найти
                    $data['val_id'] = $segment_properties[$pKey]['val_id'];
                }
                //могут передавать два одинаковых значения (например не сегментированное свойство в разных сегментах или в множественное свойство прописали одинаковые значения), 
                //чтобы такого косяка небыло, надо проверить уже сохраненные, проблема только для создания, т.к. редактирвоание само с такими вещами разерется, у него val_id есть
                if (empty($data['val_id']) && $property['set'] && ($data['value'] != '' && !is_null($data['value'])) && isset($segment_properties[$pKey])){
                    $v_key = array_search($data['value'], $segment_properties[$pKey]['value']);
                    if (!empty($v_key)){
                        $data['val_id'] = $segment_properties[$pKey]['val_id'][$v_key];
                    }
                }
                if((!empty($data['options']) && !empty($data['options']['delete'])) || (is_null($data['value']) && !empty($data['val_id']))){
                    if (empty($data['val_id'])){
                        throw new \Exception('Для удаления значения надо передавать val_id');
                    }
                    $this->deleteValue($property, $data['val_id'], $segment_id);
                }elseif (empty($data['val_id']) && !is_null($data['value'])){
                    $data['val_id'] = $this->createValue($property, $data['value'], !empty($data['position']) ? $data['position'] : $position, $segment_id, !empty($data['options']) ? $data['options'] : NULL);
                }elseif (!empty($data['val_id']) && !is_null($data['value'])){
                    $this->editValue($property, $data['val_id'], $data['value'], !empty($data['position']) ? $data['position'] : $position, !empty($data['options']) ? $data['options'] : NULL, !empty($entity_prop_values[$pKey]) ? $entity_prop_values[$pKey]['value'] : NULL, $segment_id);
                }
                if ($property['set']){
                    $position++;
                }
            }
        }
        $this->recreateViews();//делает пересчет properties
        return TRUE;
	}
	/**
     * Изменить значения свойств элемента (все или частично)
     * @param array $propValues массив в формате (key свойства => array(
	 *		'val_id' => int
			'value' => string
			'options' => mixed
	 *	)[])
     * @param array $errors в формате $errors['error_type'][$pId] для того, чтобы можно было сразу разобрать сколько ошибок какого типа
     * @throws \Exception
     * @return bool
     */
    public function updateValues($propValues, &$errors = array(), $segment_id = NULL){
        return $this->update(array(), $propValues, $errors, $segment_id);
    }
	public function updateParams($params, &$errors = array()){
		return $this->update($params, array(), $errors);
	}
    /**
     * Обновляем данные в массиве значений свойств
     * @param Properties\Property $property
     * @param int $value_id
     * @param int|string $value
     * @param NULL|int $segment_id
     */
    private function setPropValue($property, $value_id, $value, $position, $segment_id){
        $s_id = empty($segment_id) ? 0 : $segment_id;
        $segment_properties = $this->getSegmentProperties($segment_id);
        $vals = !empty($segment_properties[$property['key']]) ? $segment_properties[$property['key']] : array();
        if (empty($property['set'])/* && $vals['val_id'] == $value_id*//*@TODO не понятно, зачем нужно было это условие?*/){
            $this->propertiesBySegment[$property['segment'] ? $s_id : 0][$property['key']] = $this->makePropValue($property, array($value_id => array('id' => $value_id, 'value' => $value, 'position' => $position)), $s_id);
        }elseif($property['set'] == 1){
            $n = 0;
            if (!empty($vals['val_id']) && in_array($value_id, $vals['val_id'])){
                $n = array_search($value_id, $vals['val_id']);
            }
            if (is_null($value)){//если null, значит удаляем
                if (!empty($vals['value']) && array_key_exists($n, $vals['value'])){
                    foreach (array('value', 'real_value', 'complete_value', 'position') as $f){
                        unset($vals[$f][$n]);
                    }
                }
            }else{
                $vals['value'][$n] = $value;
                $vals['val_id'][$n] = $value_id;
                $vals['position'][$n] = $position;
            }
            if (!empty($vals['val_id'])){
                asort($vals['position']);//новая сортировка
                $new_vals = array('value' => array(), 'val_id' => array());
                foreach ($vals['position'] as $num => $pos){
                    $new_vals['value'][$num] = $vals['value'][$num];
                    $new_vals['val_id'][$num] = $vals['val_id'][$num];
                }
                $vals['value'] = $new_vals['value'];
                $vals['val_id'] = $new_vals['val_id'];
                $update_values = array();
                foreach ($vals['val_id'] as $i => $v_id){
                    $update_values[$v_id] = array(
                        'value' => $vals['value'][$i], 
                        'id' => $v_id, 
                        'position' => $vals['position'][$i]);
                }
            }else{
                $update_values = NULL;
            }
            $this->propertiesBySegment[$property['segment'] ? $s_id : 0][$property['key']] = $this->makePropValue($property, $update_values, $s_id);
        }
        $property->onPropertyLoad($this, $this->propertiesBySegment); /** @TODO также вызывается из loadProperties */
        foreach (static::$dataProviders as $p){
            $p->onPropertyLoad($this, $this->propertiesBySegment);
        }
        $this->properties = $this->getSegmentProperties($this->segment_id);
        $this->propertyValuesCache = NULL;
    }

    /**
     *
     * @param \Models\CatalogManagement\Properties\Property $property
     * @param int|string $value
     * @param int $segment_id
     * @param array $additional_data
     * @throws \Exception
     * @return null|int
     */
	protected function createValue(Properties\Property $property, $value, $position, $segment_id = NULL, $additional_data = array()){
        if ($property['segment'] == 1 && is_null($segment_id)){//если свойство сегментированное, а сегмент не передали, то тут адовый косяк
            throw new \Exception('При изменении сегментированного свойства «' . $property['title'] . '» не передали id сегмента');
        }
		$value = $property->explicitType($value);
		if (is_null($value)){
			return NULL;
		}
        //проверяем, может такое значение уже есть (в set свойствах такое возможно)
        if ($property['set'] == 1){
            foreach ($this->propertiesBySegment as $s_id => $vals){
                if (!isset($vals[$property['key']])){
                    continue;
                }
                if (isset($vals[$property['key']]['value']) && in_array($value, $vals[$property['key']]['value']) && (!$property['segment'] || $s_id == $segment_id)){
                    return NULL;
                }
            }
        }
		$db = \App\Builder::getInstance()->getDB();
        //добавляем значение именно тут, т.к. нам нужен value_id в массиве значений свойства
		$value_id = $db->query('INSERT INTO ?# SET
			`'.static::TABLE_PROP_OBJ_ID_FIELD.'` = ?d,
				`property_id` = ?d,
				`value` = ?s,
				`segment_id` = ?d,
                `position` = ?d',
				$property['table'],
				$this->id,
				$property['id'],
				$value,
				$property['segment'] == 1 ? $segment_id : NULL,
                !empty($position) ? $position : NULL);
        $this->setPropValue($property, $value_id, $value, !empty($position) ? $position : NULL, $segment_id);
		foreach (static::$dataProviders as $p){
			$p->onValueChange('create', $this, $property, $value_id, array(), $additional_data, $segment_id);
		}
		$this->need_save['add'] = TRUE;
		return $value_id;
	}
	/**
	 * 
	 * @param \Models\CatalogManagement\Properties\PropertyFactory $property
	 * @param int $value_id
	 * @param mixed $value
	 * @param array $additional_data
	 * @return NULL
	 */
	protected function editValue(Properties\Property $property, $value_id, $value, $position, $additional_data = array(), $old_value = NULL, $segment_id){
        if (empty($value_id)){
            throw new \Exception('Не передан value_id');
        }
        $segment_properties = $this->getSegmentProperties($segment_id);
        $old_values = !empty($segment_properties[$property['key']]) ? $segment_properties[$property['key']] : array();
		$value = $property->explicitType($value);
		if (is_null($value)){
			return $this->deleteValue($property, $value_id, $segment_id);
		}
		$old_value = is_null($old_value) ? $old_values['value'] : $old_value;
		if (is_array($old_value)){
			$old_value = isset($old_value[$value_id]) ? $old_value[$value_id] : NULL;
		}
        $old_position = NULL;
        if (!empty($old_values['position']) && $property['set']){
            $old_position = $old_values['position'][$value_id];
        }
        if (!empty($position) && $old_position != $position){
            $this->need_save['props']['edit'][$property['table']][$value_id]['position'] = $position;
            $this->need_save['props']['edit'][$property['table']][$value_id]['value'] = $old_value;
        }
		if ($value != $old_value){
            if (!isset($this->need_save['props']['edit'][$property['table']][$value_id]['position'])){
                $this->need_save['props']['edit'][$property['table']][$value_id]['position'] = $old_position;
            }
            $this->need_save['props']['edit'][$property['table']][$value_id]['value'] = $value;
		}
        if (!empty($this->need_save['props']['edit'][$property['table']][$value_id])){
            $this->setPropValue($property, $value_id, $value, $this->need_save['props']['edit'][$property['table']][$value_id]['position'], $segment_id);
            foreach (static::$dataProviders as $p){
                $p->onValueChange('edit', $this, $property, $value_id, $old_values, $additional_data, $segment_id);
            }
        }
	}
	/**
	 * 
	 * @param \Models\CatalogManagement\Properties\Property $property
	 * @param int $value_id
	 */
	protected function deleteValue(Properties\Property $property, $value_id, $segment_id){
        if (empty($value_id)){
            throw new \Exception('Не передан value_id');
        }
        $segment_properties = $this->getSegmentProperties($segment_id);
        $old_values = $segment_properties[$property['key']];
        $this->need_save['props']['del'][$property['table']][] = $value_id;
        $val = NULL;
        if ($property['set'] && !empty($segment_properties[$property['key']]['val_id'])){
            $v_n = array_search($value_id, $segment_properties[$property['key']]['val_id']);
            if ($v_n === false) {
                return;
            }
            $val = $segment_properties[$property['key']]['value'][$v_n];
        }
        if (!$property['set']){
            $val = $segment_properties[$property['key']]['value'];
        }
        if (is_null($val)){//если значение и так уже удалено, то ничего дальше делать не надо, т.к. все события должны были сработать после первого удаления
            return;
        }
        $property->onValueDelete($val);
        $this->setPropValue($property, $value_id, NULL, NULL, $segment_id);
        foreach (static::$dataProviders as $p){
            $p->onValueChange('delete', $this, $property, $value_id, $old_values, array(), $segment_id);
        }
	}
    /**
	 * @TODO со временем заменить этот метод на updateValueByKey
     * Обновить значения одного свойства
     * @param int $prop_id
     * @param mixed $value если свойство множественное, то должен передаваться массив: array($val_id => $value) или array('val_id' => $val_id, 'value' => $value) иначе значение будет добавлено как новое
     * @param array $errors
     * @param int $segment_id
     * @return boolean
     */
    public function updateValue($prop_id, $value, &$errors = array(), $segment_id = NULL){
        $property = PropertyFactory::getById($prop_id);
        if (empty($property)){
            $errors['main'] = 'Property #ID ' . $prop_id . ' not found';
            return FALSE;
        }
        $segment_props = $this->getSegmentProperties($segment_id);
        $entity_prop = !empty($segment_props[$property['key']]) ? $segment_props[$property['key']] : null;
		$params = array($property['key'] => array(static::getUpdateValue($property, $value, $segment_id, $entity_prop)));
		$this->updateValues($params, $errors, $segment_id);
        return FALSE;
    }
	public function updateValueByKey($prop_key, $value, &$errors = array(), $segment_id = NULL){
		$property = $this['properties'][$prop_key]['property'];
        $segment_props = $this->getSegmentProperties($segment_id);
        $entity_prop = !empty($segment_props[$property['key']]) ? $segment_props[$property['key']] : null;
		$params = array($prop_key => array(static::getUpdateValue($property, $value, $segment_id, $entity_prop)));
		$this->updateValues($params, $errors, $segment_id);
        return FALSE;
	}
    /**
     * Возможны разные форматы передачи значений
     * @param Property $property
     * @param mixed $value одно значение или массив с данными одного значения array(val_id, value) или array(val_id => value)
     * @param int $segment_id
     * @param array $entity_prop значение текущего айтема\варианта (array(val_id, value, position))
     * @return array
     */
    private static function getUpdateValue(Property $property, $value, $segment_id, $entity_prop = NULL){
        $position = NULL;
        if ($property['set'] == 1){//в таком случае $value должно быть массивом
            if (!is_array($value)){//если передан только value, значит значения ещё небыло
                $old_value = array('val_id' => NULL, 'value' => NULL);
                $max_position = isset($entity_prop) && is_array($entity_prop['position']) ? max($entity_prop['position']) : NULL;
                $position = empty($max_position) ? 1 : ($max_position + 1);
            }else{
                //под разные форматы данных (изменение только одного значения c переданным val_id)
                if (isset($value['val_id'])){
                    $old_value = $value;
                    $value = $value['value'];
                }else{
                    $val_id = key($value);
                    $value = reset($value);
                    $old_value = array('val_id' => $val_id);
                }
            }
        }else{
            $old_value = isset($entity_prop) ? $entity_prop : NULL;
        }
        return array('val_id' => !empty($old_value['val_id']) ? $old_value['val_id'] : 0, 'value' => $value, 'position' => $position);
    }

    public function getSegmentProperties($segment_id){
        if (empty($this->propertiesBySegment)){
            return array();
        }
        $item_props = !empty($this->propertiesBySegment[0]) ? $this->propertiesBySegment[0] : array();
        if (!empty($segment_id) && !empty($this->propertiesBySegment[$segment_id])){
            //если есть сегментированные свойства с такими же ключами, то они перезапишут несегментированные свойства с этими ключами! хотя таких и не должно быть
            $item_props = $this->propertiesBySegment[$segment_id] + $item_props;
        }
        return $item_props;
    }

    /**
     * Вызывает пересоздание всех комбинированных свойств, это влечет за собой обновление кеша в БД и объекта
     */
    public function recreateViews(){
//        $segments = array();
//        $segments[0] = array();
//        $segments += \App\Segment::getInstance()->getAll();
        //по идее нам не надо бегать по нулевому сегменту @TODO проверить
        $segments = \App\Segment::getInstance()->getAll();
        //список доступных свойств
        $propList = $this->getPropListsForRecreateViews();
        $updated = FALSE;
        foreach ($propList as $property) {
            /* @var $property Properties\Property */
            if (!$this->checkPropertyToRecreateView($property)){
                continue;
            }
            foreach ($segments as $segment_id => $s){
                if (!$property['segment']) {
                    $segment_id = 0;
                }
				$sp = $this->getSegmentProperties($segment_id);
                $val = $property->composeValue(
                    $sp,
                    $propList, 
                    $segment_id, 
                    $property['multiple'] == 1 ? $this['item_id'] : NULL);
                $old_value = !empty($sp[$property['key']]) ? $sp[$property['key']]['value'] : NULL;
                $val_id = !empty($sp[$property['key']]) && !empty($sp[$property['key']]['val_id']) ? $sp[$property['key']]['val_id'] : NULL;
				if ($val != $old_value && !is_null($val)  && !empty($val)){
					if (empty($val_id)){
						$this->createValue($property, $val, NULL, $segment_id);
					}else{
						$this->editValue($property, $val_id, $val, NULL, array(), $old_value, $segment_id);
					}
                    $updated = TRUE;
				}
                if (!$property['segment']) {
                    break;
                }
            }
        }
        if ($this['recreate_view'] == 1){
            $this->updateEntityParams(array('recreate_view' => 0));
            $updated = TRUE;
        }
        if ($updated){
            $this->prepareCache();
        }
        return $updated;
    }
    /**
     * 
     */
    abstract protected function checkPropertyToRecreateView($property);

    /**
     * @return Properties\Property[]
     */
    abstract protected function getPropListsForRecreateViews();
    /**
     * валидирует свойства
     */
    abstract protected function validateProperties(&$propValues, &$errors, $segment_id = NULL);
    /**
     * возвращает список свойств либо для айтема либо для варианта, в зависимости от контекста
     */
    static protected function getProperties($type_id, $keys = 'id', $search_area = PropertyFactory::P_ALL, $sort = 'group', $segment_id = NULL){}
    /**
     * Возвращает список свойств, которые доступны данной позиции
     * @param string $keys одно из двух значений: 'id' или 'key' в зависимости от этого разные ключи будут
     * @param int $search_area constants \Models\CatalogManagement\Properties\Factory::P_*
     * @return Properties\Property[]
     */
    public function getPropertyList($keys = 'id', $search_area = PropertyFactory::P_ALL, $sort = 'group'){
		return static::getProperties($this->data['type_id'], $keys, $search_area, $sort, $this->segment_id);
    }
    /**
     * Удален ли объект
     * @return bool
     */
    public function isNull(){
        return empty($this->id);
    }

     /**
     * Получить объект в виде массива
     * @return Array
     */
    public function asArray(){
        $a = $this->data;
        $a['properties'] = $this->properties;
        return $a;
    }
    /**
     * Возвращает значения свойства из всех сегментов
     * @param string $key
     * @return array()
     */
	public function getPropertyBySegments($key){
        $prop = array();
        if (!empty($this->propertiesBySegment)){
            foreach ($this->propertiesBySegment as $segment_id => $properties){
                if (array_key_exists($key, $properties)){
                    $prop[$segment_id] = $properties[$key];
                }else{
                    $prop[$segment_id] = NULL;
                }
            }
        }
        return $prop;
	}
    /**
     * Выставить id сегмента для того, чтобы брать параметры из установленного
     * @param int $segment_id
     */
    public function setSegment($segment_id){
        $this->properties = $this->getSegmentProperties($segment_id);
        $this->propertyValuesCache = NULL;
        $this->segment_id = $segment_id;
    }
    protected static function _clearCache(){
        self::$props_checked = array();
        self::$checkedProperty = array();
    }
    /******************************* Перенос товара в другой тип *******************************/
    /**
     * Ищем соответствие пропертей по ключу, равнозначными предполагаем в случае идентичности ключа и расщепляемости
     * @param Properties\Property[] $old_props
     * @param Properties\Property[] $new_props
     * @return array
     */
    private function getCopyPropIds($old_props, $new_props){
        $result = array();
        foreach($old_props as $old_id => $op){
            foreach($new_props as $new_id => $np){
                if ($np['key'] == $op['key'] && $np['multiple'] == $op['multiple']){
                    $result[$old_id] = $new_id;
                    unset($new_props[$new_id]);
                    break;
                }
            }
        }
        return $result;
    }

    protected function makeTypeProperties(Type $new_type){
        $prop_list = array();
        $new_props = $new_type->getProperties();
        $segments = array(0 => array());
        $segments += \App\Segment::getInstance()->getAll();
        $need_multiple = $this instanceof Variant ? 1 : 0;
        foreach($new_props as $new_prop){
            if ($new_prop['multiple'] != $need_multiple
                || empty($this['properties'][$new_prop['key']])
                || empty($this['properties'][$new_prop['key']]['value'])
                || ($this['properties'][$new_prop['key']]['property']['segment'] == 1 && $new_prop['segment'] == 0))
            {
                continue;
            }
            $old_property_by_segments = $this->getPropertyBySegments($new_prop['key']);
            $values = array();
            foreach($segments as $segment_id => $s) {
                if ($new_prop['segment'] == 1 && $segment_id == 0) {
                    continue;
                }
                $old_prop_segment_id = ($this['properties'][$new_prop['key']]['property']['segment']) ? $segment_id : 0;
                $value = $this->prepareTransferPropValue($this['properties'][$new_prop['key']]['property'], $new_prop, !empty($old_property_by_segments[$old_prop_segment_id]) ? $old_property_by_segments[$old_prop_segment_id] : $this->makePropValue($new_prop, NULL, $segment_id), TRUE);
                if ($new_prop['set']){
                    foreach($value as $v){
                        if (empty($v)){
                            continue;
                        }
                        $values[$new_prop['key']][] = array(
                            'val_id' => 0,
                            'value' => $v,
                            'segment_id' => $segment_id
                        );
                        if (!$this['properties'][$new_prop['key']]['property']['set']){
                            break;
                        }
                    }
                } else {
                    $value = is_array($value) ? reset($value) : $value;
                    if (!empty($value)){
                        $values[$new_prop['key']][] = array(
                            'val_id' => 0,
                            'value' => $value,
                            'segment_id' => $segment_id
                        );
                    }
                }
            }
            if (!empty($values)){
                $prop_list += $values;
            }
        }
        return $prop_list;
    }

    /**
     * Преобразование значения свойства из старого типа в новый
     * Нужно для смены типа товара
     * @param Properties\Property $old_prop
     * @param Properties\Property $new_prop
     * @param $segment_value
     * @param bool $clone_objects — при копировании объекта каталога нужно клонировать объекты для пропертей сущностей
     * @return mixed
     */
    protected function prepareTransferPropValue(Properties\Property $old_prop, Properties\Property $new_prop, $segment_value, $clone_objects = FALSE){
        if ($new_prop['data_type'] == Properties\Enum::TYPE_NAME
            // В перечисление переносятся значения всех свойств, кроме свойств-сущностей
            && !in_array($old_prop['data_type'], array(
                Properties\Flag::TYPE_NAME,
                Properties\Post::TYPE_NAME,
                Properties\Item::TYPE_NAME,
                Properties\Variant::TYPE_NAME,
                Properties\File::TYPE_NAME,
                Properties\Image::TYPE_NAME,
                Properties\Gallery::TYPE_NAME
            ))){
            if ($old_prop['set']){
                $values = array();
                foreach($segment_value['complete_value'] as $v) {
                    $values[] = $this->transferEnumPropValue($new_prop, $v);
                }
                /** @TODO удаление изображений и галерей (пока не нужно, поскольку множественных галлерей и изображений сейчас нет) */
                return $new_prop['set'] ? $values : reset($values);
            } else {
                return $this->transferEnumPropValue($new_prop, $segment_value['complete_value']);
            }
        } elseif ($old_prop['data_type'] == $new_prop['data_type']){
            if ($new_prop['data_type'] != Properties\View::TYPE_NAME){
                if ($clone_objects && $old_prop instanceof Properties\Entity){
                    return $this->clonePropValueObjects($old_prop, $segment_value);
                } else
                return $segment_value['value'];
            }
        } elseif ($old_prop['data_type'] == Properties\Post::TYPE_NAME) {
            // Статью удаляем при сменах типа данных
            $post_ids = $old_prop['set'] ? $segment_value['value'] : (!empty($segment_value['value']) ?array($segment_value['value']) : NULL);
            if (!empty($post_ids)){
                foreach($post_ids as $post_id){
                    $post = \Models\ContentManagement\Post::getById($post_id);
                    if (!empty($post)){
                        $post->delete();
                    }
                }
            }
        } elseif ($new_prop['data_type'] == Properties\Post::TYPE_NAME) {
            return $this->transferValueToPost($old_prop, $new_prop, $segment_value['complete_value']);
        } elseif (in_array($new_prop['data_type'], array(
            Properties\Int::TYPE_NAME,
            Properties\Float::TYPE_NAME,
            Properties\String::TYPE_NAME,
            Properties\Text::TYPE_NAME
        ))) {
            // При несоответствии типа пробуем привести к новому
            $complete_value = $segment_value['complete_value'];
            $end_type = $new_prop['data_type'];
            switch ($old_prop['data_type']){
                case Properties\Enum::TYPE_NAME:
                case Properties\String::TYPE_NAME:
                case Properties\Text::TYPE_NAME:
                case Properties\View::TYPE_NAME:
                    if (in_array($end_type, array(Properties\Int::TYPE_NAME, Properties\Float::TYPE_NAME))){
                        if ($old_prop['set']){
                            $values = array();
                            foreach($complete_value as $v) {
                                $values[] = ($end_type == Properties\Int::TYPE_NAME) ? round(floatval(str_replace(',', '.', trim($v)))) : floatval(str_replace(',', '.', trim($v)));
                            }
                            return $new_prop['set'] ? $values : reset($values);
                        } else {
                            $value = ($end_type == Properties\Int::TYPE_NAME) ? round(str_replace(',', '.', trim($complete_value))) : floatval(str_replace(',', '.', trim($complete_value)));
                            return $value;
                        }
                    } else {
                        return $complete_value;
                    }
                    break;
                case Properties\Color::TYPE_NAME:
                case Properties\Date::TYPE_NAME:
                    if (in_array($end_type, array(Properties\String::TYPE_NAME, Properties\Text::TYPE_NAME))){
                        return $complete_value;
                    }
                    break;
                case Properties\Int::TYPE_NAME:
                case Properties\Float::TYPE_NAME:
                    if ($old_prop['set']){
                        $values = array();
                        foreach($complete_value as $v) {
                            $values[] = ($end_type == Properties\Int::TYPE_NAME) ? round($v) : floatval($v);
                        }
                        return $new_prop['set'] ? $values : reset($values);
                    } else {
                        return ($end_type == Properties\Int::TYPE_NAME) ? round($complete_value) : floatval($complete_value);
                    }
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Клонирует объекты для пропертей-сущностей
     * @param Property $prop
     * @param $segment_value
     * @return array
     */
    private function clonePropValueObjects(Properties\Property $prop, $segment_value){
        if (!($prop instanceof Properties\Entity)){
            throw new \LogicException("Клонирование можно использовать только для свойств-объектов, передано свойство типа #${prop['data_type']}");
        }
        if (empty($segment_value['value']) || $prop instanceof Properties\CatalogPosition || $prop instanceof Properties\User){
            // Объекты каталога и пользователей не нужно копировать, также возвращаем старое значение для пустых объектов
            return $segment_value['value'];
        } else {
            $old_obj = $prop->getCompleteValue($segment_value);
            if (!empty($old_obj)) {
                if (is_array($old_obj)){
                    $result = array();
                    foreach($old_obj as $obj){
                        // У постов, файлов, картинок и галерей есть метод copy, возвращающий клон объекта
                        $new_obj = $obj->copy();
                        $result[$new_obj['id']] = $new_obj['id'];
                    }
                    return $result;
                }
                $new_obj = $old_obj->copy();
                return $new_obj['id'];
            }
            return $segment_value['value'];
        }
    }

    /**
     * Получаем id поста, с целью сохранить значение в свойство-статью
     * @param $old_prop
     * @param $new_prop
     * @param $value
     * @return array|int|null
     * @throws \ErrorException
     */
    private function transferValueToPost($old_prop, $new_prop, $value){
        if ($old_prop['data_type'] == Properties\Flag::TYPE_NAME || empty($value)) {
            return NULL;
        }
        if ($old_prop['set'] && $new_prop['set']){
            $values = array();
            foreach($value as $v) {
                $v = trim($v);
                if (empty($v)) {
                    continue;
                }
                $post_id = \Models\ContentManagement\Post::create(Properties\Post::POSTS_TYPE);
                if (empty($post_id)){
                    throw new \ErrorException('Не удалось создать пост');
                }
                \Models\ContentManagement\Post::getById($post_id)->edit(array('title' => '', 'text' => '<p>'.$v.'</p>'));
                $values[] = $post_id;
            }
            return $values;
        } else {
            $text = '<p>' . (is_array($value) ? implode('</p><p>', array_filter(array_map('trim', $value))) : $value) . '</p>';
            $post_id = \Models\ContentManagement\Post::create(Properties\Post::POSTS_TYPE);
            if (empty($post_id)){
                throw new \ErrorException('Не удалось создать пост');
            }
            \Models\ContentManagement\Post::getById($post_id)->edit(array('title' => '', 'text' => $text));
            return $post_id;
        }
    }

    /**
     * Находит или создает требуемое значение в перечислении, и возвращает его id
     * @param Properties\Enum $enum_prop
     * @param string $value
     * @return int|NULL
     */
    private function transferEnumPropValue($enum_prop, $value){
        if (empty($value) && !is_numeric($value)){
            return NULL;
        }
        foreach($enum_prop['values'] as $value_id => $value_data){
            if ($value_data['value'] == $value){
                return $value_id;
            }
        }
        // Не нашли подходящего значения в перечислении - создаем новое
        $prop_data = $enum_prop->asArray();
        $values = array();
        foreach($prop_data['values'] as $val_id => $val_data){
            $values['values'][$val_id] = $val_data['value'];
            $values['position'][$val_id] = $val_data['position'];
        }
        $values['values'][] = $value;
        $prop_data['values'] = $values;
        $enum_prop->update($prop_data, $e);
        // И после снова ищем новоявленное значение
        foreach($enum_prop['values'] as $value_id => $value_data){
            if ($value_data['value'] == $value){
                return $value_id;
            }
        }
        throw new \LogicException("Не удалось создать значение перечисления при переносе свойства #${enum_prop['key']}");
    }
    /**
     * Получение значений новых свойств для переноса объекта в другой тип, и удаление значений старых свойств
     * @param Properties\Property $old_prop
     * @param Properties\Property $new_prop
     * @return array
     */
    private function transferProperty($old_prop, $new_prop){
        // Если нашли соответствие - копируем пропертю
        // За исключениес случая сегментированного исходного и несегментированного конечного свойства
        $segments = array(0 => array());
        $segments += \App\Segment::getInstance()->getAll();
        $result = array();
        if (!empty($new_prop) && !($old_prop['segment'] == 1 && $new_prop['segment'] == 0)){
            $old_property_by_segments = $this->getPropertyBySegments($old_prop['key']);
            foreach($segments as $segment_id => $s){
                $old_prop_segment_id = ($old_prop['segment']) ? $segment_id : 0;
                if ($new_prop['segment'] == 1 && $segment_id == 0){
                    continue;
                }
                if (!empty($old_property_by_segments[$old_prop_segment_id])){
                    $new_prop_segment_values = $this->prepareTransferPropValue($old_prop, $new_prop, $old_property_by_segments[$old_prop_segment_id]);
                    if (is_array($new_prop_segment_values)){
                        foreach($new_prop_segment_values as $val){
                            if (empty($val)){
                                continue;
                            }
                            $result[!empty($segment_id) ? $segment_id : 0][] = array('val_id' => 0, 'value' => $val);
                            if (!$new_prop['set']){
                                break;
                            }
                        }
                    } elseif (!empty($new_prop_segment_values)){
                        $result[!empty($segment_id) ? $segment_id : 0] = array(array('val_id' => 0, 'value' => $new_prop_segment_values));
                    }
                }
                if ($new_prop['segment'] != 1){
                    break;
                }
            }
        }
        return $result;
    }
    /**
     * Перенос товара из типа в тип
     * @param Type $new_type
     * @param int[] $propIdsForDelete - список id пропертей, подлежащих удалению при успешном переносе
     * @return array
     */
    protected function changeTypeInner($new_type, &$propIdsForDelete = array()){
        $old_props = $this->getPropertyList();
        $new_props = $new_type->getProperties();
        $common_props = array_intersect_key($new_props, $old_props);
        $prop4create = array_diff_key($new_props, $common_props);
        $prop4delete = array_diff_key($old_props, $common_props);
        $props_copy_ids = $this->getCopyPropIds($prop4delete, $prop4create);
        $update_data = array();
        foreach($prop4delete as $old_prop){
            $data_by_segments = $this->transferProperty($old_prop, !empty($props_copy_ids[$old_prop['id']]) ? $new_props[$props_copy_ids[$old_prop['id']]] : NULL);
            foreach($data_by_segments as $s_id => $data){
                $update_data[$s_id][$old_prop['key']] = $data;
            }
        }
        $propIdsForDelete = !empty($prop4delete) ? array_keys($prop4delete) : array();
        return $update_data;
    }
    /**
     * Вытащить свойства конкретной группы
     * @param string $group_key
     */
    public function getGroupProperties($group_key){
        $properties = array();
        foreach ($this['properties'] as $pr){
            if (!empty($pr['group_id']) && $pr['group']['key'] == $group_key){
                $properties[$pr['key']] = $this[$pr['key']];
            }
        }
        return $properties;
    }

    /******************************* ArrayAccess *****************************/

    public function offsetExists ($offset){
        if ($offset == 'properties'){
            return TRUE;
        }else{
            if (isset($this->data[$offset]) or isset(static::$dataProvidersByFields[$offset]))
                return TRUE;
            if (isset($this->properties[$offset]) && isset($this->properties[$offset]['value'])){
                return TRUE;
            }
            return isset($this->propertyValuesCache[$offset]) && isset($this->propertyValuesCache[$offset]['value']);
        }
    }

    private $propertyValuesCache = null;

    protected function getPropertyValues(){
        if (empty ($this->propertyValuesCache)){//даже если массив $this->properties пустой, заполним пустыми значениями, чтобы всегда были все свойства
            $this->propertyValuesCache = $this->formatPropertyValues($this->properties);
        }
        return $this->propertyValuesCache;
    }

	protected function formatPropertyValues($properties){
		$propertyList = $this->getPropertyList();
		$props = array();
		foreach ($propertyList as $property){
			$prop_key = $property['key'];
			/* @var $property Properties\Property */
			$pValue = isset($properties[$prop_key]) ? $properties[$prop_key] : $this->makePropValue($property, NULL, $this->segment_id); //если нет то заполняем заглушками
			$props[$prop_key] = new PropertyExtension($property, $pValue, $this->segment_id);
		}
		return $props;
	}

    /**
     * @param string $offset
     * @return mixed
     * @throws \Exception
     */
    public function offsetGet ($offset){
        if ($offset == 'properties'){
            return $this->getPropertyValues();
        }else{
            if(isset(static::$dataProvidersByFields[$offset])){
                //echo '<pre>';
                //var_dump($offset);
                //echo '</pre><br />';
                return static::$dataProvidersByFields[$offset]->get($this, $offset);
            }elseif (array_key_exists ($offset, $this->data)){
                return $this->data[$offset];
            }elseif($offset == 'type_id'){
                return $this->getType()->getId();
            }elseif($offset == 'segment_id'){
                return $this->segment_id;
            }else{
                $this->getPropertyValues();
                if (isset($this->propertyValuesCache[$offset])){
                    return $this->propertyValuesCache[$offset]['complete_value'];
                }else{
                    throw new \Exception(
                            'Notice: '.get_class($this).' #'.$this['id'].' Undefined index: "'.$offset.'", having indexes:'.
                            implode(', ', array_keys($this->data)).
                            ' and additional:'.implode(', ', array_keys($this->properties))
                    );
                }
            }
        }
    }

    public function offsetSet ($offset, $value){
        throw new \Exception(get_class($this).' has only immutable Array Access');
    }

    public function offsetUnset ($offset){
        throw new \Exception(get_class($this).' has only immutable Array Access');
    }
}