<?php
/**
 * Управление типами элементов
 *
 *
 * @author olga
 */
namespace Models\CatalogManagement;
use App\Configs\SeoConfig;
use App\Configs\CatalogConfig;
use Models\Seo\PageRedirect;
use Models\CatalogManagement\CatalogHelpers\Interfaces\iTypeDataProvider;
use Models\CatalogManagement\Properties\Enum;
use Models\CatalogManagement\Properties\Factory AS PropFactory;
use Models\CatalogManagement\Properties\Property;
use App\Configs\SharedMemoryConfig;
/**
 * Типы товаров
 */
class Type implements \ArrayAccess{
    /*
     * Id самой главной верхней категории
     */
    const DEFAULT_TYPE_ID = 1;
    /**таблица типов элементов*/
    const TABLE_ITEM_TYPES = 'item_types';
    const TABLE = self::TABLE_ITEM_TYPES;
    const TABLE_PROPERTIES_HIDDEN = 'properties_hidden';
    const TABLE_ENUM_VALUES_HIDDEN = 'enum_values_hidden';
    const TABLE_FIELDS = 'item_types_fields';
    /**группы свойств*/
    const TABLE_PROPERTIES_GROUPS = 'property_groups';
    const TABLE_GROUP_FIELDS = 'property_groups_titles';
    /** статусы */
    const STATUS_VISIBLE = 'visible';
    const STATUS_HIDDEN = 'hidden';
    const STATUS_DELETE = 'delete';
    /** тип прикрепляемого поста - описания */
    const POST_TYPE = 'types';
    /** статусы */
    private static $statuses = array(self::STATUS_VISIBLE, self::STATUS_HIDDEN);
    /** разделитель для строки счетчиков */
    const COUNTERS_DELIMETR = '|';
    /* названия счетчиков */
    const V_ITEMS = 'visible_items';
    const ALL_ITEMS = 'all_items';
    const V_TYPES = 'visible_types';
    const ALL_TYPES = 'all_types';
    /** порядок следования счетчиков */
    private static $counters_order = array(self::V_ITEMS, self::ALL_ITEMS, self::V_TYPES, self::ALL_TYPES);
    /** специальные динамические ключи */
    private static $specials_keys = array('counters', 'properties');
    /**
     * id коллекции, в которой содержаться обложки для типов
     */
    const COVERS_COLLECTION_ID = 2;
    /**
     * Id коллекции в которой содержатся картинки по дефолту (зависимые от типа)
     */
    const COVERS_ITEMS_DEFAULT_COLLECTION_ID = 3;
    /**
     * @var Type[]
     */
    private static $registry = array();
    /**
     * массив соответствия [key][parent_id] => id
     * @var array
     */
    private static $idByKeys = NULL;
    /**
     * массив соответствия урла типу
     * @var array
     */
    private static $idByUrls = array();
    /**
     *
     * @var array
     */
    private static $loadIds = array();
    /**
     * Сегментированные свойства типа
     * @var array
     */
    private static $segmentDataCache = array();
    /**
     * @var \MysqlSimple\Controller
    */
    private static $db = NULL;
    /**
     * Данные о типе
     * @var array
     */
    private $data = array();
    /**
     * id сегмента, установленного при создании объекта
     * @var int
     */
    private $segment_id = NULL;
    /**
     *
     * @var array()
     */
    private $children = array();
	private $availableProperties = array();
    /**
     * Разрешенные параметры для различных сущностей системы
     * @see Catalog::checkAllowed()
     */
    static protected $allowParams = array(
        'title',        // Наименование типа
        'key',          // Ключ
        'item_prefix',
        'url',
        'parent_id',    // Родительская рубрика
        'only_items',    // Если в товаре нет вариантов, то можно создать вариант автоматом
        'counters',     // Счетчики - высчитываются внутренними механизмами @see $counters_order
        'position',     // Сортировка
        'status',       // Статусы доступности @see self::STATUS_*
        'parents',      // Строка родителей
        'tags',         //теги
        'allow_children', // Можно ли создавать подтипы, у каталога также указывает на максимально допустимое количество вложенных типов
        'post_id',      //id поста
        'annotation',   //аннотация к типу
        'fixed',        //запрещать ли редактировать\добавлять типы и группы в данном типе
        'nested_in',//вложенность в смежный тип (айтемы текущего наследуются от айтемов типа в данном поле)
        'nested_in_final',//флаг, указывающий, у айтемов категории не может быть дочерних айтемов
        'last_update',
        'dynamic_for'
    );
    /**
     * редактируемые извне параметры
     * @see Catalog::checkAllowed()
     */
    static protected $editableParams = array(
        'title',
        'key',
        'item_prefix',
        'annotation',
        'only_items',
		'allow_children',
        'nested_in',
        'nested_in_final',
        'dynamic_for'
    );
    private static function init(){
        if (is_null(self::$db)){
            self::$db = \App\Builder::getInstance()->getDB();
        }
    }

    private function getDefaultProps(){
        if (empty($this->data['default_properties'])){
            $props = PropFactory::search($this->getData('id'), PropFactory::P_DEFAULT, 'id', 'group', 'parents', array(), $this->segment_id);
            $result = array();
            foreach ($props as $prop){
                if ($prop['default_prop']){
                    $result[] = PropFactory::getDefaultPropertyData($prop);
                }
            }
            $this->data['default_properties'] = $result;
        }
        return $this->data['default_properties'];
    }
    /**
     * 
     * @param int $segment_id
     * @return Property[]
     */
    public function getRangeProps($segment_id = 0){
        return PropFactory::search($this->getData('id'), PropFactory::P_ITEMS | PropFactory::P_RANGE, 'id', 'group', 'parents', array(), $segment_id);
    }
    /**
     * Подготовка ids для загрузки
     * @param array $ids
     */
    public static function prepare(array $ids){
        if (empty($ids)){
            return;
        }
        $ids = array_diff($ids, array_keys(self::$registry), self::$loadIds);
        if (!empty($ids)){
            self::$loadIds = array_merge($ids, self::$loadIds);
        }
    }
    /**
     * @param int[] $ids
     * @return Type[]
     */
    public static function factory(array $ids, $segment_id = NULL){
        if (is_null($segment_id)){
            $defaultSegment = \App\Segment::getInstance()->getDefault();
            if (!empty($defaultSegment['id'])){
                $segment_id = $defaultSegment['id'];
            }
        }
        if (empty($ids)){
            return array();
        }
		$load_ids = array_unique(array_merge($ids, self::$loadIds));
        if (!empty(self::$registry)){
            $load_ids = array_diff($load_ids, array_keys(self::$registry));
        }
        $types = array();
        if (!empty($load_ids)){
            $cached_data = self::cacheUpData($load_ids);
            $db_ids = array_diff($load_ids, array_keys($cached_data));
            if (!empty($db_ids)){
                self::init();
                $types = self::$db->query('
                    SELECT `id`, `'.implode('`, `', self::$allowParams) .'`  FROM `'. self::TABLE_ITEM_TYPES .'`
                    WHERE `id` IN (?i) AND `status` != "' . self::STATUS_DELETE . '"',
                    $load_ids
                )->select('id');
                self::setCacheData($types);
            }
            $types = $types + $cached_data;//нельзя терять id, а данные не пересекаются
            foreach ($types as $t_id => $t_data){
                self::$registry[$t_id] = new Type($t_data, $segment_id);
                //@TODO возможно это надо перенести в конструктор, т.к. это работа с каждым конкретным объектом
                self::$idByKeys[self::$registry[$t_id]['key']][self::$registry[$t_id]['parent_id']] = $t_id;//тут мы знаем, какие объекты зареганы по ключу и id родителя
                                                                                                            //(ключи уникальны только в пределах ветки и среди непосредственных потомков типа)
                if (!empty(self::$registry[$t_id]['url'])){
                    // сохраняем соответствие урлов айдишникам
                    self::$idByUrls[self::$registry[$t_id]['url']] = $t_id;
                }
            }
            self::$loadIds = array();//загрузили в реестр всё что смогли
		}
        $result = array();
        foreach($ids as $id){
            $result[$id] = !empty(self::$registry[$id]) ? self::$registry[$id] : NULL;
        }
        return $result;
        return array_intersect_key(self::$registry, array_flip($ids));
    }
    /**
     * вытаскиваем из кэша данные
     */
    private static function cacheUpData($ids){
        $memory_key = self::getMemoryKey();
        if (empty($memory_key)){//если в конфиге запрещено кэширование, значит возвращаем как есть
            return array();
        }
        $data = \App\Builder::getInstance()->getSharedMemory()->get($memory_key);

        if (empty($data)){
            return array();
        }
        return array_intersect_key($data, array_flip($ids));
    }
    /**
     * записываем в кэш данные
     * @param type $data
     */
    private static function setCacheData($data){
        $memory_key = self::getMemoryKey();
        if (empty($memory_key)){//если в конфиге запрещено кэширование, значит возвращаем как есть
            return;
        }
        foreach ($data as $id => $value){
            \App\Builder::getInstance()->getSharedMemory()->set($memory_key, $id, $value);
        }
    }
    /**
     * взять ключ для обращения к памяти
     */
    private static function getMemoryKey(){
        return SharedMemoryConfig::getEntityKey(SharedMemoryConfig::SHM_KEY_CATEGORY);
    }
    /**
     * Возвращает тип по его id
     * @param int $id
	 * @param int $segment_id
     * @return Type|null
     */
    public static function getById($id, $segment_id = NULL){
        if (empty($id)){
            return NULL;
        }
        if (!isset(self::$registry[$id])){
            self::factory(array($id), $segment_id);
        }
        return !empty(self::$registry[$id]) ? self::$registry[$id] : NULL;
    }

    /**
     * Ищем по ключу
     * @param string $key
     * @param int $parent_id
     * @param int|null $segment_id
     * @return Type|null
     */
    public static function getByKey($key, $parent_id = self::DEFAULT_TYPE_ID, $segment_id = NULL){
        if (!isset(self::$idByKeys[$key][$parent_id])){
            $types_search = self::search(array('parent_id' => $parent_id, 'key' => $key), $segment_id);
            $type = reset($types_search);
            if (!empty($type)){
                self::$idByKeys[$key][$parent_id] = $type['id'];
            }
        }
        return !empty(self::$idByKeys[$key][$parent_id]) ? self::getById(self::$idByKeys[$key][$parent_id], $segment_id) : NULL;
    }

    /**
     * Поиск типа по URL
     * @param string $url
     * @param int|null $segment_id
     * @return Type|null
     */
    public static function getByUrl($url, $segment_id = null){
        if (!isset(self::$idByUrls[$url]) || !isset(self::$registry[self::$idByUrls[$url]])){
            $types_search = self::search(array('url' => $url), $segment_id);
            $type = reset($types_search);
        }else{
            $type = self::$registry[self::$idByUrls[$url]];
        }
        return $type;
    }

    /**
     * Проверка существования типа
     * @param int $id идентификатор типа
     * @return bool
     */
    public static function isExist($id){
        self::init();
        return self::$db->query('SELECT 1 FROM `'.self::TABLE_ITEM_TYPES.'` WHERE `id`=?d', $id)->getCell();
    }

    /**
     * Создание типа элементов
     * @param $params
     *   string $params['title']
     *   int $params['parent_id'] идентификатор рубрики
     *   bool $params['only_items'] флаг на автоматически создаваемые варианты у создаваемых товарных позиций
     *   bool $params['allow_children'] 1 - можно создавать подтипы, 0 - можно только айтемы
     * @param array $props список свойств создаваемых сразу по умолчанию
     * @return Type
     */
    public static function create(array $params, array $props = array(), &$errors = NULL, $segment_id = NULL, array $groups = array()){
        $parent_type = Type::getById($params['parent_id']);
        if (empty($parent_type) || !$parent_type['allow_children']){
            throw new \LogicException('Невозможно создать дочерний тип в данном типе');
        }
        $type_key = array_key_exists('key', $params) ? $params['key'] : NULL;
        $type_key = self::checkKey($type_key, $params['parent_id'], TRUE, $errors);
		foreach (self::$dataProviders as $p){
            /* @var $p iTypeDataProvider */
            $p->preCreate($params, $errors);
        }
		if (!empty($errors)){
			return false;
		}
        $type_url = self::generateNewUrl($type_key, $params['parent_id']);
        $params['url'] = $type_url;
		if (!Catalog::checkAllowed($params, self::$allowParams, false))
            return false;
        self::init();
        $max_position = self::$db->query('
            SELECT MAX(`position`)
            FROM `'.self::TABLE_ITEM_TYPES.'`
            WHERE `parent_id`=?d',
                $params['parent_id'])
        ->getCell();
        $parents = self::$db->query('
            SELECT `parents`
            FROM `'.self::TABLE_ITEM_TYPES.'`
            WHERE `id`=?d',
                $params['parent_id'])
            ->getCell();
        $type_id = self::$db->query('
            INSERT INTO `'.self::TABLE_ITEM_TYPES.'`
            SET `title`=?s,
                `key` = ?s,
                `url` = ?s,
                `parent_id`=?d,
                `only_items`=?d,
                `position`=?d,
                `status`=?s,
                `parents`=?s,
                `allow_children`=?d,
				`nested_in`=?d,
				`nested_in_final`=?d,
				`dynamic_for`=?s,
                `annotation`=""',
                !empty($params['title']) ? $params['title'] : '',
                !empty($type_key) ? $type_key : '',
                $params['url'],
                $params['parent_id'],
                !empty($params['only_items']) ? 1 : 0,
                $max_position+1,
                self::STATUS_VISIBLE,
                $parents . $params['parent_id'] . '.',
                !empty($params['allow_children']) ? 1 : 0,
				!empty($params['nested_in']) ? $params['nested_in'] : NULL,
				!empty($params['nested_in_final']) ? $params['nested_in_final'] : NULL,
				!empty($params['dynamic_for']) ? $params['dynamic_for'] : NULL
            );
        $type = self::getById($type_id, $segment_id);
        if (empty($params['allow_children'])){
            $props = array_merge($props, $type->getDefaultProps());
        }
        $type->onTypeChange();
        foreach (self::$dataProviders as $p){
            /* @var $p iTypeDataProvider */
            $p->onCreate($type, $params);
        }
        $type->createGroups($groups);
        $type->createProps($props, TRUE);
        // Удаляем возможно созданные ранее редиректы с урла типа
        PageRedirect::getInstance()->deleteWithAllSegments($type_url);
        // Очищаем кэш подтипов родителя, чтобы вновь созданный тип был доступен в getChildren() родителя
        $parent_type->clearChildrenCache();
        return $type;
    }

    /**
     * Проверка ключа на соответствие формату и уникальность типа в пределах ветки,
     * @param string $key проверяемый ключ
     * @param int $type_id
     * @param bool $check_via_parent_type TRUE если проверка производится относительно родительского типа
     * нужно при создании нового типа
     * @throws \Exception
     * @throws \MysqlSimple\Exceptions\InvalidArgumentException
     * @return bool
     */
    private static function checkKey($key, $type_id, $check_via_parent_type = TRUE, &$errors = array()){
        $errors = array();
        if (empty($key)){
            $errors['key'] = \Models\Validator::ERR_MSG_EMPTY;
        }elseif (preg_match('~[^a-zA-Z0-9\-_]~', $key)){
            $errors['key'] = \Models\Validator::ERR_MSG_INCORRECT_FORMAT;
        }elseif (strlen($key) > SeoConfig::ENTITY_KEY_MAX_LENGTH){
            $key = substr($key, 0, SeoConfig::ENTITY_KEY_MAX_LENGTH);
//            $errors['key'] = \Models\Validator::ERR_MSG_TOO_BIG;
        } else {
            $type = Type::getById($type_id);
            $parent = $type->getParent();
            if (empty($type)){
                throw new \Exception('Type#' . $type_id . ' not found');
            }
            if ($check_via_parent_type) {
                // если проверяем относительно родительского типа нам нужны id родителей, самого типа и его непосредственных потомков
                $self_level_types = $type->getChildren();
                $types_ids = array($type_id);
            } elseif (!empty($parent)) {
                // если у самого типа, то id родителей, своих потомков и непосредственных потомков ближайшего родителя
                $self_level_types = $parent->getChildren();
                unset($self_level_types[$type_id]); // самого себя не учитываем
                $types_ids = array();
                $children = $type->getAllChildren();
                foreach ($children as $inner_children) {
                    /** @var $inner_children int[] */
                    $types_ids = array_merge($types_ids, !empty($inner_children) ? array_keys($inner_children) : array());
                }
            }
            $type_tree_ids = array_merge($type['parents'], $types_ids, array_keys($self_level_types));
            self::init();
            $is_exists = self::$db->query('SELECT `id` FROM `' . self::TABLE_ITEM_TYPES . '` WHERE `id` IN (?i) AND `key` = ?s', $type_tree_ids, $key)->getCell();
            if ($is_exists){
                $i = 0;
                do{
                    $i++;
                    if ($i > 9) {
                        $errors['key'] = \Models\Validator::ERR_MSG_EXISTS;
                        return $key;
                    }
                } while (self::$db->query('SELECT `id` FROM `' . self::TABLE_ITEM_TYPES . '` WHERE `id` IN (?i) AND `key` = ?s', $type_tree_ids, $key.'_'.$i));
                $key = $key.'_'.$i;
//                $errors['key'] = \Models\Validator::ERR_MSG_EXISTS;
            }
        }
        return $key;
    }
    /**
     * забрать все существующие каталоги
     * @return Type[]
     */
    public static function getCatalogs(){
        return self::search(array('parent_id' => self::DEFAULT_TYPE_ID, 'key' => CatalogConfig::getCatalogKeys()));
    }
    /**
     * Функция поиска типов, отдает только ids,
     * чтобы получить объекты, пользуемся self::search($params)
     * @param array $params
	 * @return Type[]
	 * @throws \LogicException
     */
    public static function getIds($params = array()){
		if (empty($params['visible']) xor !isset($params['segment_id'])){//Если есть только что-то одно, то ошибка. Т.к. одно без другого бессмысленно
			throw new \LogicException('Params "visible" and "segment_id" must be both isset or not isset');
		}
        self::init();
        $type_ids = self::$db->query('SELECT `t`.`id` FROM `'.self::TABLE_ITEM_TYPES.'` AS `t`
			WHERE 1
            AND `t`.`status` != "'.self::STATUS_DELETE.'"
            { AND `t`.`key` = ?s}
            { AND `t`.`key` IN (?l)}
            { AND `t`.`key` NOT IN (?l)}
            { AND `t`.`url` = ?s}
            { AND `t`.`parent_id` = ?d}
            { AND `t`.`parents` LIKE ?s}
            { AND `t`.`status` IN (?l)}
            { AND `t`.`tags` LIKE ?}
			{ AND `t`.`allow_children` = ?d}
            { AND `t`.`id` IN (?i)}
            { AND `t`.`id` NOT IN (?i)}
            { AND `t`.`nested_in` = ?d}' .
            (array_key_exists('nested_in_final', $params) ? (' AND `t`.`nested_in_final` ' . (!empty($params['nested_in_final']) ? '= 1' : 'IS NULL')) : ''),
                !empty($params['key']) && !is_array($params['key']) ? $params['key'] : self::$db->skipIt(),
                !empty($params['key']) && is_array($params['key']) ? $params['key'] : self::$db->skipIt(),
                !empty($params['not_key']) ? (is_array($params['not_key']) ? $params['not_key'] : array($params['not_key'])) : self::$db->skipIt(),
                !empty($params['url']) ? $params['url'] : self::$db->skipIt(),
                !empty($params['parent_id']) ? $params['parent_id'] : self::$db->skipIt(), // Когда нужны только непосредственные потомки
                !empty($params['parents']) ? '%.' . $params['parents'] . '.%' : self::$db->skipIt(), // Все потомки типа с указанным id
				!empty($params['status']) ? $params['status'] : self::$db->skipIt(),
                !empty($params['tags']) ? ('%.' . $params['tags'] . '.%') : self::$db->skipIt(),
				isset($params['allow_children']) ? $params['allow_children'] : self::$db->skipIt(),
                !empty($params['ids']) ? $params['ids'] : self::$db->skipIt(),
                !empty($params['not_ids']) ? $params['not_ids'] : self::$db->skipIt(),
                !empty($params['nested_in']) ? $params['nested_in'] : self::$db->skipIt()
        )->getCol('id', 'id');
        return $type_ids;
    }
	/**
	 *
	 * @param array $params
	 * @return Type[]
	 * @throws \LogicException
	 */
    public static function search($params = array(), $segment_id = NULL){
        $type_ids = self::getIds($params);
        return self::factory($type_ids, $segment_id);
    }
    public static function getAllowChildrenTypesByParents($status){
        self::init();
        return self::$db->query('
            SELECT `parent`.`title` AS `parent_title`, `type`.`id` AS `type_id`, `type`.`title` AS `type_title` FROM `'.static::TABLE_ITEM_TYPES.'` AS `type`
            INNER JOIN `'.static::TABLE_ITEM_TYPES.'` AS `parent` ON (`type`.`parent_id` = `parent`.`id`)
            WHERE `type`.`allow_children` = 0 AND `type`.`status` IN (?l)
        ', $status)->select('parent_title', 'type_id');
    }
    /**
     * Собираем типы в линейный массив с указанием уровня вложенности
     * @param array $statuses
     * @return array
     */
    public static function getTypesByLevel(array $statuses = array(self::STATUS_VISIBLE), $type_id = NULL, $segment_id = NULL){
		$type_id = !is_null($type_id) ? $type_id : self::DEFAULT_TYPE_ID;
        $main_type = self::getById($type_id, $segment_id);
        $all_types_by_parents = $main_type->getAllChildren($statuses);
        $result = array();
        if (!empty($all_types_by_parents[$type_id])){
            self::getLevel($all_types_by_parents, $type_id, $result, 0);
        }
        return $result;
    }
    /**
     * Рекурсионная функция для обхода дерева
     * @param array $types
     * @param int $type_id
     * @param array $result
     * @param int $level
     */
    private static function getLevel($types, $type_id, &$result, $level){
        if (!empty($types[$type_id])){
            foreach ($types[$type_id] as $type){
                $result[$type['id']]['level'] = $level;
                $result[$type['id']]['data'] = $type;
                if (!empty($types[$type['id']])){
                    self::getLevel($types, $type['id'], $result, $level+1);
                }
            }
        }
    }
    public static function clearCache($id = NULL) {
        $memory_key = self::getMemoryKey();
        if (!empty($id) && !empty(self::$registry[$id])){
            self::$registry[$id]->clearPropertiesCache();
            unset(self::$registry[$id]);
            /** @TODO решить что сделать с этим (массив стал двухуровневым) 
             * что делать? - пробегать по массиву. и искать не только id, но и parent_id
             * массив не большой, проблем не должно быть
             */
            $key = array_search($id, self::$idByKeys);
            if (!empty($key)){
                unset(self::$idByKeys[$key]);
            }
            if (!empty($memory_key)){
                \App\Builder::getInstance()->getSharedMemory()->remove($memory_key, $id);
            }
        }else{
            self::$registry = array();
            self::$idByKeys = array();
            if (!empty($memory_key)){
                \App\Builder::getInstance()->getSharedMemory()->remove($memory_key);
            }
        }
        if (!empty($id) && !empty(self::$segmentDataCache[$id])){
            unset(self::$segmentDataCache[$id]);
        }else{
            self::$segmentDataCache = array();
        }
    }
    /**
     * @param array $data
     */
    protected function __construct(array $data, $segment_id = NULL){
        self::init();
        $this->segment_id = $segment_id;
        if (!empty($data['counters'])){ //распаковка счетчиков
            $counters = explode(self::COUNTERS_DELIMETR, $data['counters']);
            $data['counters'] = array();
            foreach (self::$counters_order as $num => $counterName){
                $data['counters'][$counterName] = $counters[$num];
            }
        }else{
            $data['counters'] = null; //в этом случае счетчики будут распакованы при запросе @see getData()
        }
        $this->data = $data;
        foreach (self::$dataProviders as $p){
            /** @var $p iTypeDataProvider */
            $p->onLoad($this);
        }
    }
    /**
     * Для удобства возвращает ид
     * @return int
     */
    public function getId(){
        return $this->data['id'];
    }
    /**
     * Для удобства возвращает ключ
     * @return string
     */
    public function getKey(){
        return $this->data['key'];
    }
    /**
     * является ли категория категорией первого уровня (каталогом)
     * @return bool
     */
    public function isCatalog(){
        return $this['parent_id'] == self::DEFAULT_TYPE_ID;
    }
    /**
     * Возвращает предка - объект категории первого уровня (каталога).
     * Если категория самого верхнего уровня - возвращает NULL, если сам каталог, возвращает себя же
     * @return Type
     */
    public function getCatalog(){
        if ($this['id'] == self::DEFAULT_TYPE_ID){
            return NULL;
        }
        if ($this->isCatalog()){
            return $this;
        }
        $parents = $this->getParents();
        foreach ($parents as $p){
            if ($p->isCatalog()){
                return $p;
            }
        }
        throw new \Exception('Непредвиденная ситуация: type_id ID#' . $this->getId(). 'не начальная категория, не является каталогом и не находится в каталоге');
    }
    /**
     * Возвращает данные типа
     * @param string $key одно из следующих значений: <ul>
     * <li> int "id"
     * <li> string "title"
     * <li> int "parent_id"
     * <li> int "position"
     * <li> bool "only_items"
     * <li> int "status"
     * <li> array "counters"
     * <li> array "parents"
     * <li> bool "allow_children"
     * <li> int "post_id"
     * <li> string "annotation"
     * <li> Property[] "properties"
     * <li> array "parents"
     * <li> Post "parents"
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function getData($key){
        if ($key == 'counters' && !isset($this->data['counters'])){ //это позволяет не пересчитывать счетчики без необходимости
            $this->recountCounters();
        }elseif ($key == 'properties'){
            $this->getProperties();
        }elseif($key == 'segment_id'){
            return $this->segment_id;
        }
        if (!array_key_exists($key, $this->data)){
            throw new \InvalidArgumentException($key . ' is not avalible');
        }
        $ans = $this->data[$key];
        if ($key == 'parents' && is_string($ans)){
            if (empty($ans)){
                $ans = array();
            }else{
                $ans = explode('.', $ans);
                array_shift($ans);//лишние элементы при разделении
                array_pop($ans);
            }
        }
        return $ans;
    }
    public function getCover(){
        return $this['cover'];
    }
    /**
     * Отдает дефолтную картинку типа
     * @return type
     */
    public function getDefault(){
        return $this['default'];
    }
    /**
     * Каскадный поиск дефолтной картинки. Т.е., если у данного типа её нет, то берется у родительского
     * @return type
     */
    public function getCascadeDefault(){
        if (empty($this->data['default_image_id'])){
            $parents = $this->getParents();
            $reverse_parents = array_reverse($parents);
            foreach ($reverse_parents as $p){
                $def_id = $p['default_image_id'];
                if (!empty($def_id)){
                    break;
                }
            }
        }else{
            $def_id = $this->data['default_image_id'];
        }
        return !empty($def_id) ? \Models\ImageManagement\Image::getById($def_id) : NULL;
    }

    /**
     * Возращает список допустимых у типа свойств
     * @return Properties\Property[]
     * @throws \Exception
     */
    public function getProperties(){
        if (empty($this->data['properties'])){
            $this->data['properties'] = PropFactory::search($this->getData('id'), PropFactory::P_ALL, 'id', 'group', 'parents', array(), $this->segment_id, $cache_key);
        }
        return $this->data['properties'];
    }

    protected function clearPropertiesCache(){
        $this->data['properties'] = NULL;
        PropFactory::clearSearchDataCache(PropFactory::getCacheKey($this->getData('id'), PropFactory::P_ALL, 'group', 'parents', array(), $this->segment_id));
    }
	/**
	 * 
	 * @param int $property_id
	 * @return array('ids' => array(), 'available' => 'boolean')
	 */
	public function getPropertiesAvailable($property_id = NULL){
		if (empty($this->availableProperties)){
			$properties_hidden = self::$db->query('SELECT `property_id` FROM `'.self::TABLE_PROPERTIES_HIDDEN.'` WHERE `type_id` = ?d', $this['id'])->getCol('property_id', 'property_id');
			$enum_hidden = self::$db->query('SELECT `property_id`, `enum_id` FROM `'.self::TABLE_ENUM_VALUES_HIDDEN.'` WHERE `type_id` = ?d', $this['id'])->getCol(array('property_id', 'enum_id'), 'enum_id');
			$properties = $this->getProperties();
			foreach ($properties as $prop){
                //из-за настроек, может поменяться состав свойства именно в текущем типе
				if (/*$prop['type_id'] != $this['id'] && */$prop['data_type'] == Enum::TYPE_NAME){
					$this->availableProperties[$prop['id']] = array('ids' => array());
					if (!empty($prop['values'])){
						foreach ($prop['values'] as $enum_id => $enum){
							if (empty($enum_hidden[$prop['id']]) || empty($enum_hidden[$prop['id']][$enum_id])){
								$this->availableProperties[$prop['id']]['ids'][$enum_id] = $enum_id;
							}
						}
					}
				}
				$this->availableProperties[$prop['id']]['available'] = empty($properties_hidden[$prop['id']]) ? 1 : 0;
			}
		}
		return !empty($property_id) ? (!empty($this->availableProperties[$property_id]) ? $this->availableProperties[$property_id] : array()) : $this->availableProperties;
	}
	/**
     * Используется ли свойство в данном типе
     * @param Property $property
     * @return bool
     */
    public function checkPropertyAccessibility(Property $property, $enum_id = NULL){
		$property_available = $this->getPropertiesAvailable($property['id']);
        if (!empty($enum_id) && $property instanceof Enum){
            if (!empty($property_available['ids'][$enum_id])){
                return TRUE;
            }
        }
        return !empty($property_available) ? ((bool) $property_available['available'] ) : TRUE;
    }
	/**
	 * устанавливает, будет ли использоваться свойство при редактировании товаров данного типа
	 * @param int $property_id
	 * @param bool $available
	 */
	public function setPropertyAvailable($property_id, $available){
		if ($available){
			self::$db->query('DELETE FROM `'.self::TABLE_PROPERTIES_HIDDEN.'` WHERE `type_id` = ?d AND `property_id` = ?d', $this['id'], $property_id);
		}else{
			self::$db->query('REPLACE INTO `'.self::TABLE_PROPERTIES_HIDDEN.'` SET `type_id` = ?d, `property_id` = ?d', $this['id'], $property_id);
		}
	}
	/**
	 * Устанавливает, какие значения будут использоваться при редактировании товаров данного типа
	 * @param int $property_id
	 * @param array $enum_ids
	 */
	public function setEnumUsed($property_id, $enum_ids){
		$property = PropFactory::getById($property_id);
		if ($property['data_type'] == Enum::TYPE_NAME && !empty($property['values'])){
			foreach ($property['values'] as $enum_id => $enum){
                $this->setSingleEnumUse($property_id, $enum_id, in_array($enum_id, $enum_ids));
			}
		}
	}
    /**
     * 
     * @param int $property_id
     * @param int $enum_id
     * @param bool $use используется или нет
     */
    public function setSingleEnumUse($property_id, $enum_id, $use){
        if ($use){
            self::$db->query('DELETE FROM `'.self::TABLE_ENUM_VALUES_HIDDEN.'` WHERE `type_id` = ?d AND `property_id` = ?d AND `enum_id` = ?d', $this['id'], $property_id, $enum_id);
        }else{
            self::$db->query('REPLACE INTO `'.self::TABLE_ENUM_VALUES_HIDDEN.'` SET `type_id` = ?d, `property_id` = ?d, `enum_id` = ?d', $this['id'], $property_id, $enum_id);
        }
    }
    /**
	 * Обновляет счетчики элементов в типе
     */
    private function recountCounters(){
        $id = $this->getData('id');
        // COUNT(DISTINCT `t`.`id`)-1 (-1 для того, чтобы при подсчете количества подтипов не учитывался сам тип)
        // `t`.`allow_children` = 0 чтобы посчитывалось количество items, которые созданны только в разрешенных типах
        $sql = 'SELECT COUNT(DISTINCT `i`.`id`) AS `items`, (COUNT(DISTINCT `t`.`id`)-1) AS `types`
                FROM `'. Type::TABLE_ITEM_TYPES .'` AS `t`
                LEFT JOIN `'.Item::TABLE_ITEMS.'` AS `i` ON (`t`.`allow_children` = 0 AND `i`.`type_id` = `t`.`id` AND `i`.`status` != "'.Item::S_DELETE.'" AND `i`.`status` != "'.Item::S_TMP.'"{ AND `i`.`status` = ?})
                WHERE (`t`.`parents` LIKE ? OR `t`.`id`=?d)
                    AND `t`.`status` != "'.self::STATUS_DELETE.'"
                    {AND `t`.`status` = ?}';
        $visible_counters = self::$db->query($sql, Item::S_PUBLIC, '%.' . $id . '.%', $id, self::STATUS_VISIBLE)->getRow();
        $all_counters = self::$db->query($sql, self::$db->skipIt(), '%.' . $id . '.%', $id, self::$db->skipIt())->getRow();
        $all = array(
            self::V_ITEMS => $visible_counters['items'],
            self::ALL_ITEMS => $all_counters['items'],
            self::V_TYPES => $visible_counters['types'],
            self::ALL_TYPES => $all_counters['types']
        );
        $counters = array();
        foreach(self::$counters_order as $counter){//в заранее заданном порядке формируем массив счетчиков
            if (isset($all[$counter])){
                $counters[$counter] = $all[$counter];
            }else{
                throw new \LogicException('Неверное название счетчика: ' . $counter);
            }
        }
        $this->_updateParams(array('counters'=>implode(self::COUNTERS_DELIMETR, $counters)));
        $this->data['counters'] = $counters;
        return $this->data['counters'];
    }
    /**
     * очистить счетчики
     * меняет БД тоже
     */
    protected function clearCacheCounters(){
        if (isset($this->data['counters']) && !is_null($this->data['counters'])){
            $this->_updateParams(array('counters' => NULL));
            unset($this->data['counters']);
        }
    }
    /**
	 * Обновление данных о типе
     * Важно! Только для внутреннего использования (отсюда, и из хелперов)
     * @param array $params
	 * @param array $errors
     * @see $allowParams
     * @return bool
     * @internal
     */
    public function _updateParams($params, &$errors = NULL){
		foreach (self::$dataProviders as $p){
            /** @var $p iTypeDataProvider */
            $p->preUpdate($this, $params, $errors);
        }
		if (!Catalog::checkAllowed($params, self::$allowParams, false)){
            throw new \InvalidArgumentException('Incorrect $params');
        }
		$id = $this->getData('id');
        if (!empty($this['url']) && isset($params['url']) && $params['url'] != $this['url']){
            // При смене урла обновляем урл у всех дочерних типов
            self::replaceTypesUrl($this['url'], $params['url']);
        }
        $updated = FALSE;
        foreach ($params as $key => $value){
            if ($value != $this->data[$key]){
                $this->data[$key] = $value;
                if ($key == 'key'){
                    $this->onKeyChange();
                }
                $updated = TRUE;
            }
        }
        if ($updated){
            $result = self::$db->query('UPDATE `'.self::TABLE_ITEM_TYPES.'` SET ?a, `last_update` = NOW() WHERE `id`=?d', $params, $id);
        }
        foreach (self::$dataProviders as $p){
            /** @var $p iTypeDataProvider */
            $p->onUpdate($this);
        }
        self::clearCache($this['id']);
        return isset($result) ? $result : $updated;
    }

    private function createGroups($groups = array()) {
        if (!empty($groups)) {
            foreach($groups as $group) {
                $err = NULL;
                $this->addGroup(
                    !empty($group['title']) ? $group['title'] : NULL,
                    !empty($group['key']) ? $group['key'] : NULL,
                    $err
                );
                if (!empty($err)) {
                    throw new \ErrorException('Не удается создать группу свойств — исходные данные: ' . var_export($group, true) . ' ошибка: ' . var_export($err, true));
                }
            }
        }
    }
    /**
     * Создание свойств (на данный момент для создания свойств по дефолту)
     * @param array $props
     * @param bool $dont_check_default_key не проверять на уникальность ключ по умолчанию, нужно при создании дефолтных свойств
     */
    private function createProps($props = null, $dont_check_default_key = FALSE){
        if (!empty($props)){
            $type_props = $this->getProperties(true);
            $tp_by_key = array();
            foreach ($type_props as $p){
                $tp_by_key[$p['key']] = $p;
            }
            foreach ($props as $prop_val){
                if (!empty($tp_by_key[$prop_val['key']])){
                    continue;
                }
                if (!empty($prop_val['group_key'])) {
                    $groups = $this->getGroups();
                    if (!empty($groups)) {
                        foreach($groups as $g) {
                            if ($g['key'] == $prop_val['group_key']) {
                                $prop_val['group_id'] = $g['id'];
                                break;
                            }
                        }
                    }
                    unset($prop_val['group_key']);
                }
                $prop_val['type_id'] = $this->getData('id');
                $prop_id = Property::create($prop_val, $e, $this['segment_id'], $dont_check_default_key);
                if (empty($e)){
                    $property = PropFactory::getById($prop_id);
                    $prop_val['group_id'] = !empty($prop_val['group_id']) ? $prop_val['group_id'] : 0;
                    $property = $property->update($prop_val, $e, $dont_check_default_key);
                } else {
                    throw new \LogicException(var_export($e, true) . ' ' . var_export($prop_val, true));
                }
                if ($e){
                    var_export($e);
                    if(is_array($e)){
                        foreach ($e as &$er){
                            if (is_array($er)){
                                $er = implode(', ', $er);
                            }
                        }
                        $e = implode(': ', $e);
                    }
                    throw new \LogicException('Неверно заданы параметры для свойства, создающегося по умолчанию: ' . $e . ' ' . var_export($prop_val, true));
                }
            }
            // После создания свойств нужно очистить кэш, чтобы можно было сразу получить к ним доступ через $this->getProperties();
            $this->clearPropertiesCache();
        }
    }
    //проверяет, является ли тип родителем данного
    public function isParent($type_id){
        $parents = $this->getData('parents');
        return in_array($type_id, $parents);
    }
    /**
     * Проверяет, включает ли в себя тип определенное свойство
     * @param \Models\CatalogManagement\PropFactory $property
     * @return type
     */
    public function includesProperty(Property $property){
        return $this['id'] == $property['type_id'] || $this->isParent($property['type_id']);
    }
    /**
     * Обновление данных о типе
     * @param array $params
     * список допустимых параметров и значений:<ul>
     * <li>title = <i>string</i>
     * <li>description = <i>string</i>
     * </ul>
     * @see $editableParams
     * @throws \InvalidArgumentException
     * @internal param int $id
     * @return bool
     */
    public function update($params, &$errors = NULL){
		if (array_key_exists('title', $params) && empty($params['title'])){
			$errors['title'] = \Models\Validator::ERR_MSG_EMPTY;
		}
        $type_key = array_key_exists('key', $params) ? $params['key'] : NULL;
        $type_key = self::checkKey($type_key, $this['id'], FALSE, $errors);
        if (!empty($errors)){
            return false;
        }
        if ($type_key != $this['key']){
            $params['key'] = $type_key;
            $params['url'] = self::generateNewUrl($params['key'], $this['parent_id']);
        }
		if (array_key_exists('allow_children', $params)){
			if (!$this->checkChangingAllowChildren($params['allow_children'] ? 1 : 0)){
				$errors['allow_children'] = 'Невозможно разрешить создание дочерних типов, т.к. в данном типе присутствуют товары';
				return false;
			}else{
				$this->onChangeAllowChildren($params['allow_children'] ? 1 : 0);
			}
		}
        return $this->_updateParams($params, $errors);
	}
    /**
     * Действия при изменении параметра allow_children
     * @param bool $param
     * @param array $default_props параметры, которые нужны в конечном типе
     * @param array $default_groups группы, которые нужны в конечном типе
     * @return bool
     */
    public function setAllowChildren($param){
        if (!$this->checkChangingAllowChildren($param)){
            return false;
        }
		$this->onChangeAllowChildren($param);
		return $this->_updateParams(array('allow_children' => $param));
    }
    /**
     * проверка возможности поменять параметр allow_children
     * @return bool
     */
    public function checkChangingAllowChildren($param){
        if (empty($param) == empty($this->getData('allow_children'))){ // у каталога allow_children может иметь значение от 0 до 5
            return true;
        }
        if (!$this->isEmpty()){
            return false;
        }
        return true;
    }
	private function onChangeAllowChildren($param){
		$props = $this->getDefaultProps();
		if (empty($props)){
			return;
		}
		if ($param){//удаляем дефолтные свойства и группы
			if (!empty($props)){
				$props_keys = array();
				foreach ($props as $pr){
					$props_keys[$pr['key']] = $pr['key'];
				}
				$properties = $this->getProperties();
				if (!empty($properties)){
					foreach ($properties as $prop){
						if (!empty($props_keys[$prop['key']])){
                            Property::delete($prop['id']);
						}
					}
				}
			}
		}else{//если это теперь конечный тип, то добавляем дефолтные группы и свойства
            $type_props = $this->getProperties();
            foreach($type_props as $prop){
                if ($prop['type_id'] == $this['id'] && $prop['default_prop']){
                    $data = $prop->asArray();
                    unset($data['values']);
                    $data['default_prop'] = NULL;
                    $prop->update($data, $error, TRUE);
                }
            }
			$this->createProps($props, TRUE);
		}
	}

    /**
     * Поменять статус
     * @param string $status
     * @see const STATUS_*
     * @return bool
     */
    public function setStatus($status){
        if (array_search($status, self::$statuses) === false){
            return false;
        }
        $result = $this->_updateParams(array('status'=>$status));
        $this->onTypeChange();
        return $result;
    }

    /**
     * Изменение порядка сортировки типов
     * @param int $move_num
     * @return bool
     */
    public function move($move_num){
        if (!empty($move_num)){
            $position = $this->getData('position');
            $parent_id = $this->getData('parent_id');
            if ($position > $move_num){
                self::$db->query('
                    UPDATE `'.self::TABLE_ITEM_TYPES.'`
                    SET `position`=`position`+1
                    WHERE `parent_id`=?d AND `position`>=?d AND `position`<?d',
                    $parent_id, $move_num, $position
                 );
            }else{
                self::$db->query('
                    UPDATE `'.self::TABLE_ITEM_TYPES.'`
                    SET `position`=`position`-1
                    WHERE `parent_id`=?d AND `position`<=?d AND `position`>?d',
                    $parent_id, $move_num, $position
                );
            }
            return $this->_updateParams(array('position'=>$move_num));
        }
        return false;
    }
    public function updateFixed($param){
        return $this->_updateParams(array('fixed' => $param));
    }
    /**
     * Удаление типа возможно только если все Items и дочерние типы удалены, инчае возвращается FALSE
     * @param string|null $e
     * @return bool
     */
    public function delete(&$e = null){
        $id = $this->getData('id');
        if (!$this->isEmpty()){
            $e = 'type not empty';
            return FALSE;
        }
        foreach (self::$dataProviders as $p){
            /** @var $p iTypeDataProvider */
            $p->onDelete($this);
        }
        $props = PropFactory::search($id, PropFactory::P_ALL);
        foreach ($props as $p) {
            /* @var $p PropFactory */
            if ($p['type_id']==$id){
                Property::delete($p['id']);
            }
        }
        self::$db->query('
            UPDATE `'.self::TABLE_ITEM_TYPES.'`
            SET `position` = `position` - 1
            WHERE `position` > ?d
                AND `parent_id` = ?d',
                $this->getData('position'),
                $this->getData('parent_id'));
        unset(self::$registry[$id]);
        $this->onTypeChange();
        if (!empty($this['parent_id'])){
            self::clearCache($this['parent_id']);
        }
        return self::$db->query('DELETE FROM `'.self::TABLE.'` WHERE `id` = ?d', $id);
    }

    /**
     * @param int|null $types_count
     * @param int|null $items_count
     * @return bool
     */
    public function isEmpty(&$types_count = null, &$items_count = null){
        $counters = $this->getData('counters');
        $types_count = $counters[self::ALL_TYPES];
        $items_count = $counters[self::ALL_ITEMS];
        $catalog = $this->getCatalog();
        if ($catalog['nested_in'] && $this['id'] != $catalog['id']) {
            $nested_in_ids = self::getIds(array('nested_in' => $this['id']));
        }
        return empty($types_count) && empty($items_count) && empty($nested_in_ids);
    }

    /**
     * Возвращает список доступных ([дочерних]) типов
     * @param array $status
     * @param bool $depth вытащить все дочерние рубрики
     * @return array('parent_id'=>array('id'=> Type))
     */
    private function getDependedTypes(array $status = array(self::STATUS_VISIBLE), $depth = false){
        $id = $this->getData('id');
        $type_ids_by_parents = self::$db->query('
            SELECT `id`,`parent_id` FROM `'.self::TABLE_ITEM_TYPES.'`
            WHERE `status` != "'.self::STATUS_DELETE.'"
            { AND `parent_id` = ?d}
            { AND `parents` LIKE ?}
            ORDER BY `position`',
                (!$depth && !is_null($id)) ? $id : self::$db->skipIt(),
                ($depth && !is_null($id)) ? ('%.' . $id . '.%') : self::$db->skipIt()
        )->select('parent_id', 'id');
        $type_ids = array();
        foreach ($type_ids_by_parents as $types_group){
            $type_ids = array_merge($type_ids, array_keys($types_group));
        }
        $types = self::factory($type_ids, $this->segment_id);
        $types_by_parents = array();
        foreach ($types as $t_id => $type){
            $types_by_parents[$type->getData('parent_id')][$t_id] = $type;
        }
        return $types_by_parents;
    }

    /**
     * Вытаскивает подтипы
     * @param mixed $statuses
     * @return Type[]
     */
    public function getChildren($statuses = array(self::STATUS_VISIBLE)){
        if (!is_array($statuses)){
            $statuses = array($statuses);
        }
		if (!isset($this->children['one_level'])){
            $id = $this->getData('id');
			$types_by_parent = $this->getDependedTypes($statuses);
			$this->children['one_level'] = !empty($types_by_parent[$id]) ? $types_by_parent[$id] : array();
		}
        $types_group = $this->children['one_level'];
        foreach($types_group as $t_id => $type_data){
            if (!in_array($type_data['status'], $statuses)){
                unset($types_group[$t_id]);
            }
        }
		return $types_group;
    }

    protected function clearChildrenCache(){
        $this->children = array();
    }

    /**
     * Проверяет, можно ли ставить галочку о вложенных типах данному типу
     * @return bool
     * @throws \Exception
     */
    public function isCanHasChildren(){
        $levels_count = count($this->getParents()) - 1;
        return ($levels_count < $this->getCatalog()['allow_children']);
    }

    /**
     * Проверяет, можно ли ставить галочку о вложенных типах данному типу
     * @return bool
     * @throws \Exception
     */
    public function isChildrenCanHasChildren(){
        $levels_count = count($this->getParents());
        return ($levels_count < $this->getCatalog()['allow_children']);
    }

    /**
     * Возвращает максимальный уровень вложенности существующих типов в каталоге
     * @return int
     */
    public function getCatalogMaxChildrenLevel(){
        $search_string = '.'.implode('.', $this['parents']).'.'.$this['id'].'.%';
        $childLines = self::$db->query('SELECT `parents` FROM ?# WHERE `parents` LIKE ?s', static::TABLE, $search_string)->getCol('parents', 'parents');
        $result = 0;
        foreach($childLines as $line){
            $count = count(explode('.', trim($line, '.'))) - 1;
            if ($count > $result){
                $result = $count;
            }
        }
        return $result;
    }

    /**
     * Вытаскивает все подтипы любой вложенности
     * @param array $statuses
     * @return array('parent_id' => array('id' => Type))
     */
    public function getAllChildren(array $statuses = array(self::STATUS_VISIBLE)){
		if (!isset($this->children['all'])){
			$this->children['all'] =  $this->getDependedTypes($statuses, true);
		}
        $types_by_parents = $this->children['all'];
        foreach($types_by_parents as &$types_group){
            foreach($types_group as $t_id => $type_data){
                if (!in_array($type_data['status'], $statuses)){
                    unset($types_group[$t_id]);
                }
            }
        }
		return $types_by_parents;
    }
    /**
     * Возвращает объект ближайшего родителя
     * @return Type|null
     */
    public function getParent(){
        if ($this['id'] != self::DEFAULT_TYPE_ID){
            return self::getById($this['parent_id'], $this->segment_id);
        }else{
            return NULL;
        }
    }

    /**
     * Возвращает список всех родительских типов для запрашиваемого (путь до типа)
     * @return Type[] id as key
     */
    public function getParents(){
        $parents_ids = $this->getData('parents');
        return self::factory($parents_ids, $this->segment_id);
    }

    /**
     * Возвращает список конечных категорий для категории из каталога с наследуемыми айтемами
     * @return Type[]
     * @throws \Exception
     */
    public function getNestedInFinalTypes() {
        $catalog = $this->getCatalog();
        if (!$catalog['nested_in']) {
            throw new \LogicException('getNestedInFilialTypes() можно вызывать только для категорий из каталога с наследуемыми айтемами');
        }
        $final_types = static::search(array('nested_in_final' => 1, 'parents' => $catalog['id']));
        if ($this['id'] == $catalog['id']) {
            // Для каталога возвращаем все конечные категории
            return $final_types;
        }
        // Для остальных - отсеиваем категории, не имеющие связи с текущей
        $result = array();
//        $this->getParents();
        foreach ($final_types as $id => $f_type) {
            if (!$f_type['nested_in']) {
                continue;
            }
            $parent = static::getById($f_type['nested_in']);
            while ($parent['id'] != $this['id']
                && $parent['id'] != $catalog['id']
                && $parent['nested_in']) {
                $parent = static::getById($parent['nested_in']);
            }
            if ($parent['id'] == $this['id']) {
                $result[$id] = $f_type;
            }
        }
        return $result;
    }
    /**
     * Получить все вложенные категории в данную категорию
     * @return Type[]
     */
    public function getNestedTypes(){
        $catalog = $this->getCatalog();
        if ($catalog['nested_in']){
            return self::search(array('nested_in' => $this['id']), $this->segment_id);
        }
        return NULL;
    }
    /**
     * Запросить категорию, в которую вложена данная
     * @return Type
     */
    public function getNestedIn(){
        return !empty($this['nested_in']) ? self::getById($this['nested_in'], $this->segment_id) : NULL;
    }
    /**
     * При изменении типа (создание, удаление, смена статуса)
     */
    private function onTypeChange(){
        Item::clearCache(NULL, $this->getData('id'));
        $parents = $this->getParents();
        $this->clearCacheCounters();
        foreach ($parents as $parent){
			if (empty($parent)){
				continue;
			}
            $parent->clearCacheCounters();//очищаем счетчики у всех родителей данного типа
        }
    }
    private function onKeyChange(){
        $catalog = $this->getCatalog();
        if (!empty($catalog) && $catalog['key'] == CatalogConfig::CONFIG_KEY){
            Positions\Settings::setCachedKeys(array());//почистим кэш, т.к. там лежат ключи категории
        }
    }
    /**
     * При изменении айтема
     * @TODO мне кажется не разумным сбрасывать счетчики всякий раз при изменении какой-либо позиции, при удалении и создании да, но при изменении нет
     * RE: @TODO При изменении видимости у айтемов, счетчики меняются.
     */
    public function onItemChange(){
        $this->clearCacheCounters();
        $parents = $this->getParents();
        foreach ($parents as $parent){
            $parent->clearCacheCounters();//очищаем счетчики у всех родителей данного типа
        }
    }

    /**
     * Возвращает url типа
     * @param null $segment_id
     * @return string|null
     */
    public function getUrl($segment_id = NULL){
//        return ($this['id'] != self::DEFAULT_TYPE_ID) ? $this['url'] : NULL;
        return CatalogConfig::getTypeUrl($this, $segment_id);
    }

    /**
     * Генерация урла типа на основе его ключа и родителя
     * @param string $key
     * @param int $parent_type_id
     * @return string
     */
    public static function generateNewUrl($key, $parent_type_id){
        $url = '/'.$key.'/';
        $parent_type = self::getById($parent_type_id);
        while($parent_type['id'] != self::DEFAULT_TYPE_ID){
            $url = '/' . $parent_type['key'] . $url;
            $parent_type = $parent_type->getParent();
        }
        return $url;
    }

    /**
     * Обновление урла типа и его потомков
     * @param string $old_url
     * @param string $new_url
     */
    private static function replaceTypesUrl($old_url, $new_url){
        if (!empty($old_url)){
            self::init();
            self::$db->query('UPDATE `' . self::TABLE_ITEM_TYPES . '` SET `url` = REPLACE(`url`, ?s, ?s) WHERE `url` LIKE ?s', $old_url, $new_url, $old_url.'%');
        }
    }
    /**
     * Поиск свойств и значений для фильтра
     * @param int $filter_visible
     * @return array()
     */
    public function getSearchableProperties($filter_visible = CatalogConfig::FV_PUBLIC, $publicOnly = TRUE, $kustik_search = false, $segment_id = NULL){
        if (is_null($segment_id)){
            $segment_id = $this->segment_id;
        }
        return Catalog::factory($this->getCatalog()->getKey(), $segment_id)->getSearchableProperties($this['id'], $publicOnly ? 'public' : 'exist', null, array('filter_visible' => $filter_visible), array(), 'group', true, $kustik_search);
    }
    /*********   Группы свойств типа  ***********/


    /**
     * добавление
     * @param string $title
     * @param string $key
     * @param array $errors
     * @return int id
     */
    public function addGroup($title, $key = NULL, &$errors = NULL){
        return Group::add($title, $key, $this->getData('id'), $errors);
    }
    /**
     * удаление
     * @param int $group_id
     * @return bool
     */
    public function deleteGroup($group_id){
        return Group::delete($group_id);
    }
    /**
     * редактирование
     * @param int $group_id
     * @param string $title
     * @return bool
     */
    public function updateGroup($group_id, $params = array(), &$errors = NULL){
		if (empty($params)){
			return TRUE;
		}
		$group_to_move = Group::getById($this['id'], $group_id);
		if (empty($group_to_move)){
			throw new \Exception('Не найдена группа: ' . $group_id);
		}
        return $group_to_move->update($params, $errors);
    }
    /**
     * поменять номер позиции
     * @param int $group_id
     * @param int $move
     */
    public function moveGroup($group_id, $move){
        $group_to_move = Group::getById($this['id'], $group_id);
		if (empty($group_to_move)){
			throw new \Exception('Не найдена группа: ' . $group_id);
		}
		return $group_to_move->move($move);
    }

    /**
     * получить массив групп в типе
     * @return Group[]
     */
    public function getGroups(){
        return Group::get(array_merge(array($this->getData('id')), $this->getData('parents')), $this->getData('segment_id'));
    }

    /* ***************************** Теги ******************************/
    public function addTag($id){
        $type_tags = $this->getData('tags');
        if (array_search($id, $type_tags) === false){
            $type_tags[] = $id;
            return $this->_updateParams(array('tags' => '.' . implode('.', $type_tags) . '.'));
        }
        return false;
    }

    public function delTag($id){
        $type_tags = $this->getData('tags');
        $del_num = array_search($id, $type_tags);
        if ($del_num !== false){
            unset($type_tags[$del_num]);
            return $this->_updateParams(array('tags' => !empty($type_tags) ? ('.' . implode('.', $type_tags) . '.') : ''));
        }
        return false;
    }
    public function asArray(){
        $data = $this->data;
        foreach (self::$dataProviders as $p){
            /** @var $p iTypeDataProvider */
            $p->asArray($this, $data);
        }
        return $data;
    }

    /******************************* ArrayAccess *****************************/

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists ($offset){
        if (in_array($offset, self::$specials_keys) || isset(static::$dataProvidersByFields[$offset])){
            return true;
        }
        if ($offset == 'segment_id'){
            return isset($this->segment_id);
        }
        return isset($this->data[$offset]);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet ($offset){
        if(isset(self::$dataProvidersByFields[$offset])){
            return self::$dataProvidersByFields[$offset]->get($this, $offset, $this['segment_id']);
        }
        return $this->getData($offset);
    }

    /**
     * @param string $offset
     * @param mixed $value
     * @throws \Exception
     */
    public function offsetSet ($offset, $value){
        throw new \Exception(get_class($this).' has only immutable Array Access');
    }

    /**
     * @param string $offset
     * @throws \Exception
     */
    public function offsetUnset ($offset){
        throw new \Exception(get_class($this).' has only immutable Array Access');
    }


    /******************************* работа с iTypeDataProvider *****************************/
    /**
     * @var iTypeDataProvider[]
     */
    static $dataProviders = array();
    /**
     * @var iTypeDataProvider[]
     */
    static $dataProvidersByFields = array();

    /**
     * @static
     * @param iTypeDataProvider $provider
     */
    static function addDataProvider(iTypeDataProvider $provider){
        self::$dataProviders[get_class($provider)] = $provider;
        foreach ($provider->fieldsList() as $field){
            self::$dataProvidersByFields[$field] = $provider;
        }
    }

    /**
     * @static
     * @param iTypeDataProvider $provider
     */
    static function delDataProvider(iTypeDataProvider $provider){
        unset(self::$dataProviders[get_class($provider)]);
    }
}
