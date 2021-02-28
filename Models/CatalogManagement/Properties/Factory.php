<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Factory
 *
 * @author olga
 */

namespace Models\CatalogManagement\Properties;

use Models\CatalogManagement\Type;
use Models\CatalogManagement\CatalogHelpers\Interfaces\iPropertyDataProvider;
use Models\CatalogManagement\Item as ItemEntity;
use Models\CatalogManagement\Variant as VariantEntity;
use App\Configs\CatalogConfig;
use MysqlSimple\Logger;

class Factory {
    /*     * таблица свойств */
    const TABLE = 'properties';
    const TABLE_PROPERTIES = self::TABLE;
    const TABLE_FIELDS = 'properties_fields';
    /**
     * Разделитель для значений (через GROUP CONCAT)
     */
    const VALUE_SEPARATOR = '|';
    
    const FILES_CLASS_PATH = 'Models/CatalogManagement/Properties/';
    const DEFAULT_DATA_TYPE = String::TYPE_NAME;
    /**
     * Константы определяющие поведение поиска по данному свойству
     */
    const SEARCH_BETWEEN = 'between';      // Поиск осуществляется установкой диапазона ! только для типов int и float
    const SEARCH_SELECT = 'select';       // Поиск осуществляется выбором элмента из списка ! для всех типов кроме flag
    const SEARCH_AUTOCOMPLETE = 'autocomplete'; // Поиск осуществляется с автозаполнением ! для всех типов кроме flag
    const SEARCH_CHECK = 'check';  // Поиск осуществляется с помощью галочки ! для всех типов кроме flag
    const SEARCH_NONE = 'none';         // Поиск по данному полю не осуществляется
    /**
     * Параметры, которые вытаскиваем из базы
     * @see Catalog::checkAllowed()
     * @var array
     */
    static protected $loadParams = array(
        'id',
        'type_id', // В рамках какого типа описано данное свойство
        'origin_type_id', // Из какой категории было взято проброшенное свойство
        'title', // Наименование свойства
        'description', // Описание свойства, для отображение в подсказках
        'public_description', //описание для паблика
        'key', // Keyword на латинице для поиска и отобрадения в URL и прочих мест. Человеко понятный идентификатор
        'data_type', // Тип данных одно из следующих значений: static::TYPE_*
        'major', // Мажорное свойство -  NULL - свойство не используется для подбора похожих, NOT NULL - разброс значений мажорного параметра
        'search_type', // Тип поиска: const SEARCH_*
        'visible', // Видимость свойства: const VIEW_*
        'values', // Возможные значения min:M,max:N,step:S для int или float (для enum отдельно)
        'validation', // Параметры валидации значения
        'mask', // Шаблон отображения
        'filter_title', // Название в фильтре
        'necessary', // Обязательный
        'unique', // Уникальный
        'multiple', // Возможно наличие нескольких вариантов для одного элемента каталога (тоесть возможно ли наличие этой характеристики у объекта)
        'group_id', // ID группы
        'position', //Позиции enum значений
        'image_id', //Id картинки
        'read_only', //только для чтения (в редактировании товара поле disabled)
        'set', //множественное
        'filter_visible', //видимость свойства в фильтре
        'fixed',
        'filter_slide',
        'segment',
		'context',
		'sort',
		'external_key',
        'default_prop', // 1 для свойства по-умолчанию
        'default_value'
    );
    /**
     * Колекция уже созданных Property, чтобы они не терялись
     * @var Property[]
     */
    static protected $registry = array();
    /**
     * Колекция уже прочитанных данных. нужна для того чтобы можно было создать объекты быстро
     * @var array[]
     */
    static protected $dataCache = array();
    /**
     * Кеш возможных значений для перечислений
     * @var array
     */
    static protected $enumValuesCache = array();
    /**
     * Кэш доп параметров, разбитых по сегментам
     * @var array 
     */
    static protected $segmentDataCache = array();
    /**
     * Список id Enum совйств, для которых нужно прочитать Enum значения
     * @var array 
     */
    static protected $prop_ids4load_enums_query = array();
    /**
     *
     * @var type 
     */
    static protected $data_type_exists = array();
    /**
     * Чтение данных из которых потом собираются наименования
     *
     * @param int[] $ids
     * @return array[]
     */
    private static function cacheUpData($ids) {
        $db = \App\Builder::getInstance()->getDB();
        // в кеш данных не нужно читать данные уже созданных объектов и данные которые есть в кеше
        $ids = array_unique(array_merge(self::$ids4Load, $ids)); // объединяем запрашиваемое с тем что предположительно тоже надо подготовить
        self::$ids4Load = array(); //отчистили
        $ids = array_flip($ids); //гарантируем ключи, с ключами быстрее работается
        $ids = array_diff_key($ids, self::$dataCache, self::$registry); //проводим быстрый вычет имеющегося
        $ids = array_flip($ids);
        if (!empty($ids)) {
            $result = $db->query('
                SELECT `p`.`' . implode('`, `p`.`', self::$loadParams) . '`
                FROM `' . self::TABLE . '` AS `p`
                WHERE `p`.`id` IN (?i)', $ids
                    )->select('id');
            //кешируем значения enum-ов
            $enumData = self::loadEnumsData($ids);
            //объединение старых данных в кеше с новыми (старые затираются)
            self::$enumValuesCache = $enumData + self::$enumValuesCache;
            foreach ($ids as $id) {
                if (!empty($id)) {
                    self::$dataCache[$id] = !empty($result[$id]) ? $result[$id] : NULL;
                }
            }
        }
    }

    /**
     * @return array
     */
    public static function getPropertyTypesList(){
        return array_keys(CatalogConfig::getPropertyFieldsData('data_type'));
    }
    //разрешения для заполнения параметров для каждого типа свойства
    public static function getPropertyAllows($data_type = NULL){
        if (is_null($data_type)){
            $propTypeList = self::getPropertyTypesList();
        }else{
            $propTypeList = array($data_type);
        }
        $allows = array();
        foreach ($propTypeList as $pt){
            $pt_class = self::getDataTypeClass($pt);
            $allows[$pt] = array(
                'values_array' => $pt_class::VALUES_TYPE_ARRAY,
                'unacceptable_params' => $pt_class::getUnacceptableParams()
            );
        }
        if (!is_null($data_type)){
            return reset($allows);
        }
        return $allows;
    }
    /**
     * считать св-в enum
     * @param array $prop_ids - массив ид свойств
     * @return array
     */
    public static function loadEnumsData(Array $prop_ids) {
        $db = \App\Builder::getInstance()->getDB();
        $result = $db->query('
			SELECT `property_id`, `id`, `value`, `position`, `key`
			FROM ?# WHERE `property_id` IN (?i) ORDER BY `position`', Enum::TABLE_PROP_ENUM, $prop_ids)
                ->select('property_id', 'id');
        return $result;
    }
    public static function getDataTypeClass($data_type){
        if (!self::isPropertyDataTypeExists($data_type, $property_class)){
            throw new \ErrorException("Запрошен несуществующий тип данных ${data_type}");
        }
        return $property_class;
    }
    /**
     * Проверяет, существует ли класс свойства
     * @param string $data_type тип данных свойства
     * @param string $property_class возвращает имя класа с неймспейсом
     * @return boolean
     */
    public static function isPropertyDataTypeExists($data_type, &$property_class = NULL) {
        if (empty($data_type)){
            return FALSE;
        }
        $property_class = __NAMESPACE__ . '\\' . ucfirst($data_type);
        if (!array_key_exists($data_type, self::$data_type_exists)){
            self::$data_type_exists[$data_type] = class_exists($property_class);
        }
        return self::$data_type_exists[$data_type];
    }

    /**
     * Проверяет существует ли свойство
     * @param int $id идентификатор свойства
     * @return bool
     */
    public static function isPropertyExist($id) {
        if (isset(self::$dataCache[$id]) or isset(self::$registry[$id]))
            return TRUE;
        $db = \App\Builder::getInstance()->getDB();
        return $db->query('SELECT 1 FROM `' . self::TABLE . '` WHERE `id`=?d', $id)->getCell();
    }

    /**
     * Возвращает список свойств, если свойства с заданным id нет, то на его месте стоит NULL
     * @param int[] $ids [ => property id]
     * @return Property[] формата property id => Property
     */
    public static function get($ids, $segment_id = NULL) {
        foreach ($ids as $k => $id) {
            if (empty($id)) {
                unset($ids[$k]); //вычистили пустые id
            }
        }
        //почему используются только id шники? потому что:
        //    делать запросы по property key нельзя т.к. тогда надо было бы указывать какие типы подразумеваются,
        //    возвращать массив с ключами нельзя, т.к. не понятно что делать с теми свойствами, которых нет в базе
        self::cacheUpData($ids); //данные гаратированно в кеше
        $result = array();
        foreach ($ids as $id) {
            if (!array_key_exists($id, self::$registry)) {
                if (!empty(self::$dataCache[$id])) {
                    $property_class = NULL;
                    if (!self::isPropertyDataTypeExists(self::$dataCache[$id]['data_type'], $property_class)) {
                        throw new \LogicException('Неверный тип данных свойства: "' . self::$dataCache[$id]['data_type'] . '" #id: ' . $id);
                    }
                    self::$registry[$id] = new $property_class(self::$dataCache[$id], $segment_id);
                }
                unset(self::$dataCache[$id]); // данные использованы - объект создан и помещен в коллекцию объектов, более чистые данные не нужны
            }
            if (!empty(self::$registry[$id])) {
                $result[$id] = self::$registry[$id];
            }
        }
        return $result;
    }

    /**
     * @var type array
     */
    static private $ids4Load = array();

    /**
     * Массив id шников которые желательно прочитать в кеш "за компанию, на всякий случай",
     * но не создавать из них объектов, когда будет какое-либо первое чтение данных.
     * Этот механизм позволяет накидать сразу много id и считать их единым махом,
     * минимизируя таким образом будующие запросы в базу.
     *
     * @param int|int[] $ids
     * @param bool $clear
     * @throws \Exception
     */
    public static function prepareId($ids, $clear = FALSE) {
        if ($clear)
            self::$ids4Load[$ids] = array();
        if (!is_array($ids)) {
            if (!empty($ids)) {
                $ids = intval($ids);
                self::$ids4Load[$ids] = $ids;
            }
        } else {
            foreach ($ids as $id) {
                if (!empty($ids)) {
                    $id = intval($id);
                    self::$ids4Load[$id] = $id;
                }
            }
        }
    }

    /**
     * Возвращает конкретный $prop
     * @param int $id
     * @return Property
     */
    public static function getById($id, $segment_id = NULL) {
        if (empty($id)) {
            return null;
        }
        $props = self::get(array($id), $segment_id);
        if (empty($props))
            return null;
        return reset($props);
    }

    /**
     * Возвращает проперти из нужного типа по ключу
     * @param string $key
     * @param int[] $type_ids
     * @param string $keys возможные значения id(по умолчанию), key, idByKey
     * @param string $searchIn среди каких типов искать
     *        self только свои,
     *        parents свои + родительские (по умолчанию),
     *        children свои + дочерние
     * @param int $segment_id сегмент
     * @return Property[]
     */
    public static function getByKey($key, $type_ids = null, $keys = 'key', $searchIn = 'parents', $segment_id = NULL) {
        return self::search(!empty($type_ids) ? $type_ids : Type::DEFAULT_TYPE_ID, self::P_ALL, $keys, 'position', $searchIn, array('key' => $key), $segment_id);
    }

    /**
     * Возвращает единственное проперти из нужного типа по ключу (либо глобальное, либо не имеющее аналогов в соседних ветках переданного типа)
     * @param string $key
     * @param int[] $type_ids
     * @param string $searchIn среди каких типов искать
     *        self только свои,
     *        parents свои + родительские (по умолчанию),
     *        children свои + дочерние
     * @param int $segment_id сегмент
     * @throws \LogicException
     * @return Property
     */
    public static function getSingleByKey($key, $type_ids = null, $searchIn = 'parents', $segment_id = NULL) {
        $props = self::getByKey($key, $type_ids, 'id', $searchIn, $segment_id);
        if (count($props) > 1){
            throw new \LogicException('Запрашивается одно свойство, а найдено несколько');
        }
        $prop = reset($props);
        if (!empty($prop)){
            // если пользователь может редактировать ключ свойства, использование ключа в коде ненадежно
            if (is_numeric($prop['fixed']) && $prop['fixed'] == Property::FIXED_NO || !is_numeric($prop['fixed']) && strpos($prop['fixed'], 'key') === FALSE){
                throw new \LogicException('Ключ свойства не защищен от редактирования');
            }
        }
        return $prop;
    }

    /**
     * Возвращает данные для создания копии свойства по умолчанию
     * @TODO зачем asArray()? если уж так, то не проще ли array_intersect_key(array('title','data_type', ...), $prop), а потом добавить key 
     * @param Property $property
     * @return array|null
     */
    public static function getDefaultPropertyData(Property $property){
        if (!$property['default_prop']){
            return NULL;
        } else {
            $prop = $property->asArray();
            return array(
                'title'         => $prop['title'],
                'data_type'     => $prop['data_type'],
                'search_type'   => $prop['search_type'],
                'key'           => substr($prop['key'], -strlen(Property::DEFAULT_PROP_SUFFIX)) == Property::DEFAULT_PROP_SUFFIX ? substr($prop['key'], 0, -strlen(Property::DEFAULT_PROP_SUFFIX)) : $prop['key'],
                'visible'       => $prop['visible'],
                'multiple'      => $prop['multiple'],
                'fixed'         => $prop['fixed'],
                'segment'       => $prop['segment']
            );
        }
    }

    public static function clearCache($id = NULL) {
        if (!empty($id) && !empty(self::$registry[$id])) {
            unset(self::$registry[$id]);
        } else {
            self::$registry = array();
        }
        if (!empty($id) && !empty(self::$dataCache[$id])) {
            unset(self::$dataCache[$id]);
        } else {
            self::$dataCache = array();
        }
        self::clearEnumValuesCache($id);
        self::$searchDataCache = array();
        if (!empty($id) && !empty(self::$segmentDataCache[$id])) {
            unset(self::$segmentDataCache[$id]);
        } else {
            self::$segmentDataCache = array();
        }
    }

    public static function clearEnumValuesCache($id = NULL) {
        if (!empty($id) && !empty(self::$enumValuesCache[$id])) {
            unset(self::$enumValuesCache[$id]);
        } else {
            self::$enumValuesCache = array();
        }
    }

    public static function getEnumValuesCache() {
        return self::$enumValuesCache;
    }
    /**
     * все параметры
     * @see self::getProperties
     */
    const P_ALL = 0;

    /**
     * Параметры, участвующие в поиске
     * @see self::search
     */
    const P_SEARCH = 1;

    /**
     * Параметры, которые не комбинируемые (для вывода свойств объекта при редактировании)
     * @see self::search
     */
    const P_NOT_VIEW = 2;

    /**
     * Параметры, только комбинируемого типа (для перегенерации после редактирования)
     * @see self::search
     */
    const P_VIEW = 4;

    /**
     * Параметры, присущие только Items
     * @see self::search
     */
    const P_ITEMS = 8;

    /**
     * Параметры, присущие только Variants
     * @see self::search
     */
    const P_VARIANTS = 16;

    /**
     * Мажорные параметры
     * @see self::search
     */
    const P_MAJOR = 32;

    /**
     * Сегментированные параметры
     */
    const P_SEGMENT = 64;

    /**
     * Не сегментированные параметры
     */
    const P_NOT_SEGMENT = 128;

    /**
     * Одиночные свойства
     */
    const P_NOT_SET = 256;

    /**
     * Свойства на экспорт
     */
    const P_EXPORT = 512;

    /**
     * уникальные свойства
     */
    const P_UNIQUE = 1024;
    /**
     * Свойства по умолчанию
     */
    const P_DEFAULT = 2048;

    const P_NOT_DEFAULT = 4096;
    /**
     * Свойства диапазон
     */
    const P_RANGE = 8192;
    
    const P_NOT_RANGE = 16384;
    /**
     * Не показывать свойства-сущности (айтемы, варианты, файлы, изображения и т.д.)
     */
    const P_NOT_ENTITY = 32768;
    /**
     * Не показывать скрытые разработчиками свойства (fixed = hide)
     */
    const P_NOT_HIDE = 65536;

    /**
     * Кэш параметров поиска (чтобы не искать одно и то же)
     * @var array 
     */
    static protected $searchDataCache = array();

    /**
     * Возвращает подходящие Property под условия поиска, задавать фильтры нужно через |
     * @see self::P_* constants
     * @param int|int[] $type_id Тип
     * @param int $filter собранные фильтры (self::P_* constants) через | operand
     * @param string $keys возможные значения id (по умолчанию), key, idByKey
     * @param string $sort position|group сортировка только по позиции свойства(по умолчанию) или по позициям групп
     * @param string $getIn какие свойства брать?
     *    возможные значения:
     *        self - только свои,
     *        parents - свои + родительские (по умолчанию)
     *        children - свои + дочерние
     * @param array $params доп условия. например: array('group_key' => 'uses')
     *  возможные ключи:
     *      group_key,
     *      key,
     *      not_key,
     *      filter_visible,
     *      visible,
     *      external_key,
     *      data_type,
     *      values
     * @param int $segment_id
     * @throws \Exception
     * @return Property[]
     */
    public static function search($type_id, $filter = self::P_ALL, $keys = 'id', $sort = 'type_group', $getIn = 'parents', $params = array(), $segment_id = NULL, &$cache_key = NULL) {
        $propList = self::get(self::searchIds($type_id, $filter, $sort, $getIn, $params, $segment_id, $cache_key), $segment_id);
        if ($keys == 'id') {
            return $propList;
        } elseif ($keys == 'key') {
            $propListByKeys = array();
            foreach ($propList as $property) {
                $propListByKeys[$property['key']] = $property;
            }
            $propList = $propListByKeys;
        } elseif ($keys == 'idByKey') {
            $propListByKeys = array();
            foreach ($propList as $property) {
                $propListByKeys[$property['key']][$property['id']] = $property;
            }
            $propList = $propListByKeys;
        } else {
            throw new \Exception('inccorect $keys value');
        }
        return $propList;
    }

    public static function clearSearchDataCache($cacheKey = NULL){
        $dbg = var_export($cacheKey, true);
        if (empty($cacheKey)){
            self::$searchDataCache = array();
        } else {
            $dbg .= PHP_EOL.'------'.PHP_EOL.var_export(self::$searchDataCache, true);
            unset(self::$searchDataCache[$cacheKey]);
            $dbg .= PHP_EOL.'------'.PHP_EOL.var_export(self::$searchDataCache, true);
            if (strpos($cacheKey, '27;0;id;group;s:7:"parents";') === 0) throw new \Exception($dbg, true);
        }
    }

    public static function getCacheKey($type_id, $filter = self::P_ALL, $sort = 'type_group', $getIn = 'parents', $params = array(), $segment_id = NULL, &$types_ids = array(), &$sort_string = ''){
        if ($sort == 'group') {
            $sort = '`g`.`type_id`, `g`.`position`, `p`.`position`';
        } elseif ($sort == 'type_group') {
            $sort = '`g`.`type_id`, `g`.`position`, `p`.`type_id`, `p`.`position`';
        } elseif ($sort == 'type_position') {
            $sort = '`p`.`type_id`, `p`.`position`';
        } elseif ($sort == 'position') {
            $sort = '`p`.`position`';
        }
        $sort_string = $sort;
        if (!is_array($type_id)) {
            $type_id = array($type_id);
        }
        $types_ids = $type_id;
        if ($getIn == 'parents') {
            $types = Type::factory($type_id, $segment_id);
            foreach ($types as $type) {
                $types_ids = array_merge($types_ids, !empty($type['parents']) ? $type['parents'] : array());
            }
        } elseif ($getIn == 'children') {//дочерние нужны для того, чтобы переписывать все свойства view, если поменялся ключ. @see Property::recreateView
            $types = Type::factory($type_id);
            foreach ($types as $type) {//для recreateView count($types) = 1, поэтому запрос будет только один
                $children = $type->getAllChildren(array(TYPE::STATUS_VISIBLE, TYPE::STATUS_HIDDEN)); //т.к. нужны только при recreateView, то вытаскиваются все статусы, как только понадобится для внешней части сайта, надо будет переделать.
                foreach ($children as $inner_children) {
                    /** @var $inner_children int[] */
                    $types_ids = array_merge($types_ids, !empty($inner_children) ? array_keys($inner_children) : array());
                }
            }
        }
        $types_ids = array_unique($types_ids);
        sort($types_ids);
        return implode('.', $types_ids) . ';' . $filter . ';' . $sort . ';' . $getIn . ';' . serialize($params);
    }
    
    public static function searchIds($type_id, $filter = self::P_ALL, $sort = 'type_group', $getIn = 'parents', $params = array(), $segment_id = NULL, &$cache_key = NULL){
        $cache_key = self::getCacheKey($type_id, $filter, $sort, $getIn, $params, $segment_id, $types_ids, $sort_string);
        if (!isset(self::$searchDataCache[$cache_key])) {
            $where = '';
            if ($filter & self::P_SEARCH)
                $where .= ' AND `search_type` != "none"';
            if ($filter & self::P_NOT_VIEW)
                $where .= ' AND `data_type` != "view"';
            elseif ($filter & self::P_VIEW)
                $where .= ' AND `data_type` = "view"';
            if ($filter & self::P_ITEMS)
                $where .= ' AND `multiple` IS NULL';
            elseif ($filter & self::P_VARIANTS)
                $where .= ' AND `multiple` = 1';
            if ($filter & self::P_MAJOR)
                $where .= ' AND `major` IS NOT NULL';
            if ($filter & self::P_SEGMENT)
                $where .= ' AND `segment` = 1';
            elseif ($filter & self::P_NOT_SEGMENT)
                $where .= ' AND `segment` IS NULL';
            if ($filter & self::P_NOT_SET)
                $where .= ' AND `set` IS NULL';
            if ($filter & self::P_EXPORT)
                $where .= ' AND `p`.`visible` & '.CatalogConfig::V_EXPORT.' > 0';
            if ($filter & self::P_UNIQUE)
                $where .= ' AND `unique` = 1';
            if ($filter & self::P_DEFAULT)
                $where .= ' AND `default_prop` = 1';
            elseif ($filter & self::P_NOT_DEFAULT)
                $where .= ' AND `default_prop` IS NULL';
            if ($filter & self::P_RANGE)
                $where .= ' AND `data_type` = "range"';
            elseif ($filter & self::P_NOT_RANGE)
                $where .= ' AND `data_type` != "range"';
            if ($filter & self::P_NOT_ENTITY) {
                /** @TODO дополнять список типов, или придумать, как автоматизировать */
                $where .= ' AND `data_type` NOT IN ("image", "gallery", "post", "item", "variant", "user", "file")';
            }
            if ($filter & self::P_NOT_HIDE){
                $where .= ' AND `fixed` != 2';
            }
            $db = \App\Builder::getInstance()->getDB();
            $ids = $db->query('
                SELECT `p`.`id`
                FROM `' . self::TABLE . '` AS `p`
                LEFT JOIN `' . Type::TABLE_PROPERTIES_GROUPS . '` AS `g` ON (`g`.`id` = `p`.`group_id`)
                WHERE `p`.`type_id` IN (?i)
                { AND (`p`.`origin_type_id` IN (?i) OR `p`.`origin_type_id` IS NULL)}
                { AND  ?d AND `p`.`origin_type_id` IS NOT NULL}
                { AND `g`.`key` IN (?l)}
                { AND `p`.`key` IN (?l)}
                { AND `p`.`key` NOT IN (?l)}
                { AND `p`.`filter_visible` & ?d > 0}
                { AND `p`.`visible` & ?d > 0}
				{ AND `p`.`external_key` = ?s}
                { AND `p`.`data_type` IN (?l)}
                { AND `p`.`values` = ?s}
                ' . $where . '
                ORDER BY ' .
                $sort_string,
				array_unique($types_ids),
                !empty($params['origin_type_id'])
                    ? (is_array($params['origin_type_id']) ? $params['origin_type_id'] : array($params['origin_type_id']))
                    : $db->skipIt(),
                !empty($params['transfered_prop']) ? 1 : $db->skipIt(),
				!empty($params['group_key']) 
					? (is_array($params['group_key']) ? $params['group_key']
					: array($params['group_key'])) : $db->skipIt(), 
				!empty($params['key']) 
					? (is_array($params['key']) ? $params['key'] 
					: array($params['key'])) : $db->skipIt(), 
				!empty($params['not_key'])
					? (is_array($params['not_key']) ? $params['not_key']
					: array($params['not_key'])) : $db->skipIt(),
				!empty($params['filter_visible']) ? $params['filter_visible'] : $db->skipIt(),
				!empty($params['visible']) ? $params['visible'] : $db->skipIt(),
				!empty($params['external_key']) ? $params['external_key'] : $db->skipIt(),
                !empty($params['data_type']) ? (is_array($params['data_type']) ? $params['data_type'] : array($params['data_type'])) : $db->skipIt(),
                !empty($params['values']) ? $params['values'] : $db->skipIt()
			)->getCol('id', 'id');
            self::$searchDataCache[$cache_key] = $ids;
        }
        return self::$searchDataCache[$cache_key];
    }

    /**
     * Проверяет существует ли в базе свойство с заданным ключем
     * @TODO возможно стоит передавать объект типа, а не id, т.к. segment тут явно лишний
     * @param string $key текстовый идентификатор свойства
     * @param int $type_id цифровой идентификатор типа
     * @param int $id цифровой идентификатор конкретного свойства
     * @param int $segment_id в каком сегменте брать тип
     * @return bool
     */
    public static function isPropertyKeyExist($key, $type_id = 1, $id = NULL, $segment_id = NULL, $check_default_key = TRUE) {
        if (empty($key)) {
            return TRUE;
        }
        $type = Type::getById($type_id, $segment_id);
//        throw new \Exception(var_export($type_id, true));
        $children = $type->getAllChildren();
        $types_ids = array();
        foreach ($children as $inner_children) {
            /** @var $inner_children int[] */
            $types_ids = array_merge($types_ids, !empty($inner_children) ? array_keys($inner_children) : array());
        }
        $checked_types = array_merge(array($type_id), $type['parents'], $types_ids);
        $db = \App\Builder::getInstance()->getDB();
        return $db->query('SELECT 1 FROM `' . self::TABLE . '` WHERE (`key`=?{ OR `key`=?}) {AND `id`!=?d} AND `type_id` IN (?i)', $key, $check_default_key ? $key . Property::DEFAULT_PROP_SUFFIX : $db->skipIt(), !empty($id) ? $id : $db->skipIt(), $checked_types
                )->getCell() ? TRUE : FALSE;
    }

    public static function cleanup() {
        $db = \App\Builder::getInstance()->getDB();
        foreach (array(ItemEntity::TABLE_PROP_INT, ItemEntity::TABLE_PROP_STRING, ItemEntity::TABLE_PROP_FLOAT, VariantEntity::TABLE_PROP_INT, VariantEntity::TABLE_PROP_STRING, VariantEntity::TABLE_PROP_FLOAT, Enum::TABLE_PROP_ENUM) as $table) {
            // Удаляются те записи, которые никуда вообще не ссылаются или ссылаются на удаленные Property
            $db->query('
                DELETE `pv` FROM ?# AS `pv`
                    LEFT JOIN ?# AS `p` ON (`pv`.`property_id` = `p`.`id`)
                    WHERE `p`.`id` IS NULL', $table, self::TABLE
            );
        }
    }

    public static function getEditableParams() {
        return Property::getEditableParams();
    }

    public static function getSegmentDataCache() {
        return self::$segmentDataCache;
    }

    public static function _setSegmentField($id, $segment_id, $field, $value) {
        if (is_null($segment_id)) {
            $segments = \App\Builder::getInstance()->getSegments();
            foreach ($segments as $s) {
                self::$segmentDataCache[$id][$field][$s['id']] = $value;
            }
        } else {
            self::$segmentDataCache[$id][$field][$segment_id] = $value;
        }
    }

    /**
     * @static
     * @param iPropertyDataProvider $provider
     */
    static function addDataProvider(iPropertyDataProvider $provider) {
        return Property::addDataProvider($provider);
    }

    /**
     * @static
     * @param iPropertyDataProvider $provider
     */
    static function delDataProvider(iPropertyDataProvider $provider) {
        return Property::delDataProvider($provider);
    }

}

?>