<?php
/**
 * Переброска свойств поиска в конечные типы кустика
 * Здесь же пока что хранятся все механизмы для поиска по кустику
 *
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 17.08.15
 * Time: 14:22
 *
 */

namespace Models\CatalogManagement\CatalogHelpers\Property;


use App\Configs\CatalogConfig;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Properties\Factory as PropertyFactory;
use Models\CatalogManagement\Properties\Property;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type;

class NestedInSearchProps extends PropertyHelper
{
    protected static $i = null;

    private $transfer_ids = array();

    private $sync_params = array('set');

    public function onUpdate(Property $property){
        if (!empty($this->transfer_ids[$property['id']])) {
            $type = $property->getType();
            $final_types = $this->getFinalTypes($type);
            $final_types_ids = array_keys($final_types);
            if (!empty($final_types_ids)) {
                if (!empty($property['filter_visible'])) {
                    $this->createSearchProp($property, $final_types_ids);
                    if ($property['set'] == 0 && $this->transfer_ids[$property['id']]['set'] == 1) {
                        // При смене множественности на ноль нам нужно перебросить значения заново
                        $this->transferPropValues($property);
                    }
                } else {
                    $this->deleteSearchProps($property, $final_types_ids);
                }
            }
        }
        unset($this->transfer_ids[$property['id']]);
    }

    public function preUpdate(Property $property, &$params, &$errors){
        $type = $property->getType();
        $catalog = $type->getCatalog();
        if ($catalog['id'] == $type['id']) { // Проперти корневого типа не нужно перебрасывать
            return;
        }
        if ($catalog['nested_in'] && !$type['nested_in_final']) {
            if (array_key_exists('filter_visible', $params) && (empty($property['filter_visible']) xor empty($params['filter_visible']))) {
                $this->transfer_ids[$property['id']] = $property->asArray();
            } elseif ($property['filter_visible']) {
                // Пока тупо обновляем поисковые свойства при каждом обновлении
                $this->transfer_ids[$property['id']] = $property->asArray();
//                foreach($this->sync_params as $param_key) {
//                    if (array_key_exists($param_key, $params) && $property[$param_key] != $params[$param_key]) {
//                        $this->transfer_ids[$property['id']] = $property->asArray();
//                        break;
//                    }
//                }
            }
        }
    }

    /**
     * Возвращает список пропертей поиска родительских категорий
     * @param Type $type — исходный тип
     * @param Type $end_type — исходный тип
     * @return Property[]
     * @throws \Exception
     */
    private function getTypeParentSearchProps(Type $type, $end_type = null) {
        $catalog = $type->getCatalog();
        if (!$catalog['nested_in']) {
            throw new \LogicException('Копирование свойств поиска допустимо только для кустика');
        }
        $parent_types = $this->getParentTypes($type, $end_type);
        return !empty($parent_types)
            ? PropertyFactory::search(
                array_keys($parent_types),
                PropertyFactory::P_ALL,
                'id',
                'type_group',
                'self',
                array('filter_visible' => 7))
            : array();
    }

    /**
     * @param Type $type
     * @throws \ErrorException
     */
    public function checkTypeSearchProps(Type $type) {
        $search_props = $this->getTypeParentSearchProps($type);
        if (empty($search_props)) {
            return;
        }
        if ($type['nested_in_final']) {
            // Создаем свойства
            foreach($search_props as $s_prop) {
                $this->createSearchProp($s_prop, array($type['id']));
            }
        } else {
            // удаляем
            $prop_keys = array();
            foreach($search_props as $s_prop) {
                $prop_keys[] = $this->getPropKey($s_prop);
            }
            $search_prop_ids = PropertyFactory::searchIds($type['id'], PropertyFactory::P_ALL, 'type_group', 'self', array('key' => $prop_keys));
            foreach($search_prop_ids as $p_id) {
                Property::delete($p_id);
            }
        }
    }

    /**
     * Создает свойство поиска в конечных кустистых категориях
     * @param Property $property
     * @param array $create_type_ids
     * @return int[]
     * @throws \ErrorException
     * @throws \Exception
     */
    private function createSearchProp(Property $property, array $create_type_ids) {
        $prop_key = $this->getPropKey($property);
        $clone_prop_params = $property->asArray();
        unset($clone_prop_params['id']);
        $clone_prop_params['key'] = $prop_key;
        $clone_prop_params['title'] = $property['title'] . ' (' . $property->getType()['title'] . ')';
        $clone_prop_params['origin_type_id'] = $property['type_id'];
        // Свойство используется только в поиске, заполняется автоматически, поэтому скрываем его отовсюду
        $clone_prop_params['fixed'] = Property::FIXED_HIDE;
        $clone_prop_params['visible'] = NULL;
        // Проверяем существование созданных пропертей в конечных категориях
        $props = PropertyFactory::search($create_type_ids, PropertyFactory::P_ALL, 'id', 'type_group', 'self', array('key' => $prop_key));
        $type_map = array();
        if (!empty($props)) {
            foreach($props as $p) {
                $type_map[$p['type_id']] = $p['id'];
            }
        }
        $created_type_ids = array(); // Сюда собираем список айдишников типов, в которых были созданы проперти
        foreach($create_type_ids as $type_id) {
            // Пропускаем, если пропертя уже создана
            $clone_prop_params['type_id'] = $type_id;
            if (!empty($type_map[$type_id])) {
                $p = $props[$type_map[$type_id]];
            } else {
                $created_type_ids[$type_id] = $type_id;
                $p_id = Property::create($clone_prop_params, $e);
                if (!empty($e)) {
                    throw new \ErrorException('Не удалось создать свойство ' . var_export($e, true));
                }
                $p = PropertyFactory::getById($p_id);
            }
            if (!empty($p)) {
                $p->update($clone_prop_params, $e);
                if (!empty($e)) {
                    throw new \ErrorException('Не удалось отредактировать свойство ' . var_export($e, true));
                }
            }
        }
        $this->transferPropValues($property);
        return $created_type_ids;
    }

    /**
     * Удаление проброшенных копий проперти
     * @param Property $property
     * @param array $type_ids
     * @return bool|int
     */
    private function deleteSearchProps(Property $property, array $type_ids) {
        if (!CatalogConfig::DELETE_ON_PROP_DISABLE_SEARCH) {
            return false;
        }
        $prop_key = $this->getPropKey($property);
        $prop_ids = PropertyFactory::searchIds($type_ids, PropertyFactory::P_ALL, 'type_group', 'self', array('key' => $prop_key));
        foreach ($prop_ids as $p_id) {
            Property::delete($p_id);
        }
        return count($prop_ids);
    }

    /**
     * Ключ проперти поиска
     * @param Property $property
     * @return string
     */
    private function getPropKey(Property $property) {
        return $property->getType()['key'] . '_' . $property['key'];
    }

    /**
     * Возвращает список конечных категорий кустика относительно указанного типа
     * @param Type $type
     * @return Type[]
     * @throws \Exception
     */
    private function getFinalTypes(Type $type) {
        $catalog = $type->getCatalog();
        $final_types = Type::search(array('nested_in_final' => 1, 'parents' => $catalog['id']));
        if ($type['id'] == $catalog['id']) {
            // Для каталога возвращаем все конечные типы
            return $final_types;
        }
        // Для остальных - отсеиваем типы, не имеющие связи с $type
        $result = array();
        $type->getParents();
        foreach ($final_types as $id => $f_type) {
            if (!$f_type['nested_in']) {
                continue;
            }
            $parent = Type::getById($f_type['nested_in']);
            while ($parent['id'] != $type['id']
                    && $parent['id'] != $catalog['id']
                    && $parent['nested_in']) {
                $parent = Type::getById($parent['nested_in']);
            }
            if ($parent['id'] == $type['id']) {
                $result[$id] = $f_type;
            }
        }
        return $result;
    }

    /**
     * Возвращает список кутегорий, от айтемов которого наследуется айтем указанной категории кустика
     * @param Type $type — категория, родителей которых мы ищем
     * @param Type $end_type — категория, на которой мы хотим прервать поиск (если не указана — возвращается вся ветка)
     * @return Type[]
     * @throws \Exception
     */
    private function getParentTypes(Type $type, $end_type = null){
        $catalog = $type->getCatalog();
        if (!$type['nested_in']) {
            return array();
        }
        if (!empty($end_type) && $end_type['id'] == $type['id']) {
            return array();
        }
        $parent = Type::getById($type['nested_in'], $type['segment_id']);
        $result = array();
        while (!empty($parent) && $parent['id'] != $catalog['id'] && (empty($end_type) || $end_type['id'] != $parent['id'])){
            $result[$parent['id']] = $parent;
            $parent = $parent['nested_in'] ? Type::getById($parent['nested_in'], $type['segment_id']) : NULL;
        }
        if (!empty($end_type)) {
            $result[$end_type['id']] = $end_type;
        }
        return $result;
    }

    /**
     * Перенос поисковых значений в конечный тип
     * Выполняем прямым запросом, поскольку могут быть большие объемы данных
     * @param Property $property
     * @param Item|null $item
     * @throws \Exception
     */
    public function transferPropValues(Property $property, $item = null, $segment_id = null) {
        $type = $property->getType();
        $catalog = $type->getCatalog();
        $rules = array(
            'type_id' => Rule::make('type_id')->setValue($type['id'])
        );
        if (!empty($item)) {
            $rules['id'] = Rule::make('id')->setValue($item['id']);
        }
        $item_ids = CatalogSearch::factory($catalog['key'])
            ->setPublicOnly(false)
            ->enableTotalCount(false)
            ->setRules($rules)
            ->searchItemIds()
            ->getItemIds();
        $final_types = $this->getFinalTypes($type);
        $child_ids = !empty($item_ids) ? $this->getItemIdsList($item_ids, $final_types) : array();
        $prop_by_type = $this->getPropsByTypes($catalog, $property);
        $prop_table = $property->getTable();
        $values = !empty($item)
            ? $this->getPropertyValuesForItem($property, $item, $segment_id)
            : $this->getPropertyValuesFromDb($property);
        $delete_values = array();
        $insert_values = array();
        foreach($child_ids as $parent_id => $children_by_type) {
            foreach ($children_by_type as $type_id => $item_ids) {
                if (empty($prop_by_type[$type_id])) {
                    continue;
                }
                $delete_values[] = '(`property_id` = ' . $this->db->escape_int($prop_by_type[$type_id]['id']) . ' AND `item_id` IN (' . implode(', ', $item_ids) . '))';
                if (!empty($values[$parent_id])) {
                    foreach($item_ids as $i_id){
                        foreach($values[$parent_id] as $s_id => $vals){
                            foreach($vals as $val) {
                                if (is_null($val)) {
                                    continue;
                                }
                                $insert_values[] = '(' . $this->db->escape_int($i_id) . ', '
                                    . $this->db->escape_int($prop_by_type[$type_id]['id']) . ', '
                                    . $this->db->escape_value($val['value']) . ', '
                                    . $this->db->escape_value(!empty($s_id) ? $s_id : NULL) . ', '
                                    . $this->db->escape_value($val['position']) . ')';
                            }
                        }
                    }
                }
            }
        }
        if (!empty($delete_values)) {
            $this->db->query('DELETE FROM ?# WHERE ' . implode(' OR ', $delete_values), $prop_table);
        }
        if (!empty($insert_values)) {
            $this->db->query('INSERT INTO ?# (`item_id`, `property_id`, `value`, `segment_id`, `position`) VALUES ' . implode(', ', $insert_values), $prop_table);
        }
        Item::clearCache(null, array_keys($final_types), true);
    }

    /**
     * @param Property $property
     * @return mixed[][][]
     */
    private function getPropertyValuesFromDb(Property $property) {
        $prop_table = $property->getTable();
        return $this->db->query('SELECT `id`,
                                          `item_id`,
                                          `property_id`,
                                          `value`,
                                          IF(`segment_id` IS NULL, 0, `segment_id`) AS `segment_id`,
                                          `position`
                                    FROM ?#
                                    WHERE `property_id` = ?d',
            $prop_table,
            $property['id'])->select('item_id', 'segment_id', 'id');
    }

    /**
     * @param Property $property
     * @param Item $item
     * @param int|null $segment_id
     * @return mixed[][][]
     */
    private function getPropertyValuesForItem(Property $property, Item $item, $segment_id = null) {
        $segment_id = !empty($segment_id) ? $segment_id : 0;
        $result = array();
        $segment_props = $item->getSegmentProperties($segment_id);
        if (!empty($segment_props[$property['key']]['val_id'])) {
            if (!is_array($segment_props[$property['key']]['val_id'])) {
                $result = array($segment_props[$property['key']]['val_id'] => array('value' => $segment_props[$property['key']]['value'], 'position' => NULL));
            } else {
                $result = array();
                foreach($segment_props[$property['key']]['val_id'] as $key => $val_id) {
                    $result[$val_id] = array(
                        'value' => $segment_props[$property['key']]['value'][$key],
                        'position' => !empty($segment_props[$property['key']]['position'][$key]) ? $segment_props[$property['key']]['position'][$key] : NULL
                    );
                }
            }
        }
        return array($item['id'] => array($segment_id => $result));
    }

    /**
     * Копирование значений пропертей поиска
     * @param Item $item
     * @return bool
     * @throws \Exception
     */
    public function copyValuesIntoNewItem(Item $item) {
        $type = $item->getType();
        $catalog = $type->getCatalog();
        if (!$type['nested_in'] || !$type['nested_in_final']) {
            return false;
        }
        $parent_search_props = $this->getTypeParentSearchProps($type);
        if (empty($parent_search_props)) {
            return false;
        }
        $prop_ids_map = array(); // карта соответсвия исходной проперти конечной
        $prop_serach_keys = array();
        $props_by_type = array();
        foreach($parent_search_props as $prop) {
            $props_by_type[$prop['type_id']][$prop['id']] = $prop;
            $prop_serach_keys[] = $this->getPropKey($prop);
        }
        $final_props = PropertyFactory::search($type['id'], PropertyFactory::P_ALL, 'key', 'type_group', 'self', array('key' => $prop_serach_keys));
        if (empty($final_props)) {
            return false;
        }
        foreach ($parent_search_props as $prop) {
            $key = $this->getPropKey($prop);
            if (!empty($final_props[$key])) {
                $prop_ids_map[$prop['id']] = $final_props[$key]['id'];
            }
        }
        $parent_items = array();
        $parent = Item::getById($item['parent_id']);
        do {
            if (!empty($props_by_type[$parent['type_id']])) {
                $parent_items[$parent['id']] = $parent;
            }
            $parent = Item::getById($parent['parent_id']);
        } while (!empty($parent) && $parent['id'] != $catalog['id']);
        $copy_values_by_table = array();
        foreach ($parent_items as $parent) {
            foreach ($props_by_type[$parent['type_id']] as $prop) {
                if (empty($prop_ids_map[$prop['id']])) {
                    continue;
                }
                $table = $prop->getTable();
                $values = $this->db->query('SELECT `id`, `value`, `segment_id`, `position` FROM ?# WHERE `property_id` = ?d AND `item_id` = ?d', $table, $prop['id'], $parent['id'])->select('id');
                if (!empty($values)) {
                    foreach($values as $id => $v) {
                        $copy_values_by_table[$table][$id] = '('
                            . $this->db->escape_int($item['id']) . ', '
                            . $this->db->escape_int($prop_ids_map[$prop['id']]) . ', '
                            . $this->db->escape_value($v['value']) . ', '
                            . $this->db->escape_value($v['segment_id']) . ', '
                            . $this->db->escape_value($v['position']) . ')';
                    }
                }
            }
        }
        if (!empty($copy_values_by_table)) {
            foreach($copy_values_by_table as $table => $values) {
                if (empty($values)) {
                    continue;
                }
                $this->db->query('INSERT INTO ?# (`item_id`, `property_id`, `value`, `segment_id`, `position`) VALUES ' . implode(', ', $values), $table);
            }
        }
        return true;
    }

    /**
     * Возвращает список id айтемов конечных типов по id айтемов исходной проперти и категориям
     * @param array $parent_ids — id айтемов, чьи дети нам нужны
     * @param Type[] $final_types — конечные категории
     * @param int|null $top_item_id — id айтема верхнего уровня
     * @param array $result
     * @return int[][][]
     */
    private function getItemIdsList($parent_ids, $final_types, $top_item_id = null, &$result = array()) {
        $ids_list = $this->db->query('SELECT `id`, `parent_id`, `type_id` FROM ?# WHERE `parent_id` IN (?i)', Item::TABLE, $parent_ids)->getCol(array('parent_id', 'type_id', 'id'), 'id');
        foreach($ids_list as $item_id => $child_by_type) {
            $result_item_id = !empty($top_item_id) ? $top_item_id : $item_id;
            foreach($child_by_type as $type_id => $child_ids) {
                if (!empty($final_types[$type_id])) {
                    if (empty($result[$result_item_id][$type_id])) {
                        $result[$result_item_id][$type_id] = $child_ids;
                    } else {
                        $result[$result_item_id][$type_id] = array_merge($result[$result_item_id][$type_id], $child_ids);
                    }
                } else {
                    $this->getItemIdsList($child_ids, $final_types, $result_item_id, $result);
                }
            }
        }
        return $result;
    }

    /**
     * Возвращает массив копий проперти поиска по типам
     * @param Type $catalog
     * @param Property $property
     * @return Property[]
     */
    private function getPropsByTypes($catalog, $property) {
        $props = PropertyFactory::search($catalog['id'], PropertyFactory::P_ALL, 'id', 'type_group', 'children', array('key' => $this->getPropKey($property)));
        $result = array();
        foreach($props as $p) {
            $result[$p['type_id']] = $p;
        }
        return $result;
    }

}