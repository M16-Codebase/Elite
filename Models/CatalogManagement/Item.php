<?php
namespace Models\CatalogManagement;
use App\Configs\CatalogConfig;
use App\Configs\SeoConfig;
use \Models\CatalogManagement\CatalogHelpers\Interfaces\iItemDataProvider;
use Models\CatalogManagement\Properties\Factory AS PropertyFactory;
use Models\Seo\PageRedirect;

/**
 * Класс предметной области каталога (позиция каталога), служит для доступа и работы на чтение. Для особых манипуляций используется класс Catalog
 */
class Item extends CatalogPosition{
    const MAX_REGISTRY_LENGHT = 500;
    /**таблица элементов*/
    const TABLE_ITEMS = 'items';
    /**таблица целочисленных значений свойств наименований*/
    const TABLE_PROP_INT = 'items_properties_int';
    /**таблица дробных значений свойств наименований*/
    const TABLE_PROP_FLOAT = 'items_properties_float';
    /**таблица строковых значений свойств наименований*/
    const TABLE_PROP_STRING = 'items_properties_string';
	const TABLE_ENTITY2COLLECTION = 'items2collecions';
    /** доп фильтр для свойств товаров */
    const PROPERTY_FILTER = PropertyFactory::P_ITEMS;
    /**
     * @var int[] ids Variant
     */
    private $variant_ids = array();

    private $parent_by_type_ids = array();
    /**
     * Разрешенные параметры для различных сущностей системы
     * @see Catalog::checkAllowed()
     */
    static protected $allowParams = array(
        'key',
        'type_id',
        'status',
        'last_update',
        'recreate_view',
        'position'
    );

    static protected $loadFields = array('id', 'key', 'type_id', 'time', 'post_id', 'parent_id', 'parents', 'status', 'recreate_view', 'recreate_range', 'position', 'last_update');
    /**
     * Имя основной таблицы
     */
    const TABLE = self::TABLE_ITEMS;
    /**
     * название основного поля в таблицах self::TABLE_PROP_*
     */
	const TABLE_PROP_OBJ_ID_FIELD = 'item_id';
    /**
     * Наименование поля, в пределах которого номер сортировки должен быть уникальным для каждой позиции
     */
    const UNIQUE_POSITION_IN_FIELD = 'type_id';
    /**
     * Самоопределение вариант это или айтем
     */
    const CATALOG_IDENTITY_KEY = 'item';
    /**
     * Название
     */
    const ENTITY_TITLE_KEY = CatalogConfig::KEY_ITEM_TITLE;
    /**
     * Колекция уже созданных Items, чтобы они не терялись
     * @var Item[]
     */
    static protected $registry = array();
	/**
     * Колекция уже прочитанных данных. нужна для того чтобы можно было создать объекты быстро
     * @var array[] (data rows)
     */
    static protected $dataCache = array();
    /**
     * @var type array
     */
    static protected $ids4Load = array();
	static protected $item2variants = array();
    /**
     * Чтение данных из которых потом собираются наименования
     *
     * @param int[] $ids
     * @return type
     */
    protected static function cacheUpData($ids){
        // в кеш данных не нужно читать данные уже созданных объектов и данные которые есть в кеше
        $ids = static::calculateCachedIdsList($ids);
        $result = array();
        if (empty ($ids)){
            return array();
        }
        $ids = static::loadCachedData($ids);
        $db = \App\Builder::getInstance()->getDB();
        if (!empty ($ids)){
            $result['main'] = $db->query('
                SELECT `items`.`'.implode('`, `items`.`', self::$loadFields).'`,
                    UNIX_TIMESTAMP(`time`) AS `timestamp`
                FROM  `'.self::TABLE.'` AS `items`
                WHERE `items`.`id` IN (?i)
                ',
                $ids
            )->select('id');
            $result['properties'] = parent::getPropertiesFromDB($ids);
            foreach ($ids as $id) {
                if (empty($result['main'][$id])){
                    self::$dataCache[$id] = NULL;
                }else{
                    $data = $result['main'][$id];
                    if (isset ($result['properties'][$id])){
                        $prop_data = $result['properties'][$id];
                        foreach ($prop_data as $segment){ // построим массив уникальных $propIds
                            PropertyFactory::prepareId(array_keys($segment));
                        }
                    }else{
                        $prop_data = NULL;
                    }
                    self::$dataCache[$id] = array('main' => $data, 'properties' => $prop_data, 'cache' => NULL);
                }
            }
        }
        //после того, как вытащили данные из таблиц, надо подготовить все типы, которые могут потребоваться
        $type_ids = array();
        foreach ($ids as $id){
            if (!empty(static::$dataCache[$id])){
                $type_ids[] = !empty(static::$dataCache[$id]['main']) ? static::$dataCache[$id]['main']['type_id'] : static::$dataCache[$id]['cache'][0]['type_id'];
            }
        }
        Type::prepare($type_ids);
    }
    /**
     * Фабрика объектов
     * @param int[] $ids
     * @return Item[]
     */
    public static function factory($ids, $segment_id = NULL){
        return parent::factory($ids, $segment_id);
    }
    public static function checkProperties($type_id, &$propValues, &$errors, $segment_id = NULL, $id = NULL, $item_id = NULL, $max_recreate_view = Catalog::MAX_RECREATE_VIEWS_ON_UPDATE){
        return parent::_checkProperties($type_id, $propValues, $errors, $segment_id, $id, $item_id, $max_recreate_view);
    }

    protected function __construct($id, $segment_id = NULL) {
        parent::__construct($id, $segment_id);
        if (!empty($this['parents'])){
            if (preg_match_all('~(\d+):(\d+)~', $this['parents'], $matches)) {
                static::prepare($matches[2]);
                $this->parent_by_type_ids = array_combine($matches[1], $matches[2]);
            }
        }
        // хак для вызова loadVariants из конструктора
        static::$registry[$id] = $this;
        if (!empty($this->data['recreate_range'])){
            $this->recreateRanges();
        }
    }

    public function recreateRanges(){
        $segments = array();
        $segments[0] = array();
        $segments += \App\Segment::getInstance()->getAll();
        //список доступных свойств
        $propList = $this->getType()->getRangeProps($this->segment_id);
        $updated = FALSE;
        foreach ($propList as $property) {
            foreach ($segments as $segment_id => $s){
				if ($property['segment'] == 1 && $segment_id == 0){
					continue;
				}
                $val = $this->getRangeValue($property);
                $old_value = $this['properties'][$property['key']];
				if ($val != $old_value['value']){
					if (empty($old_value['val_id'])){
						$this->createValue($property, $val, $segment_id);
					}else{
						$this->editValue($property, $old_value['val_id'], $val, NULL, array(), $old_value['value'], $segment_id);
					}
                    $updated = TRUE;
				}
				if ($property['segment'] != 1){
					break;
				}
            }
        }
        if ($this['recreate_range'] == 1){
            $this->updateEntityParams(array('recreate_range' => 0));
        }
        return $updated;
    }

    private function getRangeValue($property){
        if ($property['multiple'] == 1){
            throw new \LogicException('Свойство диапазон не может быть расщепляемым');
        }
        if ($property['data_type'] != Properties\Range::TYPE_NAME){
            throw new \LogicException('Расчет значения может проводиться только для ' . Properties\Range::TYPE_NAME . ' свойства');
        }
        $pValue = NULL;//конечное значение
        $range_prop_name = $property['values'];
        if (!empty($range_prop_name)){
            $variants = $this->getVariants(Variant::S_PUBLIC);
            $min = NULL; $max = NULL;
            foreach($variants as $variant){
                $prop_val = $variant['properties'][$range_prop_name]['real_value'];
                if (empty($prop_val)){
                    continue;
                }
                $prop_min = is_array($prop_val) ? min($prop_val) : $prop_val;
                if (is_null($min) || $min > $prop_min){
                    $min = $prop_min;
                }
                $prop_max = is_array($prop_val) ? max($prop_val) : $prop_val;
                if (is_null($max) || $max < $prop_max){
                    $max = $prop_max;
                }
            }
            if (!is_null($min)){
                $pValue = ($min != $max) ? $min . '—' . $max : $min;
            }
        }
        return $pValue;
    }
    /**
     * Закинуть в кеш все Variant для указанных (или имеющихся) Item
     * @param array $item_ids список Items для которых нужно предкешировать варианты
     * @return void;
     */
    static function cacheUpVariants4Items(array $item_ids = array()){
        if (empty ($item_ids))
            $item_ids = array_keys(self::$registry);
        if (empty($item_ids))
            return;
        $v_ids = \App\Builder::getInstance()->getDB()->query('SELECT `id` FROM '.Variant::TABLE.' WHERE `item_id` IN (?i)', $item_ids)->getCol('id', 'id');
        /** @var $v_ids int[] */
        Variant::prepare($v_ids);
        return;
    }
    /**
     * Возвращает конкретный объект
     * @param int $id
     * @return static
     */
    public static function getById($id, $segment_id = NULL){
         return parent::getById($id, $segment_id);
    }

    /**
     * Возвращает id объекта по его ключу или null
     * @param string $key
     * @return int | null
     */
    public static function getTypeIdByKey($key){
        if (empty($key)) return null;
        $db = \App\Builder::getInstance()->getDB();
        return $db->query("SELECT type_id FROM " . self::TABLE . " WHERE `key` LIKE '%{$key}%'")->getCell();
    }

    /**
     * Создать элемент
     * @param int $type_id идентификатор типа
     * @param int $status
     * @param array $propValues массив в формате (key свойства => характеристики значения[]), если передать NULL в качестве характеристик значения, то указанное свойство будет удалено
     * характеристики значения это массив со следующими ключами:
     * <UL>
     *      <LI>int    <B>val_id</B>
     *      <LI>string <B>value</B>
     *      <LI>mixed  <B>options</B>
     * </UL>
     * @param array $errors
     * @param int $segment_id
     * @param null $parent_id — id родительского айтема в кустике, игнорируется, если это не кустистый айтем
     * @param null $insertId — если вставляем с изсестным Id
     * @return int
     * @throws \Exception
     * @throws \MysqlSimple\Exceptions\InvalidArgumentException
     */
    public static function create($type_id, $status = self::S_TMP, $propValues = array(), &$errors = NULL, $segment_id = NULL, $parent_id = NULL, $insertId = NULL){
        foreach (static::$dataProviders as $p){
            /* @var $p iItemDataProvider */
            $p->preCreate($type_id, $propValues, $errors, $segment_id);
        }
        if (!empty($errors)){
            return FALSE;
        }
        static::checkProperties($type_id, $propValues, $errors, $segment_id, NULL);
        if (!empty($errors)){
            return FALSE;
        }
        $type = Type::getById($type_id, $segment_id);
        if (empty($type) || $type['allow_children']){
            throw new \LogicException('Невозможно создать айтем в данном типе');
        }
        $db = \App\Builder::getInstance()->getDB();
        if ($type['nested_in']) {
            // Если категория содержит вложенные айтемы — проверяем $parent_id на корректность
            if (empty($parent_id)) {
                $errors['parent_id'] = \Models\Validator::ERR_MSG_EMPTY;
                return false;
            } elseif (!$db->query('SELECT 1 FROM ?# WHERE `id` = ?d AND `type_id` = ?d', static::TABLE, $parent_id, $type['nested_in'])->getCell()) {
                $errors['parent_id'] = \Models\Validator::ERR_MSG_INCORRECT;
                return false;
            } else {
                $parent = $db->query('SELECT `type_id`, `parents` FROM ?# WHERE `id` = ?d', static::TABLE, $parent_id)->getRow();
                $parents = (!empty($parent['parents']) ? $parent['parents'] : '.') . $parent['type_id'] . ':' . $parent_id . '.';
            }
        } else {
            // Игнорируем $parent_id, если категория не содержит айтемов, вложенных в кустистые айтемы
            $parent_id = NULL;
        }
        $max_position = $db->query('SELECT MAX(`position`) FROM `'.self::TABLE_ITEMS.'` WHERE `type_id`=?d', $type_id)->getCell();
        $id = $db->query('INSERT INTO `'.self::TABLE.'` SET `status` = ?d, `type_id` = ?d, `time` = NOW(), `position`=?d{, `parent_id` = ?d}{, `parents` = ?s}{, `id` = ?d}',
            $status,
            $type_id,
            $max_position + 1,
            !empty($parent_id) ? $parent_id : $db->skipIt(),
            !empty($parents) ? $parents : $db->skipIt(),
            !empty($insertId) ? $insertId : $db->skipIt()
        );
        foreach (static::$dataProviders as $p){
            /* @var $p iItemDataProvider */
            $p->onCreate($id, $segment_id);
        }
        $type->onItemChange();
        return $id;
    }

    /**
     * Очищение кеша с фильтрами
     * final, т.к. чистится всё подряд (из разных каталогов)
     * @param int $item_id
     * @param mixed $type_id
     * @param bool $fromDB
     * @return array id айтемов которые почистили
     */
    final public static function clearCache($item_id = null, $type_id = null, $fromDB = true){
        parent::_clearCache();
        if (!empty($item_id)){
            if (array_key_exists($item_id, self::$registry)){
                self::$registry[$item_id]->save();
                unset(self::$registry[$item_id]);
            }
            if (array_key_exists($item_id, self::$dataCache)){
                unset(self::$dataCache[$item_id]);
            }
            if (array_key_exists($item_id, self::$item2variants)){
                unset(self::$item2variants[$item_id]);
            }
            foreach (self::$dataProviders as $pr){
                $pr->onClearCache($item_id);
            }
        }else{
            foreach (self::$registry as $en){
                $en->save();
            }
            self::$registry = array();
            foreach (self::$dataProviders as $pr){
                $pr->onClearCache();
            }
            self::$dataCache = array();
            self::$item2variants = array();
        }
        if ($fromDB){
            if (is_null($item_id)){
                $item_id = array();
            }
            $db = \App\Builder::getInstance()->getDB();
            if (!empty($type_id)){
                $item_id = $db->query('SELECT `id` FROM '.static::TABLE.' WHERE `type_id` IN (?i)', is_array($type_id) ? $type_id : array($type_id))->getCol('id', 'id');
            }elseif(!empty($item_id) && !is_array($item_id)){
                $item_id = array($item_id);
            }
            $db->query('DELETE FROM `'.static::TABLE_DATA_CACHE.'` WHERE `type` = "'.static::CATALOG_IDENTITY_KEY.'"{ AND `id` IN (?i)}', !empty($item_id) ? $item_id : $db->skipIt());
        }else{//если удалили из базы, значит хотим пересобрать вручную
            static::saveCacheData();
        }
        return $item_id;
    }

    /**
     * Проверка на уникальность значения свойства
     * @TODO проверка set свойств???
     * @param string $prop_key
     * @param string $value
     * @param int $segment_id
     * @param int $id Id элемента (для которого проверяем). Не требуется, если элемент ещё не создан.
     * @param int $type_id Требуется, если не передан id элемента
     * @throws \Exception
     * @throws \MysqlSimple\Exceptions\InvalidArgumentException
     * @return bool TRUE if value is unique
     */
    public static function checkUniqueValue($prop_key, $value, $segment_id = NULL, $id = NULL, $type_id = NULL){
        if (!empty($id)){
            $item = static::getById($id, $segment_id);
            $propertyList = $item->getPropertyList('key');
        }elseif(!empty($type_id)){
            $propertyList = PropertyFactory::search($type_id, PropertyFactory::P_ITEMS, 'key');
        }else{
            throw new \LogicException('Должен передаваться либо id элемента, либо id типа');
        }
        if (!isset ($propertyList[$prop_key]))
            throw new \Exception('incorrect property key "'.$prop_key.'" for item#'.$id);
        $property = $propertyList[$prop_key];
        $db=\App\Builder::getInstance()->getDB();
        return !$db->query('
            SELECT 1
            FROM `'.$property['table'].'` `p`
            INNER JOIN `'.self::TABLE.'` `i` ON (
                `i`.`id` = `p`.`item_id` AND
                `i`.`status` NOT IN (?l)
            )
            WHERE
                `p`.`property_id`=?d AND
                `p`.`value` IN (?l){ AND
                `p`.`item_id`!=?d}{ AND
                `p`.`segment_id` = ?d}
            ',
                array(Item::S_TMP, Item::S_DELETE),
                $property['id'],
                is_array($value) ? $value : array($value),
                !is_null($id) ? $id : $db->skipIt(),
                $property['segment'] ? $segment_id : $db->skipIt()
            )->getCell();
    }
    /**
     * Отчистка базы данных от старых хвостов.
     * @return bool
     */
    public static function onCleanup(){
        $db = \App\Builder::getInstance()->getDB();
        foreach (array(Item::TABLE_PROP_INT, Item::TABLE_PROP_STRING, Item::TABLE_PROP_FLOAT) as $table){
            // Удаляются те записи, которые никуда вообще не ссылаются или ссылаются на удаленные Items или Variants
            $db->query('
                DELETE `pv` FROM ?# AS `pv`
                    LEFT JOIN ?# AS `i` ON (`pv`.`item_id` = `i`.`id`)
                    WHERE
                        `i`.`status` = ? OR
                        `i`.`id` IS NULL',
                $table, Item::TABLE, Item::S_DELETE
            );
        }
        // Удаляются ненужные варианты
        $db->query('
            UPDATE ?# AS `v`
                LEFT JOIN ?# AS `i` ON (`v`.`item_id` = `i`.`id`)
                SET `v`.`status` = ?
                WHERE
                    `i`.`status` = ? OR
                    `i`.`id` IS NULL',
            Variant::TABLE, Item::TABLE, Variant::S_DELETE, Item::S_DELETE
        );
        // Удаляются ненужные items
        $db->query('
            DELETE `i` FROM ?# AS `i`
                WHERE
                    `i`.`status` = ?',
            Item::TABLE, Item::S_DELETE
        );
        foreach (self::$dataProviders as $p){
            /* @var $p iItemDataProvider */
            $p->onCleanup();
        }
        Variant::cleanup();
        return true;
    }
    public function isCollectionExists($color){
        $collections = $this['galleries'];
        foreach ($collections as $collection){
            if ($collection->getColor() == $color){
                return TRUE;
            }
        }
        return FALSE;
    }
    /**
     *
     * @return Type
     */
    public function getType(){
        return Type::getById($this->data['type_id'], $this->segment_id);
    }

    /**
     * Удаление Item
     * @param array $errors
     * @param bool $db удалять ли из базы?
     * @return bool
     */
    public function delete(&$errors = array(), $db = false){
        $type = $this->getType();
        $catalog = $type->getCatalog();
        if ($catalog['nested_in'] && !$type['nested_in_final']) {
            $child_count = Search\CatalogSearch::factory($catalog['key'])
                ->setPublicOnly(false)
                ->setRules(array(Rules\Rule::make('parent_id')->setValue($this['id'])))
                ->searchItemIds()
                ->count();
            if ($child_count) {
                $errors[] = array(
                    'key' => 'item',
                    'error' => 'not_empty'
                );
                return false;
            }
        }
        $variants = $this->getVariants();
        foreach ($variants as $variant){
            /* @var $variant Variant */
                $variant->_delete($errors, $db);
        }
        Type::getById($this->data['type_id'])->onItemChange();
        parent::delete($errors, $db);
        return empty($errors);
    }

    /**
     * Обновить данные элемента
     * @param array $params
     * @see parent::update()
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
    public function update(Array $params = NULL, Array $properties = NULL, &$errors = array(), $segment_id = NULL){
        parent::update($params, $properties, $errors, $segment_id);
        Type::getById($this->data['type_id'])->onItemChange();
    }
    /**
     * Изменить значения свойств элемента (все или частично)
     * @param array $propValues массив в формате (id свойства => характеристики значения[]), если передать NULL в качестве характеристик значения, то указанное свойство будет удалено
     * характеристики значения это массив со следующими ключами:
     * <UL>
     *      <LI>int    <B>val_id</B>
     *      <LI>string <B>value</B>
     *      <LI>mixed  <B>options</B>
     * </UL>
     * @param array $errors в формате $errors['error_type'][$pId] для того, чтобы можно было сразу разобрать сколько ошибок какого типа
     * @param array $propIds массив свойств, которые реально поменялись
     * @throws \Exception
     * @return bool
     */
    protected function updatePropertiesValues($propValues, &$errors = array(), $segment_id = NULL, &$propIds = NULL) {
        $result = parent::updatePropertiesValues($propValues, $errors, $segment_id, $propIds);
        if ($result && !empty($propIds)) {
            $this->recreateRanges();//@TODO почему при редактировании айтема проверяются диапазоны? ведь они меняются только при изменении значений у вариантов?
            $variants = $this->getVariants();
            //@TODO придумать ограничения, при которых не придется пересчитывать составные свойства
            foreach ($variants as $v){
                $v->recreateViews();
            }
        }
        return $result;
    }

    /**
     * @return Properties\Property[]
     * @throws \Exception
     */
    protected function getPropListsForRecreateViews(){
        return PropertyFactory::search($this['type_id'], PropertyFactory::P_ITEMS, 'key', 'position', 'parents', array(), $this->segment_id);
    }

    protected function checkPropertyToRecreateView($property){
        return $property instanceof Properties\View && $property['multiple'] != 1;
    }

    protected function validateProperties(&$propValues, &$errors, $segment_id = NULL){
        return static::checkProperties($this['type_id'], $propValues, $errors, $segment_id, $this->id);
    }
    public function getVariantIds(){
        $this->loadVariants();
        return $this->variant_ids;
    }
    /**
     * @param array $status //список статусов
     * @param array $order //порядок сортировки $order: key => value, где value = -1 если сортировка в обратном порядке
     * @return Variant[]
     */
    public function getVariants($status = array(), $order = array()){
		$this->loadVariants();
        if (!empty($status) && !is_array($status)){
            $status = array($status);
		}
        $variants = Variant::factory($this->variant_ids, $this->segment_id);
        if (!empty($status) && !empty($variants)){//проверка имеющихся вариантов на допустимый статус
            foreach ($variants as $variants_id => $variant){
                if (!in_array($variant['status'], $status)){
                    unset($variants[$variants_id]);
                }
            }
        }
        if (!empty($order)){
             $order_func = function (Variant $a, Variant $b) use ($order){
                reset($order);
                while($key = key($order)){
                    $valueA = $a[$key];
                    $valueB = $b[$key];
                    if ($valueA == $valueB){
                        next($order);
                        continue;
                    }else{
                        return ($valueA < $valueB ? 1 : -1)*(current($order) == -1 ? -1 : 1);
                    }
                }
                return 0;
            };
            uasort($variants, $order_func); //применение функции сортировки.
        }
        return $variants;
    }
	private function loadVariants(){
		if (!array_key_exists($this->id, self::$item2variants)){
			$load_ids = array_keys(array_diff_key(self::$registry, self::$item2variants));
			if (!empty($load_ids)){
				$item2variants = \App\Builder::getInstance()->getDB()->query('
					SELECT `id`, `item_id` FROM `'.Variant::TABLE_VARIANTS.'`
					WHERE `item_id` IN (?i) AND `status` != "'.Variant::S_DELETE.'" AND `status` != "'.Variant::S_TMP.'"
					ORDER BY `position`', $load_ids
				)->getCol(array('item_id', 'id'), 'id');
				$variant_ids = array();
				foreach ($item2variants as $item_id => $ids){
					$variant_ids += $ids;
				}
				Variant::prepare($variant_ids);
                foreach ($load_ids as $item_id){
                    if (!isset($item2variants[$item_id])){
                        //если вариантов нет, то, чтобы не лезло опять на проверку в базу, рисуем заглушку
                        $item2variants[$item_id] = array();
                    }
                }
				self::$item2variants = self::$item2variants + $item2variants;
			}
		}
		$this->variant_ids = !empty(self::$item2variants[$this->id]) ? self::$item2variants[$this->id] : array();
	}
    /**
     * Создание варианта
     * @param int $status
     * @param array $propValues
     * @param array $errors
     * @param int $segment_id
     * @return int id варианта
     */
    public function createVariant($status = self::S_TMP, $propValues = array(), &$errors = NULL, $segment_id = NULL){
        foreach (static::$dataProviders as $p){
            $p->preVariantAdd($this, $propValues, $errors, $segment_id);
        }
        if (!empty($errors)){
            return;
        }
        $catalog = $this->getType()->getCatalog();
        $variant_class = CatalogConfig::getEntityClass($catalog['key'], 'variant');
        if (empty($variant_class) || !class_exists($variant_class)){
            $errors['exception'] = 'Неверно задан класс варианта ' . $variant_class;
            return;
        }
        $variant_id = $variant_class::_create($this->id, $status, $errors, $propValues, $segment_id);
        if (!empty($errors)){
            return;
        }
        Variant::prepare(array($variant_id));
        self::$item2variants[$this->id][$variant_id] = $variant_id;
        $this->variant_ids[$variant_id] = $variant_id;
        foreach (static::$dataProviders as $p){
            $p->onVariantAdd($this, $variant_id);
        }
        return $variant_id;
    }
    public function delVariant($variant_id, &$errors = array(), $remove_from_db = false){
        $variant = Variant::getById($variant_id);
        if (empty($variant) || $variant['item_id'] != $this['id']) {
            $errors[] = array(
                'key' => 'variant_id',
                'error' => \Models\Validator::ERR_MSG_EMPTY
            );
            return false;
        }
        foreach (static::$dataProviders as $p){
            $p->preVariantRemove($this, $variant);//надо до физического удаления, т.к. иногда требуются параметры самого варианта
        }
        $variant->_delete($errors, $remove_from_db);
        if (isset(self::$item2variants[$this->id][$variant_id])){
            unset(self::$item2variants[$this->id][$variant_id]);
        }
        if (isset($this->variant_ids[$variant_id])){
            unset($this->variant_ids[$variant_id]);
        }
        foreach (static::$dataProviders as $p){
            $p->onVariantRemove($this, $variant_id);
        }
    }

    /**
     * URL айтема
     * Если в настройках каталога указано, что сущности не имеют публичного урла — возвращает NULL
     * @param int|null $segment_id
     * @return null|string
     * @throws \Exception
     */
    public function getUrl($segment_id = NULL){
        if ($this->getType()->getCatalog()['allow_item_url']){
			$permuri=\App\Configs\CatalogConfig::getItemUrl($this, $segment_id);
			if(!empty($_SERVER['REQUEST_URI'])&&strpos($_SERVER['REQUEST_URI'],'arenda')){
			$permuri = rtrim($permuri,'/');
			$url_tmp = explode('/',$permuri);
			$permuri='/arenda/'.end($url_tmp).'/';
			}
			$permuri=str_replace('complex/','',$permuri);
            return $permuri;
        } else {
            return NULL;
        }
    }

    protected function getPropertyFilter(){
        return PropertyFactory::P_ITEMS;
    }
    protected static function getProperties($type_id, $keys = 'id', $search_area = PropertyFactory::P_ALL, $sort = 'group', $segment_id = NULL){
        $props = PropertyFactory::search($type_id, $search_area, $keys, $sort, 'parents', array(), $segment_id);
		$result = array();
		foreach ($props as $key => $pr){
			if ($pr['multiple'] != 1){
				$result[$key] = $pr;
			}
		}
        return $result;
    }
    public function getVariantProperties($keys = 'id', $search_area = PropertyFactory::P_ALL, $sort = 'group'){
        return Variant::getProperties($this['type_id'], $keys, $search_area, $sort, $this->segment_id);
    }

    /**
     * Перенос товара из типа в тип
     * @param int $type_id
     * @param array $errors
     * @return boolean
     */
    public function changeType($type_id, &$errors = array())
    {
        $old_url = $this->getUrl();
        $errors = array();
        $new_type = Type::getById($type_id);
        if ($this->getType()->getCatalog()['nested_in']) {
            throw new \LogicException('Перенос в типе с наследуемыми айтемами невозможен');
        } elseif (empty($new_type)) {
            $errors['type'] = 'not_found';
            return FALSE;
        } elseif ($new_type['allow_children']) {
            $errors['type'] = 'not_final_type';
            return FALSE;
        } elseif ($new_type->getCatalog()['id'] != $this->getType()->getCatalog()['id']) {
            $errors['type'] = 'another_catalog';
            return FALSE;
        }
        $variants = $this->getVariants();
        $variants_update_data = array();
        $variants_props2delete = array();
        foreach ($variants as $v) {
            $variants_update_data[$v['id']] = $v->changeTypeInner($new_type, $variants_props2delete[$v['id']]);
        }
        $item_update_data_by_segments = $this->changeTypeInner($new_type, $item_props2delete);
        // Перед переносом проверяем, можем ли мы сохранить свойства в новом типе
        foreach ($item_update_data_by_segments as $s_id => $item_update_data) {
            if (!empty($item_update_data)) {
                Item::checkProperties(
                    $type_id,
                    $item_update_data,
                    $errors,
                    $s_id,
                    NULL);
                if (!empty($errors)) {
                    break;
                }
            }
        }
        foreach ($variants_update_data as $v_id => $variant_data_by_segments) {
            foreach ($variant_data_by_segments as $s_id => $variants_data) {
                if (!empty($variants_data)) {
                    Variant::checkProperties(
                        $type_id,
                        $variants_data,
                        $errors,
                        $s_id,
                        NULL);
                    if (!empty($errors)) {
                        break;
                    }
                }
            }
        }
        // при успешной валидации переносим в новый тип
        if (empty($errors)){
            $title = !empty($this['title']) ? $this['title'] : '';
            // Удаляем значения свойств старого типа
            if (!empty($item_props2delete)){
                $this->deleteProps($item_props2delete);
            }
            foreach($variants as $v){
                if (!empty($variants_props2delete[$v['id']])){
                    $v->deleteProps($variants_props2delete[$v['id']]);
                }
            }
            $old_type_id = $this['type_id'];
            $old_type = $this->getType();
            $this->update(array('type_id' => $type_id), NULL, $errors);
            $catalog = $new_type->getCatalog();
            /** @TODO почему это не в Helper::preUpdate и Helper::onUpdate? */
			/** @TODO возможно не надо new_type_id, т.к. есть t_id и new_type_title => t_t */
            $upd_data = array(
                'type'        => \Models\Logger::LOG_TYPE_TRANSFER_ITEM,
                'entity_type' => 'item',
                'entity_id' => $this['id'],
                'additional_data' => array(
                    'new_type_id' => $type_id,
                    'new_type_title' => $new_type['title'],
                    'old_type_id' => $old_type_id,
                    'old_type_title' => $old_type['title'],
                    'c_id' => $catalog['id'],
                    't' => $title,
                    't_c' => $catalog['title'],
                    't_t' => $new_type['title'],
                    't_is_c' => $new_type->isCatalog(),
                )
            );
            \Models\Logger::add($upd_data);
            foreach($item_update_data_by_segments as $s_id => $item_update_data) {
                if (!empty($item_update_data)){
                    $this->updateValues($item_update_data, $errors, $s_id);
                }
            }
            foreach ($variants as $v){
                //реально в базу type_id не пишется, но надо в поле перепрописать
                $v->changeType($type_id);
            }
            foreach($variants_update_data as $v_id => $variant_data_by_segments){
                foreach ($variant_data_by_segments as $s_id => $variants_data) {
                    if (!empty($variants_data)) {
                        $variants[$v_id]->updateValues($variants_data, $errors, $s_id);
                    }
                }
            }
            if ($new_type->getCatalog()['allow_item_url']) {
                $new_url = $this->getUrl();
                PageRedirect::getInstance()->createItemAutoRedirect($this, $old_url, $new_url);
            }
            // Здесь у нас могут возникнуть сообщения об ошибках, которые уже не влияют на перенос товара
            // Поэтому сообщаем об успехе, несмотря на возвращенные ошибки
            return TRUE;
        } else {
            return FALSE;
        }
    }
    /**
     * Чистим варианты. При добавлении\удалении варианта из айтема
     * public нужен для вызова из класса варианта
     */
    public function clearVariantIds(){
        $this->variant_ids = array();
    }

    /**
     * Создание копии айтема в другом типе
     * @param int $type_id
     * @param array $errors
     * @param int $default_status
     * @return Item id нового айтема
     * @throws \ErrorException
     */
    public function copyItemToType($type_id, &$errors, $default_status = self::S_HIDE){
        $new_type = Type::getById($type_id);
        $current_type = $this->getType();
        if ($current_type['id'] != $new_type['id'] && $current_type->getCatalog()['nested_in']){
            throw new \LogicException('Перенос в типе с наследуемыми айтемами невозможен');
        } elseif ($new_type['allow_children']){
            $errors['type'] = 'not_final_type';
            return FALSE;
        } elseif ($new_type->getCatalog()['id'] != $this->getType()->getCatalog()['id']){
            $errors['type'] = 'another_catalog';
            return FALSE;
        }
        $new_item_id = static::create($type_id);//, self::S_HIDE, $this->makeTypeProperties($new_type), $errors);
        $new_item = static::getById($new_item_id);
        if (empty($new_item)){
            throw new \ErrorException('Не удалось создать айтем');
        }
        $new_item->update(array('status' => $default_status), $this->makeTypeProperties($new_type, $this->getType()), $errors);
        return $new_item;
    }
    /**
     * Собрать айтемы из вложенной категории
     * @param int $type_id
     * @return type
     */
    public function getChildren($type_id, $public_only = TRUE){
        return Search\CatalogSearch::factory($this->getType()->getCatalog()->getKey())
            ->setRules(array(
                Rules\Rule::make('parent_id', $this['id'])
            ))
            ->setPublicOnly($public_only)
            ->setTypeId($type_id)
            ->setSegment($this->segment_id)
            ->searchItems()->getSearch();
    }

    /**
     *
     * @param boolean $public_only
     * @return array
     */
    public function getChildrens($public_only = TRUE){
        return Search\CatalogSearch::factory($this->getType()->getCatalog()->getKey())
            ->setRules(array(
                Rules\Rule::make('parent_id', $this['id'])
            ))
            ->setPublicOnly($public_only)
            ->setSegment($this->segment_id)
            ->searchItems()->getSearch();
    }


    /**
     * проверить, имеет ли айтем хоть одного потомка
     * @param int $type_id
     * @return type
     */
    public function checkChildren(){
        $db = \App\Builder::getInstance()->getDB();
        $res = $db->query("SELECT `id` FROM `items` WHERE `parent_id`=?d", $this['id']);

        return ($res->count() > 0);
    }

    public function getParent(){
        return !empty($this['parent_id']) ? static::getbyId($this['parent_id'], $this['segment_id']) : NULL;
    }

    /**
     * Возвращает родительский айтем произвольного уровня
     * @param int $type_id
     * @return Item|null
     */
    public function getParentByTypeId($type_id) {
        return !empty($this->parent_by_type_ids[$type_id]) ? static::getById($this->parent_by_type_ids[$type_id], $this['segment_id']) : null;
    }
    public function getParentsByTypes(){
        return array_combine(array_flip($this->parent_by_type_ids), static::factory($this->parent_by_type_ids));
    }
    /******************************* ArrayAccess *****************************/
    public function offsetExists ($offset){
        if ($offset == 'collection_ids'){
            return !empty($this->data['collection_ids']) ? TRUE : FALSE;
        }else{
            return parent::offsetExists($offset);
        }
    }
    /**
     * @param string $offset
     * @return mixed
     * @throws \Exception
     */
    public function offsetGet ($offset){
        if ($offset == 'collection_ids'){
            return !empty($this->data['collection_ids']) ? array_reverse(explode(',', $this->data['collection_ids'])) : array();
        }else{
			//if($offset!='bed_number'&&$offset!='typerk'&&$offset!='area_all')
            return parent::offsetGet($offset);
        }
    }

    /******************************* работа с iItemDataProvider *****************************/
    /**
     * @var iItemDataProvider[]
     */
    static protected $dataProviders = array();
    /**
     * @var iItemDataProvider[]
     */
    static protected $dataProvidersByFields = array();

    /**
     * @static
     * @param iItemDataProvider $provider
     */
    public static function addDataProvider(iItemDataProvider $provider){
        static::$dataProviders[get_class($provider)] = $provider;
        foreach ($provider->fieldsList() as $field){
            static::$dataProvidersByFields[$field] = $provider;
        }
    }

    /**
     * @static
     * @param iItemDataProvider $provider
     */
    public static function delDataProvider(iItemDataProvider $provider){
        unset(static::$dataProviders[get_class($provider)]);
    }


    public static function getDataCache() {
        return self::$dataCache;
    }

    public static function getProperties1($type_id, $keys = 'id', $search_area = PropertyFactory::P_ALL, $sort = 'group', $segment_id = NULL){
        $props = PropertyFactory::search($type_id, $search_area, $keys, $sort, 'parents', array(), $segment_id);
        $result = array();
        foreach ($props as $key => $pr){
            if ($pr['multiple'] != 1){
                $result[$key] = $pr;
            }
        }
        return $result;
    }
}
