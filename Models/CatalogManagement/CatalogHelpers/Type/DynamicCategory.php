<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 01.07.15
 * Time: 19:45
 */

namespace Models\CatalogManagement\CatalogHelpers\Type;


use Models\CatalogManagement\RulesConstructor;
use Models\CatalogManagement\Type;

class DynamicCategory extends TypeHelper{
    protected static $i = NULL;
    const TABLE_FIELDS = 'item_types_dynamic_rules';
    protected static $fields_list = array(
        'rules',
        'complete_rules'
    );
    private static $cache = array();
    private $loadItemsQuery = array();

    public function fieldsList(){
        return static::$fields_list;
    }
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Type $type, $field, $segment_id = NULL){
        if (in_array($field, self::$fields_list)) {
            $catalog = $type->getCatalog();
            /** @TODO нужно ли бросать исключения при запросе данных у нединамических категорий? */
            if ($catalog['dynamic_for']){
                $this->loadData();
                if (!empty(self::$cache[$type['id']])) {
                    if (isset(self::$cache[$type['id']][$field])) {
                        return self::$cache[$type['id']][$field];
                    } else {
                        if ($field == 'complete_rules') {
                            return !empty(self::$cache[$type['id']]['rules'])
                                ? RulesConstructor::getInstance()->getSearchResult(self::$cache[$type['id']]['rules'], $catalog['dynamic_for'], 'item')
                                : NULL;
                        }
                    }
                }
            }
        }
        return NULL;
    }
    /**
     * уведомление, что данные для указанных Types попали в кеш данных и могут быть востребованы
     */
    public function onLoad(Type $type){
        if (!isset(self::$cache[$type['id']])){
            $this->loadItemsQuery[$type['id']] = $type['id'];
        }
    }

    private function loadData(){
        if (empty ($this->loadItemsQuery)){
            return;
        }
        $db = \App\Builder::getInstance()->getDB();
        $data = $db->query('
            SELECT `type_id`, `rule_id`, `rule`
            FROM `'.self::TABLE_FIELDS.'`
                WHERE `type_id` IN (?i)
            ',  $this->loadItemsQuery
        )->getCol(array('type_id', 'rule_id'), 'rule');
        if (!empty($data)){
            foreach($data as $type_id => $rules){
                self::$cache[$type_id] = array(
                    'rules' => array_map(function($v){return json_decode($v, TRUE);}, $rules)
                );
            }
        }
        $this->loadItemsQuery = array();
    }

    /**
     * @param Type $type
     * @param $rule
     * @param array $errors
     * @return bool|int
     * @throws \Exception
     * @throws \MysqlSimple\Exceptions\InvalidArgumentException
     */
    public function addDynamicRule(Type $type, $rule, &$errors = array()){
        $catalog = $type->getCatalog();
        if (!$catalog['dynamic_for']){
            throw new \LogicException("Невозможно задать правила поиска, type#${type['id']} не является динамической категорией");
        }
        if (RulesConstructor::getInstance()->validateDynamicRule($rule, $catalog['dynamic_for'], $errors)){
            $rule_id = $this->db->query('SELECT MAX(`rule_id`) AS `rule_id` FROM ?# WHERE `type_id` = ?d', self::TABLE_FIELDS, $type['id'])->getCell() + 1;
            $this->db->query('INSERT INTO ?# SET `type_id` = ?d, `rule_id` = ?d, `rule` = ?s', self::TABLE_FIELDS, $type['id'], $rule_id, json_encode($rule));
            self::$cache[$type['id']]['rules'][$rule_id] = $rule;
            unset(self::$cache[$type['id']]['complete_rules']);
            return $rule_id;
        }
        return FALSE;
    }

    /**
     * @param Type $type
     * @param $rule_id
     * @param $rule
     * @param array $errors
     * @return bool|int
     * @throws \Exception
     */
    public function editDynamicRule(Type $type, $rule_id, $rule, &$errors = array()){
        $catalog = $type->getCatalog();
        if (!$catalog['dynamic_for']){
            throw new \LogicException("Невозможно задать правила поиска, type#${type['id']} не является динамической категорией");
        } elseif (empty($type['rules'][$rule_id])){
            $errors['rule_id'] = 'not_found';
            return FALSE;
        }
        if (RulesConstructor::getInstance()->validateDynamicRule($rule, $catalog['dynamic_for'], $errors)){
            $this->db->query('UPDATE ?# SET `rule` = ?s WHERE  `type_id` = ?d AND `rule_id` = ?d', self::TABLE_FIELDS, json_encode($rule), $type['id'], $rule_id);
            self::$cache[$type['id']]['rules'][$rule_id] = $rule;
            unset(self::$cache[$type['id']]['complete_rules']);
            return $rule_id;
        }
        return FALSE;
    }

    /**
     * @param Type $type
     * @param int $rule_id
     * @param array $errors
     * @return FALSE|int|
     * @throws \Exception
     */
    public function deleteDynamicRule(Type $type, $rule_id, &$errors = array()){
        $catalog = $type->getCatalog();
        if (!$catalog['dynamic_for']){
            throw new \LogicException("Невозможно задать правила поиска, type#${type['id']} не является динамической категорией");
        }
        return $this->db->query('DELETE FROM ?# WHERE `type_id` = ?d AND `rule_id` = ?d', self::TABLE_FIELDS, $type['id'], $rule_id);
    }
}