<?php
/**
 * Абстрактный класс свойства
 * Каждый наследник этого класса определяет свойства с одним типом данных
 * Методы, которые может потребоваться переопределить:
 *  checkData()     //проверяет специфичные для типа данных параметры перед сохранением
 *  pack()          //пакует данные перед записью в базу
 *  unpack()        //распаковывает данные из базы
 *  formatValue()   //берет значение и формирует из него конечный вид, опираясь на поля свойства
 *  isValuesChanged()//поменялись ли значения свойства
 *  isRequired()    //проверка свойства на необходимость заполнения
 *  getDataTypeTable() //получить таблицу в которой лежат значения свойства с определенным типом
 *  save()          //сохранение
 *  explicitType() //явное приведение типов
 *
 * @author olga
 */

namespace Models\CatalogManagement\Properties;

use Models\CatalogManagement\Catalog;
use Models\CatalogManagement\Item as ItemEntity;
use Models\CatalogManagement\Variant as VariantEntity;
use App\Configs\CatalogConfig;
use Models\CatalogManagement\Type;
use Models\CatalogManagement\CatalogHelpers\Interfaces\iPropertyDataProvider;
use Models\CatalogManagement\Group;
use Models\CatalogManagement\PropertyImageCollection;
use Models\Validator;

abstract class Property implements \ArrayAccess {
    const FIXED_NO = 0;
    const FIXED_FIX = 1;
    const FIXED_HIDE = 2;
    /**
     * ключ параметра для определения отображения свойств-объектов в фильтре
     */
    const FILTER_VIEW_KEY = 'title';
    /**
     * обозначение значения свойства в шаблоне mask
     * @see self::markerReplace()
     */
    const MARKER = '{!}';
    /**
     * обозначение аналога plural_form в шаблоне mask (начальные символы)
     * @see self::markerReplace()
     */
    const MARKER_WORD_FORM = '{?';
    /**
     * обозначение аналога plural_form в шаблоне mask (обозначение окончания)
     * @see self::markerReplace()
     */
    const MARKER_WORD_FORM_END = '}';
    /**
     * обозначение разделителя слов для аналога plural_form в шаблоне mask
     * @see self::markerReplace()
     */
    const MARKER_WORD_FORM_DELIM = '|';
    /**
     * разрешаем множественность свойства
     */
    const ALLOW_SET = TRUE;
    /**
     * разрешаем использовать маркеры
     */
    const ALLOW_MASK = TRUE;
    /**
     * разрешаем использовать фильтрацию
     */
    const ALLOW_FILTER = TRUE;
    /**
     * разрешаем использовать подбор похожих
     */
    const ALLOW_MAJOR = TRUE;
    /**
     * свойство может быть у вариантов
     */
    const ALLOW_MULTIPLE = TRUE;
    /**
     * свойство может быть дефолтным
     */
    const ALLOW_DEFAULT = TRUE;
    /**
     * свойство можно использовать в фильтре
     */
    const ALLOW_SEARCH_TYPE = TRUE;
    /**
     * у свойства можно добавлять параметр values
     */
    const ALLOW_VALUES = TRUE;
    /**
     * можно ли загружать картинку
     */
    const ALLOW_IMAGE = TRUE;
    /**
     * можно ли делать свойство сегментированным
     */
    const ALLOW_SEGMENT = TRUE;
    /**
     * можно ли сортировать свойство
     */
    const ALLOW_SORT = TRUE;
    /**
     * ожидаемый формат поля values array/string
     */
    const VALUES_TYPE_ARRAY = TRUE;
    /**
     * Суффикс ключа свойства по-умолчанию (свойства, автоматически пересоздаваемые в конечных типах)
     */
    const DEFAULT_PROP_SUFFIX = '_tmpl';
    protected $segment_id = NULL;
    protected $data = NULL;
    /**
     * если values массив - здесь список допускаемых полей, пустой если поля могут быть любыми
     * @var array
     */
    protected static $values_fields = array();
    /**
     * Разрешенные параметры для свойства при редактировании
     * @see Catalog::checkAllowed()
     * @var array
     */
    static protected $allowParams = array(
        'type_id', // В рамках какого типа описано данное свойство
        'origin_type_id', // Из какой категории было взято проброшенное свойство
        'title', // Наименование свойства
        'description', // Описание свойства, для отображение в подсказках
        'public_description', //описание для паблика
        'key', // Keyword на латинице для поиска и отобрадения в URL и прочих мест. Человеко понятный идентификатор
        'data_type', // Тип данных одно из следующих значений: static::TYPE_*
        'major', // Мажорное свойство -  NULL - свойство не используется для подбора похожих, NOT NULL - разброс значений мажорного параметра
        'search_type', // Тип поиска: const SEARCH_*
        'visible', // Видипость свойства: const VIEW_*
        'values', // Возможные значения min:M,max:N,step:S для int или float (для enum отдельно)
        'validation', // Параметры валидации значения
        'mask', // Шаблон отображения
        'filter_title', // Название в фильтре
        'necessary', // Обязательный
        'unique', // Уникальный
        'multiple', // Возможно наличие нескольких вариантов для одного элемента каталога (тоесть возможно ли наличие этой характеристики у объекта)
        'group_id', // ID группы
        'position', //Позиция
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
        'default_value',
        'validation'
    );
    /**
     * Свойства-флаги, которые если пустые или равны нулю, можно прописать NULL
     * @var array
     */
    private static $flag_properties = array(
        'necessary',
        'unique',
        'multiple',
        'read_only',
        'set',
        'segment',
        'default_prop'
    );
    /**
     * Поля свойства, при изменении которых надо чистить кэш айтемов\вариантов
     * @var array
     */
    protected static $fieldsClearCache = array(
        'key',
        'data_type',
        'values',
        'mask',
        'multiple',
        'set',
        'segment',
		'sort',
        'default_prop', // 1 для свойства по-умолчанию
        'default_value'
    );
/**
 * @TODO почему мы не хотим все коды ошибок хранить в валидаторе и пользоваться ими?
 */
    const ERR_MSG_EMPTY = Validator::ERR_MSG_EMPTY;
    const ERR_MSG_EXISTS = Validator::ERR_MSG_EXISTS;
    const ERR_MSG_INCORRECT_FORMAT = Validator::ERR_MSG_INCORRECT_FORMAT;
    const ERR_MSG_RESERVED = 'reserved';
    const ERR_MSG_DISALLOW = 'disallow';

    public static function getEditableParams() {
        return array_merge(self::$allowParams, array_keys(static::$dataProvidersByFields));
    }
    /**
     * разрешения для параметров в конкретном типе данных свойства
     * @return type
     */
    public static function getUnacceptableParams(){
        return array(
            'major' => static::ALLOW_MAJOR,
            'search_type' => static::ALLOW_SEARCH_TYPE,
            'values' => static::ALLOW_VALUES,
            'mask' => static::ALLOW_MASK,
            'multiple' => static::ALLOW_MULTIPLE,
            'image_id' => static::ALLOW_IMAGE,
            'set' => static::ALLOW_SET,
            'segment' => static::ALLOW_SEGMENT,
            'sort' => static::ALLOW_SORT,
            'default_prop' => static::ALLOW_DEFAULT
        );
    }
    /**
     * возвращает в какой таблице хранится тот или иной тип данных для конкетрных позиций каталога
     * не требует переопределения, т.к. получает dataType
     * @param string $dataType Property type
     * @param string $multiple Property flag "multiple"
     * @return string Table name
     */
    public static final function getValuesTable($dataType, $multiple) {
        if (!Factory::isPropertyDataTypeExists($dataType, $property_class)) {
            throw new \LogicException('Тип данных "' . $dataType . '" не существует');
        }
        return $property_class::getDataTypeTable($multiple);
    }
    /**
     * Отдает название таблицы со значениями свойств в зависимости от расщепляемости
     * переопределяется для разных типов данных
     * @param boolean $multiple
     * @return string
     */
    protected static function getDataTypeTable($multiple) {
        return $multiple ? VariantEntity::TABLE_PROP_STRING : ItemEntity::TABLE_PROP_STRING;
    }
    /**
     * смена типа данных свойства. т.к. типы свойства - это отдельные классы, возвращает новый объект другого класса
     * @param int $id
     * @param string $new_type
     * @return Property
     * @throws \LogicException
     */
    private static final function changeDataType($id, $new_type) {
        $property_class = NULL;
        if (!Factory::isPropertyDataTypeExists($new_type, $property_class)) {
            throw new \LogicException('Тип данных "' . $new_type . '" не существует');
        }
        $db = \App\Builder::getInstance()->getDB();
        $db->query('UPDATE `' . Factory::TABLE . '` SET `data_type` = ?s WHERE `id` = ?d', $new_type, $id);
        Factory::clearCache($id);
        return Factory::getById($id);
    }
    /**
     * Создать новое свойство
     *
     * @param int $type_id к какому типу принадлежит
     * @param array $data данные
     * @param array $errors
     * @param int|null $segment_id
     * @param bool $dont_check_default_key
     * @return int
     * @throws \ErrorException
     * @throws \Exception
     * @throws \MysqlSimple\Exceptions\InvalidArgumentException
     */
    public static function create($data, &$errors, $segment_id = NULL, $dont_check_default_key = FALSE) {
        if (empty($data['data_type'])){
            $errors['data_type'] = Validator::ERR_MSG_EMPTY;
            return FALSE;
        }
        if (!Factory::isPropertyDataTypeExists($data['data_type'], $class_name)){
            $errors['data_type'] = Validator::ERR_MSG_INCORRECT;
            return FALSE;
        }
        if (empty($data['title'])){
            $errors['title'] = Validator::ERR_MSG_EMPTY;
            return FALSE;
        }
        if (empty($data['type_id'])){
            $errors['type_id'] = Validator::ERR_MSG_EMPTY;
            return FALSE;
        }
        $type = Type::getById($data['type_id'], $segment_id);
        if (empty($type)) {
            $errors['type_id'] = Validator::ERR_MSG_EMPTY;
            return FALSE;
        }
        $db = \App\Builder::getInstance()->getDB();
        if (is_array($data['title'])) {
            $title = reset($data['title']);
        }else{
            $title = $data['title'];
        }
        if (empty($data['key'])){
            $key_base = \LPS\Components\Translit::Supertag($title);
            $key_base = str_replace('-', '_', $key_base);
            $key = $key_base;
            $i = 0;
            while (Factory::isPropertyKeyExist($key, $data['type_id'], NULL, $segment_id, $type['allow_children'] && !$dont_check_default_key) || ItemEntity::checkIsKeyReserved($key) || VariantEntity::checkIsKeyReserved($key)) {
                $i++;
                $key = $key_base . '_' . $i;
            }
            $data['key'] = $key;
        }
        $updateKey = md5(json_encode($data) . $data['type_id'] . $segment_id . time());
        $update_data = $class_name::prepareUpdateParams($data, $errors, $data['data_type']);
        foreach (static::$dataProviders as $p) {
            $p->preCreate($update_data, $errors, $updateKey);
        }
        if (!Property::checkParams(NULL, Type::getById($data['type_id']), $update_data, array(), $errors, $additionalData, $dont_check_default_key)){
            return FALSE;
        }
        $position = $db->query('SELECT MAX(`position`) FROM `' . Factory::TABLE . '` WHERE 1')->getCell();
        $id = $db->query('INSERT INTO `' . Factory::TABLE . '`
            SET `title`=?s, `type_id`=?d, `mask`="{!}", position = ?d, `key` = ?, `segment` = ?d, `data_type` = ?s', $title, $data['type_id'], $position + 1, $update_data['key'], !empty($update_data['segment']) ? 1 : NULL, $update_data['data_type']);
        foreach (static::$dataProviders as $p) {
            $p->onCreate($id, $updateKey);
        }
        $class_name::onCreate($id);
        return $id;
    }
    /**
     * Дополнительные операции, необходимые при создании свойства, индивидуальные для требуемого типа данных
     * @param int $id
     */
    protected static function onCreate($id) {

    }
    /**
     * Распаковка и явное приведение типов
     * @param string $value
     * @return mixed
     */
    public function valueTypeCast($value) {
        if (!is_array($value)) {
            if ($this['set'] == 1) {
                $value = array_filter(explode(Factory::VALUE_SEPARATOR, $value), function($v) {return !empty($v);}, ARRAY_FILTER_USE_KEY);//@TODO проверить, в каких случаях это возможно, может уже неактуально
            } else {
                $value = array($value);
            }
        }
        $return = array();
        foreach ($value as $val_id => $v) {
            $return[$val_id] = $this->explicitType($v);
        }
        return $return;
    }
    /**
     * явное приведение типов
     * @param type $v
     * @return type
     */
    public function explicitType($v) {
        return (string) $v;
    }
    /**
     * Возвращает объект категории айтема\варианта
     * @return Type
     */
    public final function getType(){
        return Type::getById($this->data['type_id'], $this->segment_id);
    }
    /**
     * Возвращает название таблицы, в которой хранятся значения данного проперти
     * @return string
     */
    public final function getTable(){
        return static::getValuesTable($this['data_type'], $this['multiple']);
    }
    /**
     * хоть и паблик, но вызывается только из Factory::get()
     * @param array $data
     * @see Factory::get()
     */
    public final function __construct($data, $segment_id) {
        $this->id = $data['id'];
        $this->data = $this->unpack($data);
        if (is_null($segment_id)) {
            $segment = \App\Segment::getInstance()->getDefault();
            $segment_id = $segment['id'];
        }
        $this->segment_id = $segment_id;
        if (!empty($data['group_id'])) {
            Group::prepare($data['type_id']); //подготовим данные заранее
        }
        $data_types = CatalogConfig::getPropertyFieldsData('data_type');
        if (!empty($data_types)) {
            foreach ($data_types as $data_type => $rus_title) {
                if (!Factory::isPropertyDataTypeExists($data_type, $property_class)) {
                    throw new \LogicException('В конфиге указан неверный тип данных свойства: ' . $data_type);
                }
                $this->onLoad();
            }
        }
        foreach (static::$dataProviders as $p) {
            $p->onLoad($this, $this->data);
        }
    }
    /**
     * При создании объекта в некоторых типах данных надо подготавливать другие объекты к загрузке
     * @return null
     */
    protected function onLoad() {
        return NULL;
    }
    /**
     * @TODO разобраться с json_encode (сделать везде нормальные массивы)
     * Запаковывает значение свойства для добавления в БД
     * @param array $data
     * @return array
     */
    protected function pack($data) {
        if (array_key_exists('table', $data)) {
            unset($data['table']); //сюда может попасть лишнее поле
        }
        foreach (array('filter_visible', 'visible') as $field) {
            if (empty($data[$field])) {
                $data[$field] = NULL;
            } else {
                //сюда может попасть уже сумма
                $data[$field] = is_array($data[$field]) ? array_sum($data[$field]) : $data[$field];
            }
        }
        if (static::VALUES_TYPE_ARRAY && array_key_exists('values', $data) && is_array($data['values'])){
            $data['values'] = !empty($data['values']) ? json_encode($data['values'], JSON_UNESCAPED_UNICODE) : NULL;
        }
        return $data;
    }
    /**
     * @TODO разобраться с json_encode (сделать везде нормальные массивы)
     * распаковывает данные полученные из БД
     * @param array $data
     * @return array
     */
    protected function unpack($data) {
        //чтобы не писать одно и то же для нескольких параметров, используем переменную переменной
        foreach (array('filter_visible', 'visible') as $field) {
            $$field = array();
            foreach (CatalogConfig::getPropertyFieldsData($field) as $b_n => $rus) {
                if ($b_n & $data[$field]) {
                    ${$field}[$b_n] = $b_n;
                }
            }
            $data[$field] = $$field;
        }
        $data['table'] = static::getValuesTable($data['data_type'], $data['multiple']);
        if (static::VALUES_TYPE_ARRAY && !is_array($data['values'])){
            $data['values'] = json_decode($data['values'], TRUE);
        }
        return $data;
    }

    /**
     * Преобразование значения из БД в финальный вид
     * @param mixed $value
     * @param bool $useMask
     * @param bool $singleSet для одного из значений set разделение на массив не требуется
     * @param int|null $segment_id
     * @return string
     */
    public function formatValue($value, $useMask = TRUE, $singleSet = FALSE, $segment_id = NULL) {
        if ($this['set'] == 1 && !is_array($value) && !$singleSet) {
            $value = explode(Factory::VALUE_SEPARATOR, $value);
        }
        if (!is_array($value)) {
            $value = array($value);
        }
        $returnedValue = $value;
        foreach ($value as $k => $v) {
            $returnedValue[$k] = $this->getFinalValue($v, $segment_id);
            if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE) {
                if ($returnedValue[$k] != '' && $useMask && !empty($this['segment_data']['mask'][$segment_id]) !== false && static::ALLOW_MASK) {
                    $returnedValue[$k] = $this->markerReplace($returnedValue[$k], $this['segment_data']['mask'][$segment_id]);
                }
            } else {
                if ($returnedValue[$k] != '' && $useMask && !empty($this['mask']) !== false && static::ALLOW_MASK) {
                    $returnedValue[$k] = $this->markerReplace($returnedValue[$k], $this['mask']);
                }
            }
        }
        return $this['set'] == 1 && !$singleSet ? $returnedValue : reset($returnedValue);
    }

    public function onPropertyLoad(\Models\CatalogManagement\CatalogPosition $catalogPosition, &$propertiesBySegments) {

    }
    /**
     * Преобразует одно значение в финальный вид без маски
     * @param mixed $v
     * @return mixed
     */
    public function getFinalValue($v, $segment_id = NULL) {
        return $v;
    }
	/**
	 * Возвращает конечный вид свойства только для отображения. не для записи в базу
	 * @param type $formated_value
     * @param int $segment_id
	 * @return mixed
	 */
	public function getCompleteValue($formated_value, $segment_id = NULL){
		return $formated_value['complete_value'];
	}
    /**
     * Заменяет маркер в строке на конкретное значение, возможны последующие усложнения разметки строки
     * @param string $value
     * @param string $pattern
     * @return string
     */
    public function markerReplace($value, $pattern) {
        if (strpos($pattern, static::MARKER) === FALSE){
            return $value;
        }
        if ($this instanceof Int && preg_match('~\\' . str_replace('?', '\?', static::MARKER_WORD_FORM) . '([^\\'.static::MARKER_WORD_FORM_END.']*)\\' . static::MARKER_WORD_FORM_END . '~', $pattern, $out) && !empty($out[1])){
            $word_forms = explode(static::MARKER_WORD_FORM_DELIM, $out[1]);
            if (count($word_forms) == 3){
                $word_case = \LPS\Components\FormatString::pluralForm($value, $word_forms, FALSE);
                return str_replace(static::MARKER, $value, str_replace(static::MARKER_WORD_FORM . $out[1] . static::MARKER_WORD_FORM_END, $word_case, $pattern));
            }
        }
        return str_replace(static::MARKER, $value, $pattern);
    }
    /**
     * Подготавливает значение свойства для записи в БД
     * не final, т.к. некоторым вообще ничего подготавливать не надо
     * @param type $value
     */
    public function prepareValueToSave($value){
		$value['value'] = $this->getValueToSave($value['value']);
        return $value;
    }
    /**
     * Чистит значение свойства, чтобы записать в базу
     * @param mixed $val
     * @return mixed
     */
    protected function getValueToSave($val){
        return $val == '' ? NULL : $val;
    }
    /**
     * Проверка значения на соответствие типу данных
     * @param $val
     * @return boolean
     */
    public function isValueFormatCorrect($val){
        if (!is_scalar($val)){
            return FALSE;
        }
        return TRUE;
    }
    /**
     * Возвращает список уникальных значений по части строки
     * @param string $q часть строки поиска
     * @return array
     * @todo обновить для работы с enum
     */
    public function getDistinctValues($q = '') {
        $db = \App\Builder::getInstance()->getDB();
        $values = $db->query('
            SELECT `value` FROM ?#
            WHERE `property_id` = ?d
                {AND `value` LIKE ?s }
            GROUP BY `value`', $this['table'], $this['id'], !empty($q) ? '%' . $q . '%' : $db->skipIt()
                )->getCol('value', 'value');
        return $values;
    }
    /**
     * Внутреннее сохранение данных в базу
     * @param array $data
     * @return type
     * @throws \LogicException
     */
    protected function save(array $data) {
        if (!empty($data['data_type']) && !empty($this['data_type']) && $this['data_type'] != $data['data_type']) {
            throw new \LogicException('При изменении типа данных надо пользоваться методом update');
        }
        $data = Catalog::checkAllowed($data, static::$allowParams);
        $db = \App\Builder::getInstance()->getDB();
        $result = $db->query('UPDATE `' . Factory::TABLE . '` SET ?a WHERE `id` = ?d', $data, $this->id);
        $this->data = $this->unpack($data + $this->data); //новые значения перезапишутся, старые останутся
        return $result;
    }

    /**
     * приведение поля values к соответвующему данной проперте виду (вызывается только внутри модели)
     * @param array $data
     * @param array $errors
     * @return array
     * @throws \Exception
     */
    protected static function prepareValues($data, &$errors){
        if (!array_key_exists('values', $data) || empty($data['values'])){
            $data['values'] = static::VALUES_TYPE_ARRAY ? array() : '';
        }
        if (static::VALUES_TYPE_ARRAY xor is_array($data['values'])){
            throw new \Exception('Field "values" must be ' . (static::VALUES_TYPE_ARRAY ? 'array' : 'not array'));
        } elseif (is_array($data['values']) && !empty(static::$values_fields)) {
            foreach($data['values'] as $key => $value){
                if (!in_array($key, static::$values_fields)){
                    unset($data['values'][$key]);
                }
            }
        }
        return $data;
    }

    protected static function checkUnacceptableParams($data){
        if (!static::ALLOW_FILTER && !empty($data['search_type']) && $data['search_type'] != 'none'){
            throw new \Exception('В типе #' . static::TYPE_NAME . ' фильтрация запрещена');
        }
        if (!static::ALLOW_MAJOR && isset($data['major'])){ // major равен NULL, если подбор похожих запрещен, поэтому проверяем с помощью isset
            throw new \Exception('В типе #' . static::TYPE_NAME . ' подбор похожих запрещен');
        }
        if (!static::ALLOW_MULTIPLE && !empty($data['multiple'])){
            throw new \Exception('Тип #' . static::TYPE_NAME . ' может использоваться только в айтемах');
        }
        $unacceptableParams = static::getUnacceptableParams();
        foreach ($data as $k => $v){
            if (isset($unacceptableParams[$k]) && $unacceptableParams[$k] === FALSE){
                $data[$k] = in_array($k, self::$flag_properties) ? 0 : NULL;
            }
        }
        return $data;
    }
    /**
     * Отфильтровать данные перед сохранением
     * @param array $data
     * @param array $errors
     * @param string $data_type
     * @return array
     * @throws \Exception
     */
    private static function filterEditData($data, &$errors, $data_type = NULL){
        $allow_params = static::getEditableParams();
        foreach ($data as $key => &$value){
            if (array_search($key, $allow_params) === FALSE){
                unset($data[$key]);
            }
        }
        $data_type = isset($data['data_type']) ? $data['data_type'] : $data_type;
        if (empty($data_type) || !Factory::isPropertyDataTypeExists($data_type, $property_class)){
            throw new \Exception('Property type "' . $data_type . '" doesn\'t exists');
        }
        $data = $property_class::prepareValues($data, $errors);
        $data = $property_class::checkUnacceptableParams($data);
        return $data;
    }

    /**
     * Подготовка входных данных
     * @param array $data
     * @param type $errors
     * @param null|string $data_type ключ типа данных свойства, нужно при создании
     * @return array
     */
    private static function prepareUpdateParams(array $data, &$errors, $data_type = NULL){
        $data = self::filterEditData($data, $errors, $data_type);
        // тайтл первая буква в верхнем регистре
        if (isset($data['title'])){
            $property_title = $data['title'];
            if (is_array($data['title'])){
                foreach ($data['title'] as &$pt){
                    $pt = \LPS\Components\FormatString::ucfirstUtf8($pt);
                }
            } else {
                $data['title'] = \LPS\Components\FormatString::ucfirstUtf8($data['title']);
            }
        }
        if (isset($data['search_type']) && $data['search_type'] == Factory::SEARCH_NONE){
            $data['filter_visible'] = NULL;
        }
        foreach (self::$flag_properties as $pr){
            if (isset($data[$pr]) && empty($data[$pr])){
                $data[$pr] = NULL;
            }
        }
        if (empty($data['search_type'])){
            $data['search_type'] = Factory::SEARCH_NONE;
        }
        return $data;
    }
    /**
     * Обновить данные о свойстве
     * Возвращает обновленный объект. Сам объект может остаться прежним.
     * При смене типа свойства, отдается уже совсем другой объект
     *
     * @param array $data
     * список необходимых параметров и их типов:<ul>
     * <li>int <b>type_id</b>
     * <li>string <b>key</b> идентификатор на латинице !обязательное
     * <li>string <b>title</b> название !обязательное
     * <li>string <b>filter_title</b> название в фильтре
     * <li>string <b>data_type</b> тип данных Может принимать одно из значений: <ul>
     * <li>int
     * <li>float
     * <li>flag
     * <li>string
     * <li>enum
     * <li>view //конструктор</ul>
     * <li>string <b>search_type</b> определяет как должен происходить поиск.<br>Может принимать одно из значений: <ul>
     * <li>between выбор диапазона
     * <li>select выбор из списка доступных
     * <li>autocomplete автодополнение
     * <li>none если поиск по полю не производится </ul>
     * <li>string <b>visible</b> определяет будет ли свойство видно в кратком описании элемента, будет ли видно вообще. <br>Может принимать одно из значений: <ul>
     * <li>any
     * <li>list
     * <li>item
     * <li>none</ul>
     * <li>string <b>values</b> описывает ограничения на возможные значения, формат зависит от типа данных (<b>data_type</b>). <br>Примеры форматов:<ul>
     * <li>для <b>enum</b> отдельная таблица значений
     * <li>min:M,max:N,step:S для <b>int</b> или <b>float</b>
     * <li>{$prop_key1} {$prop_key2} и т.д. для <b>view</b> </ul>
     * <li>string <b>mask</b> шаблон отображения, может содержать маркер {@see static::MARKER}
     * <li>bool <b>major</b> Мажорное свойство -  NULL - свойство не используется для подбора похожих, NOT NULL - разброс значений мажорного параметра</ul>
     * <li>bool <b>necessary</b> флаг, определяющий необходимо ли заполнить данное свойство перед публикацией</ul>
     * <li>bool <b>unique</b> флаг, определяющий нужно ли проверять значение свойства на уникальность</ul>
     * <li>int <b>position</b> порядок внутри группы
     * <li>int <b>group_id</b> id группы для разбиения свойств типа
     * <li>bool <b>multiple</b> флаг о том, что этот параметр расщепляемый
     * @param array $e
     * @param bool $dont_check_default_key - нужен для автосоздания дефолтных свойств, используется ТОЛЬКО из моделей
     * @throws \Exception
     * @return mixed Property at success, else FALSE
     */
    public function update($data, &$e = array(), $dont_check_default_key = FALSE) {
        $default_on_mult_change = FALSE;//при изменении расщепляемости, можно передать параметр, который определит, переносить значения или удалить
        if (array_key_exists('default_mult_change', $data)){
            $default_on_mult_change = !empty($data['default_mult_change']);
            unset($data['default_mult_change']);
        }
        //подготовка данных
        $update_data = self::prepareUpdateParams($data, $e, $this['data_type']);
        //записываем старые значения
        $old_data = $this->data;
        foreach (static::$dataProviders as $p) {
            $p->preUpdate($this, $update_data, $e);
        }
//        var_dump($update_data);
        //проверка данных
        if (!static::checkParams($this->id, $this->getType(), $update_data, $old_data, $e, $additionalData, $dont_check_default_key)){
            return FALSE;
        }
        //все проверки на ошибки должны быть завершены до этой строки, иначе может тип поменяться, а из-за ошибки update не пройдет
        if (array_key_exists('data_type', $update_data) && $old_data['data_type'] != $update_data['data_type']) {//при смене типа данных, меняется класс свойства
            $this->preDataTypeChange($update_data['data_type']);
            $entity = static::changeDataType($this->id, $update_data['data_type']);
        } else {
            $entity = $this;
        }
        // preUpdate поле сменты типа данных, поскольку нам нужно выполнять preUpdate и onUpdate в контексте одного типа данных
        $entity->preUpdate($update_data, $e);
        //корректировка параметров для записи в бд
        $update_data = $entity->pack($update_data);
        //собственно само сохранение
        $entity->save($update_data); //сначала сохраняем основные праметры
        if (isset($update_data['group_id']) && $update_data['group_id'] != $old_data['group_id']) {
            $update_data['position'] = $this->onChangeGroup($old_data); //если поменялась группа, убираем пустую позицию из старой группы
        }
        $entity->saveAdditionalData($additionalData); //сохраняем дополнительные параметры, которые зависят от типа данных
        //при смене множественности надо что-то делать
        $entity->changeSet($old_data['set']);
        //если меняется тип данных или параметр становится multiple, надо перемещать значения свойств в другие таблицы
        $entity->changeValuesType($old_data['data_type'], $old_data['multiple'], $default_on_mult_change);
        //при смене ключа надо обновить все значения свойств типа view
        //Отметить надо те товары, которые лежат в типе данного свойства, т.е. и в дочерних типах
        $type_ids = $this->getTypeIds($entity['type_id']);
        // обновляем задетые view и range свойства
        $clearCatalogCache = $entity->recreateView($old_data, $type_ids);//если меняются view, то у айтемов\вариантов надо чистить кэш
        $entity->recreateRange($old_data, $type_ids);
        // для вновь созданного свойства по умолчанию создаем его копии в конечных типах
        $entity->recreateDefaultProps($old_data);
        $entity->changeSegment($old_data, $type_ids);
        //в кеш кидается информация о значениях свойств, так что кеш надо пересчитывать
        foreach (static::$fieldsClearCache as $cf){
            if (array_key_exists($cf, $update_data) && $update_data[$cf] != $old_data[$cf]){
                $clearCatalogCache = TRUE;
                break; 
            }
        }
        if ($clearCatalogCache){
            $item_ids = ItemEntity::clearCache(NULL, $type_ids);
            VariantEntity::clearCache(NULL, $item_ids);
        }
        foreach (static::$dataProviders as $p) {
            $p->onUpdate($entity);
        }
        $entity->onUpdate();
        return $entity; //возвращаем уже объект с нужным классом
    }
    /**
     * проверка, можно ли сменить тип
     */
    protected static function checkDataTypeChange(Property $property, $new_data_type, &$e){
        
    }
    /**
     * действия в старом проперти перед сменой типа данных (на ошибки уже должно быть проверено в static::checkDataTypeChange)
     */
    protected function preDataTypeChange($new_data_type){

    }

    protected function preUpdate($params, &$errors){

    }

    protected function onUpdate() {

    }

    /**
     * Проверяем данные от пользователя на корректность. 
     * сначала проверяются общие данные для всех типов данных, 
     * потом, если сменился тип данных, проверяется в предыдущем классе. 
     * после идет проверка в новом (или текущем) классе.
     * @param int|null $id
     * @param \Models\CatalogManagement\Type $type
     * @param array $update_data
     * @param array $old_data
     * @param array $e
     * @param array $additionalData
     * @param bool $dont_check_default_key
     * @throws \Exception
     * @return array|boolean
     */
    final public static function checkParams($id, $type, &$update_data, $old_data, &$e, &$additionalData = NULL, $dont_check_default_key = FALSE){
        $update_data = Catalog::checkAllowed($update_data, static::$allowParams);
        if (!empty($id)){
            $property = Factory::getById($id);
            if (empty($property)){
                throw new \Exception("Property #${id} doesn't exists");
            }
        }
        if (array_key_exists('key', $update_data)){
            self::checkKey($type, !empty($property) ? $property : NULL, $update_data, $old_data, $e, $dont_check_default_key);
            if (!empty($e)){
                return FALSE;
            }
        }
        if (!empty($update_data['default_prop'])){
            if (!$type['allow_children']){
//                $e = 'В конечном типе невозможно создавать свойства по-умолчанию';
                $e['default_prop'] = self::ERR_MSG_DISALLOW;
                return FALSE;
            }
            // свойство по умолчанию должно быть невидимым
            $update_data['visible'] = array();
            // и необязательным
            $update_data['necessary'] = NULL;
        }
        if (!empty($update_data['segment'])) {
            $catalog = $type->getCatalog();
            if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_NONE) {
                throw new \Exception("Невозможно создать сегментированное свойство, сегменты отключены");
            } elseif (!$catalog['allow_segment_properties']) {
                throw new \Exception("Невозможно создать сегментированное свойство, в каталоге «${catalog['title']}» сегменты отключены");
            }
        }
        if (array_key_exists('title', $update_data) && !array_key_exists('title', static::$dataProvidersByFields) && empty($update_data['title'])) {
//            $e = 'Поле "Название" должно быть заполнено';
            $e['title'] = self::ERR_MSG_EMPTY;
            return FALSE;
        }
        $property_class = NULL;
        if (!array_key_exists('data_type', $update_data)){
            //если не передан тип, значит это редактирование, следовательно property существует
            $data_type = $property['data_type'];
        }else{
            $data_type = $update_data['data_type'];
        }
        if (!Factory::isPropertyDataTypeExists($data_type, $property_class)) {
//            $e = 'Тип данных "' . $update_data['data_type'] . '" не существует';
            $e['data_type'] = self::ERR_MSG_INCORRECT_FORMAT;
            return FALSE;
        }
        $allowSet = $property_class::ALLOW_SET;
        if (array_key_exists('set', $update_data) && !empty($update_data['set']) && $allowSet !== TRUE) {
            $e['set'] = self::ERR_MSG_DISALLOW;
//            $e = 'Мульти значение не может быть у типа данных "' . CatalogConfig::$properties_rus['types'][$update_data['data_type']] . '"';
            return FALSE;
        }
        //корректировка маски
        if (array_key_exists('mask', $update_data)){
            if (empty($update_data['mask'])){
                $update_data['mask'] = static::MARKER;
            }elseif (strpos($update_data['mask'], static::MARKER) === false){
                $update_data['mask'] = static::MARKER . ' ' . $update_data['mask'];
            }
        }
        //проверка и корректировка диапазона мажорного параметра
        if (array_key_exists('major', $update_data)){
            if (array_key_exists('major', $update_data)) {
                if (!is_null($update_data['major']) && preg_match('~^(\+|\-)?[0-9]*\%?(\+?[0-9]*\%?)?$~', $update_data['major'])) {
                    $update_data['major'] = preg_replace('~[^\+\-0-9\%]~', '', $update_data['major']); //нам нужны только цифры с процентом и +\-
                } elseif (empty($update_data['major'])){
                    $update_data['major'] = is_null($update_data['major']) ? NULL : 0;
                } else {
    //                $e = 'Неверно задан разброс значений';
                    $e['major'] = self::ERR_MSG_INCORRECT_FORMAT;
                    return FALSE;
                }
            } else {
                $update_data['major'] = NULL;
            }
        }
        //если поменялась группа, надо позицию свойства поменять
        if (isset($update_data['group_id']) && (empty($old_data) || $update_data['group_id'] != $old_data['group_id'])) {
            $update_data['position'] = self::getNextPositionInGroup($type['id'], $update_data['group_id']);
        }
        //если сменился тип данных, возможно что-то надо сделать в старом классе свойства (почистить за собой хвосты)
        if (!empty($id) && array_key_exists('data_type', $update_data) && $old_data['data_type'] != $update_data['data_type']){
            static::checkDataTypeChange($property, $update_data['data_type'], $e);
            if (!empty($e)) {
                return FALSE;
            }
        }
        //проверка и корректировка данных для разных типов
        $additionalData = $property_class::checkData($id, $update_data, $e); //проверяем!!! и отделяем данные, которые нужны только определенным типам данных
        if (!empty($e)) {
            return FALSE;
        }
        return $update_data;
    }
    private static function checkKey($type, $property, &$update_data, $old_data, &$e, $dont_check_default_key){
        if (empty($update_data['key'])) {
            $e['key'] = self::ERR_MSG_EMPTY;
//            $e = 'Поле "Ключ" должно быть заполнено.';
            return FALSE;
        }
        // Проверка ключа на уникальность
        $key = substr($update_data['key'], -strlen(self::DEFAULT_PROP_SUFFIX)) == self::DEFAULT_PROP_SUFFIX ? substr($update_data['key'], 0, -strlen(self::DEFAULT_PROP_SUFFIX)) : $update_data['key'];
        // фикс для правильной валидации ключа при изменении свойства с св-ва по умолчанию в обычное и наоборот
        if (isset($update_data['default_prop']) && (empty($property) || $update_data['default_prop'] != $property['default_prop'])){
            $update_data['key'] = $key . ($update_data['default_prop'] ? self::DEFAULT_PROP_SUFFIX : '');
        }
        // Проверяем ключ на уникальность, в конечных типах и в случае $dont_check_default_key == TRUE проверяется только ключ без суффикса св-ва по умолчанию
        // в остальных случаях проверяются оба ключа
        if (Factory::isPropertyKeyExist($key, $type['id'], !empty($property) ? $property['id'] : NULL, NULL, $type['allow_children'] && !$dont_check_default_key)
            && (empty($old_data)
                || array_key_exists('default_prop', $update_data) && $update_data['default_prop'] != $old_data['default_prop']
                || $old_data['key'] != $update_data['key'])) {
//            $e = '"Ключ" должнен быть уникальным в рамках данного типа, родителей и наследников.';
            $e['key'] = self::ERR_MSG_EXISTS;
            return FALSE;
        }
        if (preg_match('~[^_a-zA-Z0-9\-]~', $update_data['key'])) {
//            $e = 'Неверный формат ключа. Можно использовать только латинские буквы, цифры, тире и нижнее подчеркивание';
            $e['key'] = self::ERR_MSG_INCORRECT_FORMAT;
            return FALSE;
        }
        if (ItemEntity::checkIsKeyReserved($key) || VariantEntity::checkIsKeyReserved($key)){
//            $e = 'Ключ "' . $key . '" зарезервирован, используйте другой ключ';
            $e['key'] = self::ERR_MSG_RESERVED;
            return FALSE;
        }
        if (!empty($update_data['default_prop'])){
            // Добавляем ключу суффикс
            $key .= self::DEFAULT_PROP_SUFFIX;
        }
        $update_data['key'] = $key;
        return TRUE;
    }
    /**
     * Получить список айдишников типов, в которых присутствует данное свойство
     * @param int $type_id
     * @return array
     */
    private function getTypeIds($type_id = NULL){
        $type_id = !empty($type_id) ? $type_id : $this['type_id'];
        $type = Type::getById($type_id);
        $type_ids = array($type_id);
        if ($type['allow_children']){
            $children = $type->getAllChildren();
            if (!empty($children)) {
                foreach ($children as $parent_id => $t) {
                    if (!empty($t)) {
                        $type_ids = array_merge($type_ids, array_keys($t));
                    }
                }
            }
        }
        return $type_ids;
    }
	/**
	 * Проверяет принадлежность свойства определенному типу данных или группе типа данных
	 * @param type $data_type
	 * @return type
	 */
	public function instanceofDataType($data_type){
		$class = Factory::getDataTypeClass($data_type);
		return $this instanceof $class;
	}
    /**
     * Проверить\откорректировать специфичные данные для определенного типа данных
     * @param int $id пустой, если проверка перед созданием
     * @param array $data
     * @param array $e
     */
    protected static function checkData($id, &$data, &$e) {
        return array();
    }
    /**
     * 
     * @param array $data дополнительные данные. ДОЛЖНЫ БЫТЬ УЖЕ ПРОВЕРЕНЫ
     * @return boolean
     */
    protected function saveAdditionalData($data) {
        return TRUE;
    }
    /**
     * Проверяет, изменились ли значения свойства
     * @param array $old_values старые значения свойства
     * @return boolean
     */
    protected function isValuesChanged($old_values, &$changes = NULL) {
        if ($this['data_type'] == Enum::TYPE_NAME) {//с enum всё сложнее 
			//вычисляем те enum_id, которые есть в старых значениях, но нет в новых (т.е. удалили)
            if (empty($old_values)){
                $old_values = array();
            }
            //проверим на удаление
            foreach ($old_values as $en_id => $val){
                if (!isset($this['values'][$en_id])){
                    $changes[$en_id] = $en_id;
                }
            }
            $diff = FALSE;
            //проверим на изменения
            foreach ($this['values'] as $enum_id => $enum) {
				//при добавлении нового перечисления, можно считать что значение не поменялось
//                if (!isset($old_values[$enum_id])) {
//                    $diff = TRUE;
//                    break;
//                }
                if (isset($old_values[$enum_id]) && $enum['value'] != $old_values[$enum_id]['value']) {
                    $changes[$enum_id] = $enum_id;
                    $diff = TRUE;
                }
            }
            return $diff;
        } elseif ($this['data_type'] != Int::TYPE_NAME && $this['data_type'] != Float::TYPE_NAME) {//с остальными всё проще
            return $this['values'] != $old_values;
        }
    }

    /**
     * Обрабатывет зависимости со свойствами типа View
     * Обновляет ключи связанных свойств при их изменении, удаляет связи с несовместимыми свойствами (если изменился тип)
     * При необходимости устанавливает у айтемов, содержащих данное свойство recreate_view => 1
     * При смене ключа надо поменять значения у свойств типа view, которые включают в себя ключ данного свойства
     * Это могут быть свойства того же типа или дочерние!!!
     * @param array $old_data property->asArray() до апдейта
     * @param array $type_ids массив айдишников типов в которых используется свойство
     * @return boolean
     */
    protected final function recreateView($old_data, $type_ids) {
        $changed = FALSE;
        $prop_used_in_view = FALSE;
        //проверяем, при каких изменениях view, в который входит данное свойство может испортится, и вырезаем его, если так
        $views_properties = Factory::search($this['type_id'], Factory::P_VIEW, 'id', 'position', 'children');
        if (!empty($views_properties)) {
            foreach ($views_properties as $view_property) {
                $prop_values = $view_property['values'];
                if (strpos($prop_values, '{' . $old_data['key'] . '}') !== false) {//находим ключ в строке
                    $prop_used_in_view[$view_property['id']] = $view_property['multiple'];
                    $is_prop_acceptable =    //Свойство может использоваться во View если
                        !($view_property['multiple'] != 1 && $this['multiple'] == 1) //расщепляемое свойство не используется в нерасщепляемом View
                        && !$this instanceof Entity; //свойство не хранит объект (Item, Variant, File etc.)
                    $prop_values = str_replace('{' . $old_data['key'] . '}', ($is_prop_acceptable ? '{' . $this['key'] . '}' : ''), $prop_values); //меняем
                    $view_property->save(array('values' => $prop_values));
                    // если изменилась множественность или свойство стало неприменимым view нужно пересчитывать
                    $changed = $changed || $old_data['set'] != $this['set'] || !$is_prop_acceptable;
                }
            }
        }
        $changed = $changed
            || ($this instanceof View && (
                    $this['data_type'] != $old_data['data_type']// Новое составное свойство
                    || $this->isValuesChanged($old_data['values']) //или поменялся состав свойства
                )
            )
            || (//если свойство участвует в составных
                !empty($prop_used_in_view) && (
                    (
                        $old_data['data_type'] != $this['data_type']
                        && (
                               !($old_data['data_type'] == Flag::TYPE_NAME && $this['data_type'] == IntNumber::TYPE_NAME)//поменялся тип не с флага на инт
                            || !($old_data['data_type'] == IntNumber::TYPE_NAME && $this['data_type'] == FloatNumber::TYPE_NAME)//поменялся тип не с инта на флоат
                        )
                    )
					|| $old_data['set'] != $this['set'] //если изменилась множественность
                    || $this->isValuesChanged($old_data['values'], $changes) //поменялись возможные значения
                    || (!$old_data['multiple'] && $this['multiple'])
                )
            );
        if (empty($changed)){
            return FALSE;
        }
        $db = \App\Builder::getInstance()->getDB();
        //пересчитывать view будем только у тех, которые реально поменялись
        if ($this['data_type'] == Enum::TYPE_NAME && !empty($changes)){
            if ($this['multiple']){
                $changed_variants = $db->query('SELECT `variant_id` FROM `'.$this['table'].'` WHERE `value` IN (?l)', $changes)->getCol('variant_id', 'variant_id');
            }else{
                $changed_items = $db->query('SELECT `item_id` FROM `'.$this['table'].'` WHERE `value` IN (?l)', $changes)->getCol('item_id', 'item_id');
            }
        }
        if ($this['multiple'] != 1 || !empty($changed_items)){
            $db->query('UPDATE `' . ItemEntity::TABLE_ITEMS . '` SET '
                . '`recreate_view` = 1 WHERE 1'
                    . '{ AND `type_id` IN (?i)}'
                . '{ AND `id` IN (?i)}', 
                    !empty($type_ids) ? $type_ids : $db->skipIt(),
                    !empty($changed_items) ? $changed_items : $db->skipIt()
                );
        }
        if ($this['multiple'] == 1 || !empty($changed_variants) || (!empty($prop_used_in_view) && array_sum($prop_used_in_view)) > 0/*значения - это параметр multiple у свойства, т.е. определим, используется ли свойство у вариантов */){
            $db->query('UPDATE `' . VariantEntity::TABLE .'` AS `v`'
                . 'INNER JOIN `'.ItemEntity::TABLE.'` AS `i` ON (`v`.`item_id` = `i`.`id`{ AND `i`.`type_id` IN (?i)}) SET '
                . '`v`.`recreate_view` = 1 '
                . 'WHERE 1{ AND `v`.`id` IN (?i)}',
                !empty($type_ids) ? $type_ids : $db->skipIt(),
                !empty($changed_variants) ? $changed_variants : $db->skipIt()
            );            
        }
        return $changed;
    }

    /**
     * Обрабатывет зависимости со свойствами типа Range
     * Обновляет ключи связанных свойств при их изменении, удаляет связи с несовместимыми свойствами (если изменился тип)
     * При необходимости устанавливает у айтемов, содержащих данное свойство recreate_range => 1
     * @param array $old_data property->asArray() до апдейта
     * @param array $type_ids массив айдишников типов в которых используется свойство
     * @return boolean
     */
    protected final function recreateRange($old_data, $type_ids) {
        //если меняется ключ
        $changed = FALSE;
        if ($old_data['key'] != $this['key'] || $old_data['set'] != $this['set']) {
            $views_properties = Factory::search($this['type_id'], Factory::P_RANGE, 'id', 'position', 'children');
            if (!empty($views_properties)) {
                foreach ($views_properties as $property) {
                    if ($property['values'] == $old_data['key']) {//находим ключ в строке
                        $is_prop_acceptable = in_array($this['data_type'], array(Int::TYPE_NAME, Float::TYPE_NAME));
                        $property->save(array('values' => ($is_prop_acceptable ? $this['key'] : '')));
                        //реально изменяются значения товаров
                        $changed = $changed || ($old_data['set'] != $this['set']) || !$is_prop_acceptable || ($this['data_type'] != $old_data['data_type'] && !$this['data_type'] == Float::TYPE_NAME);
                    }
                }
            }
        }
        $changed = $changed
            || (
                $this['data_type'] == Range::TYPE_NAME
                && (
                    $old_data['data_type'] != Range::TYPE_NAME
                    || $this['values'] != $old_data['values']
                )
            );
        if ($changed){
            $db = \App\Builder::getInstance()->getDB();
            $db->query('UPDATE `' . ItemEntity::TABLE_ITEMS . '` SET `recreate_range` = 1, `cache` = NULL WHERE 1{ AND `type_id` IN (?i)}', !empty($type_ids) ? $type_ids : DBSIMPLE_SKIP);
        }
        return $changed;
    }
    /**
     * Создает свойства по умолчанию у дочерних конечных типов при создании шаблона свойства по умолчанию
     * @param $old_data
     * @return bool
     */
    protected final function recreateDefaultProps($old_data){
        // Если свойство только стало шаблоном свойства по умолчанию
        if ($this['default_prop'] && !$old_data['default_prop']){
            $child_type_ids = Type::getIds(array('parents' => $this['type_id'], 'allow_children' => 0));
            $prop_data = Factory::getDefaultPropertyData($this);
            if (!empty($prop_data)){
                foreach($child_type_ids as $type_id){
                    $prop_data['type_id'] = $type_id;
                    $prop_id = Property::create($prop_data, $errors);//Factory::create($prop_data['title'], $type_id);
                    $prop = Factory::getById($prop_id);
                    $prop->update($prop_data);
                }
            }
            return true;
        }
        return false;
    }
    /**
     * При изменении сегментированности, надо очистить кэш, и почистить все значения
     * @TODO подумать о том, как не чистить, а копипастить 
     * (проблема копипаста с объектами, т.к. нет смысла копировать id объекта, 
     * и в один сегмент не положить, т.к. у множественных свойств все сегментные 
     * посты создаются одновременно, чтобы не терялась связь постов между сегментами)
     * @param array $old_data
     */
    protected final function changeSegment($old_data, $type_ids){
        if ($old_data['segment'] == $this['segment']){
            return;
        }
        $db = \App\Builder::getInstance()->getDB();
        $db->query('DELETE FROM `'.$this['table'].'` WHERE `property_id` = ?d', $this['id']);
        $item_ids = ItemEntity::clearCache(NULL, $type_ids);
        VariantEntity::clearCache(NULL, $item_ids);
    }
    /**
     * Забираем следующую позицию в группе
     * @param type $group_id
     * @return type
     */
    protected static final function getNextPositionInGroup($type_id, $group_id) {
        $db = \App\Builder::getInstance()->getDB();
//		$max_position = $db->query('SELECT MAX(`position`) FROM `' . Factory::TABLE . '` WHERE `type_id` = ?d AND `group_id`=?d', $type_id, $group_id)->getCell();
        $max_position = $db->query('SELECT MAX(`position`) FROM `' . Factory::TABLE . '` WHERE 1')->getCell();
        if (empty($max_position)) {
            $max_position = 0;
        }
        return $max_position + 1;
    }
    /**
     * При смене группы, надо поменять порядок
     * @param array $data
     * @return int
     */
    protected final function onChangeGroup($old_data) {
        $db = \App\Builder::getInstance()->getDB();
//        $db->query('UPDATE `' . Factory::TABLE . '` SET `position` = `position`-1 WHERE `type_id`=?d AND `group_id`=?d AND `position`>?d', $old_data['type_id'], $old_data['group_id'], $old_data['position']);
        $db->query('UPDATE `' . Factory::TABLE . '` SET `position` = `position`-1 WHERE `position`>?d', $old_data['position']);
    }
    /**
     * что делать при удалении значения
     * @param mixed $value
     * @return NULL
     */
    public function onValueDelete($value){
        return NULL;
    }

    /**
     * Копирует значения свойства Items при смене таблицы в которой должны храниться эти значения в случае если не менялся параметр multiple
     * @param string $old_type
     * @param bool $old_multiple
     * @TODO одновременно переносить и для разных типов и для разной расщепляемости
     * @TODO запись в логи???
     * @return void

     */
    protected final function changeValuesType($old_type, $old_multiple, $default_on_mult_change = FALSE) {
        if ($this['data_type'] == $old_type && $this['multiple'] == $old_multiple) {
            return FALSE;
        }
        $old_table = self::getValuesTable($old_type, $old_multiple);
        $new_table = $this->getTable();
        if (
                $old_table != $new_table ||
                $this['data_type'] == Enum::TYPE_NAME ||
                $old_type == Enum::TYPE_NAME
        ) {
            $obj_field = ($this['multiple'] == 1 ? VariantEntity::TABLE_PROP_OBJ_ID_FIELD : ItemEntity::TABLE_PROP_OBJ_ID_FIELD);
            $db = \App\Builder::getInstance()->getDB();
            //если свойство сохранило свой параметр расщепляемости, перемещаем таблицы. 
            //иначе удаляем все значения, т.к. не понятно какие должны быть.
            if ($this['multiple'] == $old_multiple) {
                if ($this['data_type'] != Enum::TYPE_NAME && $old_type != Enum::TYPE_NAME) { //Если старые и новые типы не ENUM
                    $db->query('
                        INSERT INTO ?# (`property_id`, `segment_id`, `value`, ?#)
                            SELECT `property_id`, `segment_id`, `value`, ?# FROM ?# WHERE `property_id` = ?d', $new_table, $obj_field, $obj_field, $old_table, $this->id);
                } elseif ($old_type == Enum::TYPE_NAME) {//если раньше был enum
                    //вытаскиваем настоящие enum значения
                    $values = $db->query('
                        SELECT ?#, `e`.`value`, `v`.`segment_id` FROM ?# AS `v`
                        INNER JOIN `' . Enum::TABLE_PROP_ENUM . '` AS `e` ON (`e`.`id` = `v`.`value`)
                        WHERE `v`.`property_id` = ?d', $obj_field, $old_table, $this->id)->select($obj_field);
                    $insert_values = '';
                    foreach ($values as $item_id => $val) {
                        // Собираем преобразованные значения...
                        $insert_values .= (!empty($insert_values) ? ', ' : '')
                            . '(' . $this->id . ', '
                            . $item_id . ', '
                            . ($this['data_type'] == Float::TYPE_NAME ? floatval(str_replace(',', '.', $val['value'])) : ($this['data_type'] == Int::TYPE_NAME ? intval($val['value']) : $db->escape_value($val['value']))) . ', '
                            . (empty($val['segment_id']) ? 'NULL' : $val['segment_id']) . ')';
                    }
                    // ...и вставляем их значения в новую таблицу
                    $db->query('INSERT INTO ?# (`property_id`, ?#, `value`, `segment_id`) VALUES ' . $insert_values, $new_table, $obj_field);
                    //удаляем все уже лишние значения enum
                    $db->query('DELETE FROM `' . Enum::TABLE_PROP_ENUM . '` WHERE `property_id` = ?d', $this->id);
                } elseif ($this['data_type'] == Enum::TYPE_NAME) {//если стал enum
                    //новые возможные значения enum выводятся в интерфейсе, поэтому тут их не ищем
                    $values = $db->query('SELECT `value`, ?#, `segment_id` FROM ?# WHERE `property_id` = ?d', $obj_field, $old_table, $this->id)->getCol(array('value', $obj_field), 'segment_id');
                    foreach ($values as $new_value => $data) {
                        $enum_id = NULL;
                        if (!empty($this['values'])) {
                            foreach ($this['values'] as $e_id => $v) {
                                if ($v['value'] == $new_value) {
                                    $enum_id = $e_id;
                                }
                            }
                        }
                        if (!empty($enum_id)) {
                            foreach ($data as $item_id => $segment_id) {
                                $db->query('INSERT INTO ?# SET `property_id` = ?d, ?# = ?d, `value` = ?, `segment_id` = ?d', $new_table, $this->id, $obj_field, $item_id, $enum_id, $segment_id);
                            }
                        }
                    }
                }
            }else{//мы хотим менять значения при смене расщепляемости параметра
            //сюда заходим только если параметр поменялся
                // Если свойство по-умолчанию - нужно переделать дочерние свойства
                // Поскольку свойство по-умолчанию просто шаблон, не обрабатываем значения свойства, их не должно быть
//                    throw new \Exception($this['id'].var_export($this['default_prop'], true));
                if ($this['default_prop']){
                    $search_key = substr($this['key'], 0, -strlen(self::DEFAULT_PROP_SUFFIX));
                    $props = Factory::search($this['type_id'], Factory::P_ALL, 'id', 'type_group', 'children', array('key' => $search_key));
                    if (!empty($props)){
                        foreach($props as $p){
                            $prop_params = $p->asArray();
                            $prop_params['multiple'] = $this['multiple'];
                            $prop_params['default_on_mult_change'] = $default_on_mult_change;
                            $p->update($prop_params);
                        }
                    }
                } elseif ($default_on_mult_change === TRUE){
                    //если стал расщепляемым
                    if (empty($old_multiple) && !empty($this['multiple'])){
                        //заменяем каждому варианту на бывшее значение его айтема
                        $variant_values = $db->query('SELECT '
                            . '`v`.`id`, '
                            . '`iv`.`value`, '
                            . '`iv`.`segment_id`, '
                            . 'CONCAT(`v`.`id`, "_", '
                            . ' IF (`iv`.`segment_id` IS NULL, 0, `iv`.`segment_id`)) '
                            . 'AS `key` '
                            . 'FROM `'.\Models\CatalogManagement\Variant::TABLE.'` AS `v`'
                            . 'INNER JOIN `'.$old_table.'` AS `iv` ON (`v`.`item_id` = `iv`.`item_id` AND `iv`.`property_id` = ?d)'
                            , $this['id'])->select('key');
                        $query_data = array();
                        foreach ($variant_values as $v_data){
                            $query_data[] = $v_data['id'] . ',' . $this['id'] . ',' . $db->escape_value($v_data['value']) .  ',' . (empty($v_data['segment_id']) ? 'NULL' : $v_data['segment_id']);
                        }
                        if (!empty($query_data)){
                            $db->nakedQuery('INSERT INTO `'.$new_table.'` (`variant_id`, `property_id`, `value`, `segment_id`) VALUES ('.implode('),(', $query_data).')');
                        }
                    }else{//если стал нерасщепляемым
                        //вот тут начинается опа 
                        //добавить каждому айтему значение (первое попавшееся) его вариантов
                        $item_values = $db->query('SELECT '
                            . '`v`.`item_id`, '
                            . '`vv`.`value`, '
                            . '`vv`.`segment_id`, '
                            . 'CONCAT(`v`.`item_id`, "_", '
                            . ' IF (`vv`.`segment_id` IS NULL, 0, `vv`.`segment_id`)) '
                            . 'AS `key` '
                            . 'FROM `'.\Models\CatalogManagement\Variant::TABLE.'` AS `v`'
                            . 'INNER JOIN `'.$old_table.'` AS `vv` '
                            . ' ON (`v`.`id` = `vv`.`variant_id` '
                            . '     AND `vv`.`property_id` = ?d)'
                            . 'GROUP BY `v`.`item_id`, `vv`.`segment_id`'
                            , $this['id'])->select('key');
                        $query_data = array();
                        foreach ($item_values as $i_data){
                            $query_data[] = $i_data['item_id'] . ',' . $this['id'] . ',' . $db->escape_value($i_data['value']) .  ',' . (empty($i_data['segment_id']) ? 'NULL' : $i_data['segment_id']);
                        }
                        if (!empty($query_data)){
                            $db->nakedQuery('INSERT INTO `'.$new_table.'` (`item_id`, `property_id`, `value`, `segment_id`) VALUES ('.implode('),(', $query_data).')');
                        }
                    }
                }
            }
            //хвосты удаляем в любом случае
            $db->query('DELETE FROM ?# WHERE `property_id` = ?d', $old_table, $this->id);
            return TRUE;
        }
        return FALSE;
    }
    protected function changeSet($old_set){
        if (empty($this['set']) && !empty($old_set)){
            //делаем изменения, только если было множественное, стало не множественное (надо удалить лишние значения)
            $db = \App\Builder::getInstance()->getDB();
            $db->query('DELETE
                FROM `'.$this['table'].'` 
                WHERE `property_id` = ?d AND `position` > 1', 
                $this['id']
            );
        }
    }
    /**
     * Меняет порядковый номер свойства в сортировке
     * @param int $position новый номер, на какой номер поменять позицию
     *
     */
    public final function move($position = 0) {
        $db = \App\Builder::getInstance()->getDB();
        if (!empty($position)) {
            if ($this['position'] > $position) {
//                $db->query('
//                    UPDATE `' . Factory::TABLE . '`
//                    SET `position` = `position`+1
//                    WHERE `type_id` = ?d AND `group_id`=?d AND `position` >= ?d AND `position` < ?d', $this['type_id'], $this['group_id'], $position, $this['position']
//                );
                $db->query('
                    UPDATE `' . Factory::TABLE . '`
                    SET `position` = `position`+1
                    WHERE `position` >= ?d AND `position` < ?d', $position, $this['position']
                );
            } else {
//                $db->query('
//                    UPDATE `' . Factory::TABLE . '`
//                    SET `position` = `position`-1
//                    WHERE `type_id` = ?d AND `group_id`=?d AND `position` <= ?d AND `position` > ?d', $this['type_id'], $this['group_id'], $position, $this['position']
//                );
                $db->query('
                    UPDATE `' . Factory::TABLE . '`
                    SET `position` = `position`-1
                    WHERE `position` <= ?d AND `position` > ?d', $position, $this['position']
                );
            }
            $this->save(array('position' => $position));
        }
        Factory::clearCache($this['id']);
    }
    /**
     * Сохранение ограничений на свойство
     * @param type $param
     * @return type
     */
    public final function updateFixed($param) {
        return $this->save(array('fixed' => $param ? $param : 0));
    }
    /**
     * Подгружаем картинку к свойству
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $FILE
     * @param type $error
     * @return \Models\CatalogManagement\Properties\Property
     */
    public final function uploadImage(\Symfony\Component\HttpFoundation\File\UploadedFile $FILE = NULL, &$error = NULL) {
        if ($FILE) {
            if (empty($this['image_id'])) {
                $collection = \Models\ImageManagement\Collection::getById(PropertyImageCollection::COLLECTION_ID);
                $image = $collection->addImage($FILE, '', $error);
                $this->save(array('image_id' => $image['id']));
            } else {
                $image = $this['image'];
                $error = $image->reload($FILE);
            }
            return $image;
        } else {
            if (!empty($this['image_id'])) {
                \Models\ImageManagement\Image::del($this['image_id']);
                $this->save(array('image_id' => NULL));
            }
            return NULL;
        }
    }
    /**
     * Удалить Property
     * @param int $id id свойства
     * @param string $error
     * @param bool $delete_default_props при удалении свойства по умолчанию TRUE - удалять копии свойства, FALSE - нет
     * @return bool
     */
    public static function delete($id, &$error = NULL, $delete_default_props = FALSE) {
        $property = Factory::getById($id);
        if (empty($property)){
            return FALSE;
        }
        foreach(static::$dataProviders as $p){
            $p->preDelete($property, $error);
            if (!empty($error)){
                return FALSE;
            }
        }
        $property->preDelete($error);
        if (!empty($error)){
            return FALSE;
        }
        if ($property['default_prop'] && $delete_default_props){
            $props = Factory::search($property['type_id'], Factory::P_ALL, 'id', 'type_group', 'children', array('key' => substr($property['key'], 0, -strlen(self::DEFAULT_PROP_SUFFIX))), NULL);
            foreach($props as $prop_id => $p){
                self::delete($prop_id);
            }
        }
        $data_type_class = Factory::getDataTypeClass($property['data_type']);
        $db = \App\Builder::getInstance()->getDB();
        $db->query('DELETE FROM ?# WHERE `property_id` = ?d', $property['table'], $property->id); //чистим итемсы
        if ($property['data_type'] == Enum::TYPE_NAME) {
            $db->query('DELETE FROM `' . Enum::TABLE_PROP_ENUM . '` WHERE `property_id` = ?d', $property->id);
        }
		$db->query('UPDATE `'.Factory::TABLE.'` SET `values` = REPLACE(`values`, "{'.$property['key'].'}", "") WHERE `type_id` = ?d', $property['type_id']);
        $db->query('UPDATE `'.Factory::TABLE.'` SET `values` = "" WHERE `data_type` = "range" AND `values` = ?s', $property['key']);
//        $db->query('UPDATE `' . Factory::TABLE . '` SET `position` = `position`-1 WHERE `position` > ?d AND `group_id`=?d AND `type_id` = ?d', $this['position'], $this['group_id'], $this['type_id']);
        $db->query('UPDATE `' . Factory::TABLE . '` SET `position` = `position`-1 WHERE `position` > ?d', $property['position']);
        $db->query('DELETE FROM `' . Factory::TABLE . '` WHERE `id`=?d', $property->id);
        Factory::clearCache();
        foreach(static::$dataProviders as $p){
            $p->onDelete($id);
        }
        $data_type_class::onDelete($id);
        return TRUE;
    }
    /**
     * Метод перед удалением свойства
     * @param \Models\CatalogManagement\Properties\Property $property
     * @param type $error
     */
    protected function preDelete(&$error){
        
    }
    /**
     * Метод, необходимый для удаления сущностей, созданных свойством
     * ошибки передавать уже бесполезно, если что-то случилось - то это косяк.
     */
    protected static function onDelete($id){

    }
    /**
     * Проверка свойства на необходимость заполнения
     * @param int $type_id
     * @return boolean
     */
    public function isRequired($type_id) {
        if ($this['necessary']) {//если свойство обязательное, но не используется в данном типе, то заполнять его не обязательно
            return Type::getById($type_id)->checkPropertyAccessibility($this);
        }
        return false;
    }
    /**
     * Получить объект в виде массива
     * @return Array
     */
    public final function asArray() {
        $data = $this->data;
        /** @TODO Разобраться, нужны ли нам здесь данные хелперов. Ломают апдейты. */
        foreach(self::$dataProviders as $p){
            $p->asArray($this, $data);
        }
        return $data;
    }
    /**
     * Список возможностей для текущего объекта
     * @return type
     */
    protected function getPropertyConstants(){
        return Factory::getPropertyAllows($this['data_type']);
    }

    /*     * ***************************** работа с iPropertyDataProvider **************************** */

    /**
     * @var iPropertyDataProvider[]
     */
    static $dataProviders = array();
    /**
     * @var iPropertyDataProvider[]
     */
    static $dataProvidersByFields = array();
    /**
     * @static
     * @param iPropertyDataProvider $provider
     */
    static final function addDataProvider(iPropertyDataProvider $provider) {
        static::$dataProviders[get_class($provider)] = $provider;
        foreach ($provider->fieldsList() as $field) {
            static::$dataProvidersByFields[$field] = $provider;
        }
    }
    /**
     * @static
     * @param iPropertyDataProvider $provider
     */
    static final function delDataProvider(iPropertyDataProvider $provider) {
        unset(static::$dataProviders[get_class($provider)]);
    }

    /*     * ***************************** ArrayAccess **************************** */

    public final function offsetExists($offset) {
        if ($offset == 'segment_id') {
            return isset($this->segment_id);
        } elseif ($offset == 'group') {
            return !empty($this['group_id']);
        }
        return isset($this->data[$offset]) or isset(static::$dataProvidersByFields[$offset]);
    }

    public final function offsetGet($offset) {
        if (isset(static::$dataProvidersByFields[$offset])) {
            return static::$dataProvidersByFields[$offset]->get($this, $offset);
        } elseif ($offset == 'group') {
            $group_id = $this['group_id'];
            return !empty($group_id) ? \Models\CatalogManagement\Group::getById($this['type_id'], $this['group_id'], $this->segment_id) : NULL; //данные уже подготовленны
        } elseif ($offset == 'segment_id') {
            return $this->segment_id;
        } elseif($offset == 'constants'){
            return $this->getPropertyConstants();
        } elseif (!array_key_exists($offset, $this->data)) {
            throw new \Exception('Undefined index "' . $offset . '" for prop #' . $this->id);
        } else {
            return $this->data[$offset];
        }
    }

    public final function offsetSet($offset, $value) {
        throw new \Exception('Property has only immutable Array Access');
    }

    public final function offsetUnset($offset) {
        throw new \Exception('Property has only immutable Array Access');
    }
}
