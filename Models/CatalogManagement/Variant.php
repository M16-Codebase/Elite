<?php
namespace Models\CatalogManagement;

use App\Configs\CatalogConfig;
use \Models\CatalogManagement\CatalogHelpers\Interfaces\iVariantDataProvider;
use Models\CatalogManagement\Properties\Factory AS PropertyFactory;
/**
 * Класс предметной области каталога (позиция каталога), служит для доступа и работы на чтение. Для особых манипуляций используется класс Catalog
 *
 */
class Variant extends CatalogPosition implements iOrderData{
    const MAX_REGISTRY_LENGHT = 500;
    /**таблица вариаций*/
    const TABLE_VARIANTS = 'variants';
    /**таблица целочисленных значений свойств вариаций*/
    const TABLE_PROP_INT = 'variants_properties_int';
    /**таблица дробных значений свойств вариаций*/
    const TABLE_PROP_FLOAT = 'variants_properties_float';
    /**таблица строковых значений свойств вариаций*/
    const TABLE_PROP_STRING = 'variants_properties_string';
    /**доп фильтр для свойств вариантов*/
    const PROPERTY_FILTER = PropertyFactory::P_VARIANTS;
    /**
     * Разрешенные параметры
     * @see Catalog::checkAllowed()
     */
    static protected $allowParams = array(
        'key',
        'item_id', // нужно для переноса варианта @TODO бесит
        'status',
        'last_update',
        'recreate_view',
        'position'
    );

    static protected $loadFields = array('id', 'key', 'item_id', 'status', 'time', 'position', 'last_update', 'recreate_view');
    /**
     * @var Item
     */
    protected $item = null;
    /**
     * @var type array
     */
    static protected $ids4Load = array();
	/**
     * Колекция уже созданных Variants, чтобы они не терялись
     * @var Variant[]
     */
    static protected $registry = array();
	/**
     * Колекция уже прочитанных данных. нужна для того чтобы можно было создать объекты быстро
     * @var array[] (data rows)
     */
    static protected $dataCache = array();
	/**
     * Имя основной таблицы
     */
    const TABLE = self::TABLE_VARIANTS;
    /**
     * название основного поля в таблицах self::TABLE_PROP_*
     */
    const TABLE_PROP_OBJ_ID_FIELD = 'variant_id';
    /**
     * Наименование поля, в пределах которого номер сортировки должен быть уникальным для каждой позиции
     */
    const UNIQUE_POSITION_IN_FIELD = 'item_id';
    /**
     * Самоопределение вариант это или айтем
     */
    const CATALOG_IDENTITY_KEY = 'variant';
    /**
     * Название
     */
    const ENTITY_TITLE_KEY = CatalogConfig::KEY_VARIANT_TITLE;
    /**
     * Удаление. Вызываем только из айтема
     * @param array $errors
     * @param bool $remove_from_db удалять ли из базы?
     * @return bool
     */
    public function _delete(&$errors = array(), $remove_from_db = false) {
        $item = $this->getItem();
        $result = parent::delete($errors, $remove_from_db);
        if ($result) {
            $item->recreateRanges();
        }
        return $result;
    }
    /**
     * Чтение данных из которых потом собираются наименования
     *
     * @param int[] $ids
     * @return type
     */
    protected static function cacheUpData($ids, $segment_id = NULL){
        // в кеш данных не нужно читать данные уже созданных объектов и данные которые есть в кеше
        $ids = static::calculateCachedIdsList($ids);
        $result = array();
        $itemIds = array();
        if (empty ($ids)){
            return array();
        }
        $ids = static::loadCachedData($ids);
        if (!empty($ids)){
            $result['main'] = \App\Builder::getInstance()->getDB()->query('
                SELECT `v`.`'.implode('`, `v`.`', self::$loadFields).'`, 
                    `items`.`type_id`, 
                    UNIX_TIMESTAMP(`v`.`time`) AS `timestamp`
                FROM   `'.self::TABLE_VARIANTS.'` AS `v`
                INNER JOIN `'.Item::TABLE_ITEMS.'` AS `items` ON (`items`.`id` = `v`.`item_id`)
                WHERE `v`.`id` IN (?i)
                ',
                $ids
            )->select('id');
            $result['properties'] = parent::getPropertiesFromDB($ids);
            foreach ($ids as $id) {
                if (empty($result['main'][$id])){
                    self::$dataCache[$id] = NULL;
                }else{
                    $item_id = $result['main'][$id]['item_id'];
                    $itemIds[$item_id] = $item_id;
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
            Item::prepare($itemIds);
        }
    }
    /**
     * Фабрика объектов
     * @param int[] $ids
     * @return Variant[]
     */
    public static function factory($ids, $segment_id = NULL){
        return parent::factory($ids, $segment_id);
    }

    /**
     * Создание варианта
     * Вызываем только из айтема
     * @param int $item_id
     * @param int $status
     * @param array $errors
     * @param array $propValues массив в формате (key свойства => характеристики значения[]), если передать NULL в качестве характеристик значения, то указанное свойство будет удалено
     * характеристики значения это массив со следующими ключами:
     * <UL>
     *      <LI>int    <B>val_id</B>
     *      <LI>string <B>value</B>
     *      <LI>mixed  <B>options</B>
     * </UL>
     * @param int $segment_id
     * @return int id варианта
     * @throws \LogicException
     */
    public static function _create($item_id, $status = self::S_TMP, &$errors = NULL, $propValues = array(), $segment_id = NULL){
        $item = Item::getById($item_id, $segment_id);
        if (empty($item)){
            throw new \LogicException('Невозможно создать вариант в данном айтеме. Айтем с id #'.$item_id.' не найден');
        }
        foreach (static::$dataProviders as $p){
            /* @var $p iVariantDataProvider */
            $p->preCreate($item_id, $errors, $propValues, $segment_id);
        }
        if (!empty($errors)){
            return FALSE;
        }
        static::checkProperties($item['type_id'], $propValues, $errors, $segment_id, NULL, $item_id);
        if (!empty($errors)){
            return false;
        }
        $db = \App\Builder::getInstance()->getDB();
        $max_position = $db->query('SELECT MAX(`position`) FROM `'.Variant::TABLE.'` WHERE `item_id`=?d', $item_id)->getCell();
        $variant_id = $db->query('INSERT INTO `'.Variant::TABLE_VARIANTS.'` SET `status`=?d, `item_id`=?d, `time`=NOW(), `position`=?d',  $status, $item_id, $max_position + 1);
        foreach (static::$dataProviders as $p){
            /* @var $p iVariantDataProvider */
            $p->onCreate($variant_id, $segment_id);
        }
        return $variant_id;
    }
    public static function checkProperties($type_id, &$propValues, &$errors, $segment_id = NULL, $id = NULL, $item_id = NULL, $max_recreate_view = Catalog::MAX_RECREATE_VIEWS_ON_UPDATE){
        return parent::_checkProperties($type_id, $propValues, $errors, $segment_id, $id, $item_id, $max_recreate_view);
    }
     /**
     * Возвращает конкретный объект
     * @param int $id
     * @return Variant
     */
    public static function getById($id, $segment_id = NULL){
         return parent::getById($id, $segment_id);
    }
    /**
     * Очищение кеша с фильтрами
     * final, т.к. чистится всё подряд (из разных каталогов)
     * @param int $variant_id
     * @param mixed $item_id
     * @param boolean $fromDB
     * @return array id вариантов которые почистили
     */
    final static public function clearCache($variant_id = null, $item_id = null, $fromDB = true){
        parent::_clearCache();
        if (!empty($variant_id)){
            if (array_key_exists($variant_id, self::$registry)){
                self::$registry[$variant_id]->save();
                unset(self::$registry[$variant_id]);
            }
            if (array_key_exists($variant_id, self::$dataCache)){
                unset(self::$dataCache[$variant_id]);
            }
            foreach (self::$dataProviders as $p){
                $p->onClearCache($variant_id);
            }
        }else{
            foreach (self::$registry as $en){
                $en->save();
            }
            self::$registry = array();
            foreach (self::$dataProviders as $p){
                $p->onClearCache();
            }
            self::$dataCache = array();
        }
        if ($fromDB){
            $db = \App\Builder::getInstance()->getDB();
            if (!empty($item_id)){
                $variant_id = $db->query('SELECT `id` FROM '.static::TABLE.' WHERE `item_id` IN (?i)', is_array($item_id) ? $item_id : array($item_id))->getCol('id', 'id');
            }elseif(!empty($variant_id) && !is_array($variant_id)){
                $variant_id = array($variant_id);
            }
            $db->query('DELETE FROM `'.static::TABLE_DATA_CACHE.'` WHERE `type` = "'.static::CATALOG_IDENTITY_KEY.'"{ AND `id` IN (?i)}', !empty($variant_id) ? $variant_id : $db->skipIt());
        }
		static::saveCacheData();
        return $variant_id;
    }
    /**
     * Проверка на уникальность значения свойства для элемента
     * @param int $id
     * @param string $prop_key
     * @param string $value
     * @param int $segment_id
     * @return bool TRUE if value is unique
     */
    public static function checkUniqueValue($prop_key, $value, $segment_id = NULL, $id = NULL, $type_id = NULL){
        if (!empty($id)){
            $variant = static::getById($id, $segment_id);
            $propertyList = $variant->getPropertyList('key');
        }elseif(!empty($type_id)){
            $propertyList = PropertyFactory::search($type_id, PropertyFactory::P_VARIANTS, 'key');
        }else{
            throw new \LogicException('Должен передаваться либо id элемента, либо id типа');
        }
        if (!isset ($propertyList[$prop_key]))
            throw new \Exception('incorrect property key "'.$prop_key.'" for variant#'.$id);
        $property = $propertyList[$prop_key];
        $db=\App\Builder::getInstance()->getDB();
        return !$db->query('
            SELECT 1
            FROM `'.$property['table'].'` AS `p`
            INNER JOIN `' . self::TABLE . '` AS `v` ON (
                `v`.`id`=`p`.`variant_id` AND 
                `v`.`status` NOT IN (?l)
            )
            INNER JOIN `' . Item::TABLE . '` `i` ON (
                `i`.`id` = `v`.`item_id` AND 
                `i`.`status` NOT IN (?l)
            )
            WHERE `p`.`property_id` = ?d AND
                `p`.`value` IN (?l){ AND
                `p`.`variant_id` != ?d}{ AND
                `p`.`segment_id` = ?d}
            ', 
                array(Variant::S_TMP, Variant::S_DELETE), 
                array(Item::S_TMP, Item::S_DELETE),
                $property['id'], 
                is_array($value) ? $value : array($value), 
                !is_null($id) ? $id : $db->skipIt(),
                $property['segment'] ? $segment_id : $db->skipIt()
            )->getCell();
    }
    /**
     * Отчистка базы данных от старых хвостов.
     *
     * @return bool
     */
    public static function cleanup(){
        $db = \App\Builder::getInstance()->getDB();
        foreach (array(Variant::TABLE_PROP_INT, Variant::TABLE_PROP_STRING, Variant::TABLE_PROP_FLOAT) as $table){
            // Удаляются те записи, которые никуда вообще не ссылаются или ссылаются на удаленные Items или Variants
            $db->query('
                DELETE `pv` FROM ?# AS `pv`
                    LEFT JOIN ?# AS `v` ON (`pv`.`variant_id` = `v`.`id`)
                    LEFT JOIN ?# AS `i` ON (`v`.`item_id` = `i`.`id`)
                    WHERE
                        `i`.`status` = ? OR
                        `v`.`status` = ? OR
                        `v`.`id` IS NULL OR
                        `i`.`id` IS NULL',
                $table, Variant::TABLE, Item::TABLE, Item::S_DELETE, Variant::S_DELETE
            );  
        }
        // Удаляются ненужные варианты
        $db->query('
            DELETE `v` FROM ?# AS `v`
                LEFT JOIN ?# AS `i` ON (`v`.`item_id` = `i`.`id`)
                WHERE
                    `i`.`status` = ? OR
                    `i`.`id` IS NULL',
            Variant::TABLE, Item::TABLE, Item::S_DELETE
        );
        foreach (static::$dataProviders as $p){
            /* @var $p iVariantDataProvider */
            $p->onCleanup();
        }
		return true;
    }
    protected function checkPropertyToRecreateView($property){
        return $property instanceof Properties\View && $property['multiple'] == 1;
    }

    /**
     * @return Properties\Property[]
     * @throws \Exception
     */
    protected function getPropListsForRecreateViews(){
        $properties = PropertyFactory::search($this->getType()->getId(), PropertyFactory::P_ALL, 'key', 'position', 'parents', array(), $this->segment_id);
//        $prop = !empty($properties[CatalogConfig::KEY_VARIANT_TITLE_SEARCH]) ? $properties[CatalogConfig::KEY_VARIANT_TITLE_SEARCH] : NULL;
        if (!empty($prop)){
            unset($properties[CatalogConfig::KEY_VARIANT_TITLE_SEARCH]);
            $properties[CatalogConfig::KEY_VARIANT_TITLE_SEARCH] = $prop;//ставим в конец @TODO временная заплатка
        }
        return $properties;
    }
    
    protected function validateProperties(&$propValues, &$errors, $segment_id = NULL){
        return static::checkProperties($this['type_id'], $propValues, $errors, $segment_id, $this->id, $this['item_id']);
    }
    public static function getProperties($type_id, $keys = 'id', $search_area = PropertyFactory::P_ALL, $sort = 'group', $segment_id = NULL){
        $props = PropertyFactory::search($type_id, $search_area, $keys, $sort, 'parents', array(), $segment_id);
		$result = array();
		foreach ($props as $key => $pr){
			if ($pr['multiple'] == 1){
				$result[$key] = $pr;
			}
		}
        return $result;
    }
    /**
     * Возврашает Item к которой привязан данный Variant
     * @return Item
     */
    public function getItem(){
        if (empty($this->item))
            $this->item = Item::getById($this->data['item_id'], $this->segment_id);
        return $this->item;
    }
    public function getType(){
        return $this->getItem()->getType();
    }
    /**
     * @return bool
     */
    public function isNull(){
        return empty($this->id);
    }

    /**
     * URL варианта
     * Если в настройках каталога указано, что сущности не имеют публичного урла — возвращает NULL
     * @param int|null $segment_id
     * @return null|string
     * @throws \Exception
     */
    public function getUrl($segment_id = NULL){
        if ($this->getItem()->getType()->getCatalog()['allow_item_url']){
            return CatalogConfig::getVariantUrl($this, $segment_id);
        } else {
            return NULL;
        }
    }
    /**
     * Отдаём данные для заказа
     * @return type
     */
    public function getDataForOrder(){
        if (!isset($this['order_data'])){
            throw new \LogicException('Хелпер Variant\OrderData либо не используется, либо отдает неверные данные.');
        }
        return $this['order_data'];
    }
    protected function formatPropertyValues($properties){
        $props = parent::formatPropertyValues($properties);
        $item_props = $this->getItem()->getPropertyValues();
		return array_merge($props, $item_props);
	}

    /**
     * @param Item $new_item
     * @param array $errors
     * @return bool
     */
    public function changeItem(Item $new_item, &$errors = array()){
        $old_item = $this->getItem();
        if ($this['item_id'] == $new_item['id']){
            $errors['item_id'] = 'not_changed';
            return FALSE;
        }
        if ($new_item['type_id'] != $old_item['type_id']){
            if ($old_item->getType()->getCatalog()['nested_in']){
                throw new \LogicException('Перенос варианта в другой айтем в типе с наследуемыми айтемами возможен только в пределах одного типа');
            }
            $update_data_by_segments = $this->changeTypeInner($new_item->getType(), $pros2delete);
        }
        $title = !empty($this[CatalogConfig::KEY_VARIANT_TITLE]) ? $this[CatalogConfig::KEY_VARIANT_TITLE] : '';
        if (!empty($pros2delete)){
            $this->deleteProps($pros2delete);
        }
        $this->updateParams(array('item_id' => $new_item['id']), $errors);
        $this->save();
        $type = $new_item->getType();
        $catalog = $type->getCatalog();
        $upd_data = array(
            'type'        => \Models\Logger::LOG_TYPE_TRANSFER_VARIANT,
            'entity_type' => 'variant',
            'entity_id' => $this['id'],
            'additional_data' => array(
                'new_item_id' => $new_item['id'],
                'new_item_title' => $new_item['title'],
                'old_item_id' => $old_item['id'],
                'old_item_title' => $old_item['title'],
                'c_id' => $catalog['id'],
                't' => $title,
                't_c' => $catalog['title'],
                't_t' => $type['title'],
                't_is_c' => $type->isCatalog(),
            )
        );
        \Models\Logger::add($upd_data);
        if (!empty($update_data_by_segments)){
            Variant::clearCache($this['id']);
            $variant = Variant::getById($this['id']);
            foreach($update_data_by_segments as $s_id => $update_data){
                if (!empty($update_data)){
                    $variant->update(array(), $update_data, $errors, $s_id);
                }
            }
        }
        $this->item = $new_item;
        $new_item->clearVariantIds();
        return empty($errors);
    }
    /**
     * id категории варианта не хранится в базе (берется у айтема), но при смене типа айтема, надо и у варианта поле менять.
     * @param type $new_type_id
     */
    public function changeType($new_type_id){
        $this->data['type_id'] = $new_type_id;
        $this->need_save['cache'] = TRUE;
        $this->prepareCache();//в кэш тоже надо прописать
    }

    /**
     * Копирование варианта внутри айтема
     * @param array $errors
     * @return int
     */
    public function copy(&$errors = array()){
        $item = $this->getItem();
        $variant_id = $item->createVariant(self::S_TMP, array(), $errors);
        $new_variant = static::getById($variant_id);
        $new_variant->updateValues($this->makeTypeProperties($item->getType()), $errors);
        return $variant_id;
    }
    
    /******************************* ArrayAccess *****************************/

    public function offsetExists ($offset){
        if ($offset == 'item'){
            return TRUE;
        }elseif ($offset == 'collection_ids'){
            return !empty($this->data['collection_ids']) ? TRUE : FALSE;
        }else{
            return parent::offsetExists($offset);
        }
    }

    /**
     * @param mixed $offset
     * @return Item|mixed
     */
    public function offsetGet ($offset){
        if ($offset == 'item'){
            return $this->getItem();
        }elseif ($offset == 'collection_ids'){
            return !empty($this->data['collection_ids']) ? array_reverse(explode(',', $this->data['collection_ids'])) : array();
        }else{
            return parent::offsetGet($offset);
        }
    }

    /******************************* работа с iVariantDataProvider *****************************/
    static protected $dataProviders = array();
    static protected $dataProvidersByFields = array();

    /**
     * @static
     * @param iVariantDataProvider $provider
     */
    public static function addDataProvider(iVariantDataProvider $provider){
        static::$dataProviders[get_class($provider)] = $provider;
        foreach ($provider->fieldsList() as $field){
            static::$dataProvidersByFields[$field] = $provider;
        }
    }

    /**
     * @static
     * @param iVariantDataProvider $provider
     */
    public static function delDataProvider(iVariantDataProvider $provider){
        unset(static::$dataProviders[get_class($provider)]);
    }
}