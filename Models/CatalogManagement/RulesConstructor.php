<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 01.07.15
 * Time: 18:39
 *
 * Сборщик рулов для динамических категорий и пропертей-правил
 */

namespace Models\CatalogManagement;


use App\Configs\CatalogConfig;
use Models\CatalogManagement\Properties\Factory as PropertyFactory;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Rules\RuleAggregator;
use Models\Validator;

class RulesConstructor {
    private static $i = NULL;
    /**
     * Список ключей пропертей, которые не должны попадать в правила динамических категорий
     * @var array
     */
    private static $ignorePropList = array(
        \App\Configs\SphinxConfig::CATALOG_SEARCH_PROP_KEY,
        CatalogConfig::KEY_ITEM_REVIEW_COUNT,
        CatalogConfig::KEY_ITEM_NEW_REVIEW_COUNT,
        CatalogConfig::KEY_ITEM_QUESTION_COUNT,
        CatalogConfig::KEY_ITEM_NEW_QUESTION_COUNT
    );

    /**
     * @return RulesConstructor
     */
    public static function getInstance(){
        if (empty(self::$i)) {
            self::$i = new self();
        }
        return self::$i;
    }

    private function __construct(){}

    /**
     * @param array $search_params
     * @param string $catalog_key
     * @param string $entity_type
     * @return array - правила поиска array('catalog' => <ключ каталога>, 'type' => item/variant, 'rules' => RuleAggregator)
     * @throws \ErrorException
     */
    public function getSearchResult($search_params, $catalog_key, $entity_type){
        $catalog = Type::getByKey($catalog_key);
        if (empty($catalog)) {
            throw new \ErrorException("Catalog #${catalog_key} not found");
        }
        if (!in_array($entity_type, array('item', 'variant')) || $catalog['only_items'] && $entity_type == 'variant') {
            throw new \ErrorException("Недопустимое значение \$entity_type #${entity_type}" . ($entity_type == 'variant' ? " В каталоге #${catalog_key} не используются варианты" : ''));
        }
        $rules = array_map(array($this, 'makeRules'), $search_params);
        $rules = array_filter($rules);
        if (!empty($rules)){
            $rules = (count($rules) > 1) ? RuleAggregator::make(RuleAggregator::LOGIC_OR, $rules) : reset($rules);
        }
        return array(
            'catalog' => $catalog_key,
            'type' => $entity_type,
            'rules' => $rules
        );
    }

    /**
     * Преобразует json в набор рулов
     * @param $search_params
     * @return array|null
     * @throws \Exception
     */
    private function makeRules($search_params){
        $search_params = is_array($search_params) ? $search_params : json_decode($search_params, true);
        if (empty($search_params)){
            return NULL;
        }
        $rules = array();
        foreach($search_params as $field => $params){
            if (empty($rules[$field])){
                $rule = Rule::make($field);
                if ($field == 'type_id') {
                    if (empty($params['value'])) {
                        throw new \Exception('Empty values for type_id');
                    } else {
                        $type_ids = $this->getFinalTypesIds($params['value']);
                        $rule->setValue(!empty($type_ids) ? $type_ids : $params['value']);
                    }
                } else {
                    if (!empty($params['value'])) {
                        $rule->setValue($params['value'], !empty($params['type']) ? $params['type'] : NULL);
                    } elseif (!empty($params['min']) || !empty($params['max'])) {
                        if (!empty($params['max'])){
                            $rule->setMax($params['max']);
                        }
                        if (!empty($params['min'])){
                            $rule->setMax($params['min']);
                        }
                    } else {
                        throw new \LogicException("Incorrect search params for ${field}");
                    }
                }
                $rules[$field] = $rule;
            }
        }
        return !empty($rules) ? RuleAggregator::make(RuleAggregator::LOGIC_AND, $rules) : NULL;
    }

    /**
     * @param int|int[] $type_ids
     * @return int[]
     */
    private function getFinalTypesIds($type_ids){
        $type_ids = is_array($type_ids) ? $type_ids : array($type_ids);
        $types = Type::factory($type_ids);
        $result = array();
        if (!empty($types)) {
            foreach($types as $type){
                if ($type['allow_children']){
                    $ids = Type::getIds(array('parents' => $type['id'], 'allow_children' => 0));
                    $result = array_merge($result, $ids);
                } else {
                    $result[] = $type['id'];
                }
            }
            $result = array_unique($result);
        }
        return $result;
    }

    /**
     *
     * @param RuleAggregator $filter_rules
     * @param RuleAggregator $sort_rules
     * @return RuleAggregator
     */
    public static function setSortRules(RuleAggregator $filter_rules, RuleAggregator $sort_rules){
        return RuleAggregator::make(RuleAggregator::LOGIC_AND, array($filter_rules, $sort_rules));
    }

    /**
     * @param array $rule
     * @param array $errors
     * @return bool
     */
    public function validateDynamicRule($rule, $catalog_key, &$errors = array()){
        $rule = is_array($rule) ? $rule : json_decode($rule, true);
        $catalog = Type::getByKey($catalog_key);
        $type_ids = array();
        if (empty($rule['type_id'])) {
            $type_ids = array($catalog['id']);
        } else {
            if (empty($rule['type_id']['value'])){
                $errors['type_id']['value'] = Validator::ERR_MSG_EMPTY;
            } else {
                $types = Type::factory($rule['type_id']['value']);
                foreach($rule['type_id']['value'] as $type_id){
                    if (empty($types[$type_id])){
                        $errors['type_id']['value'][$type_id] = Validator::ERR_MSG_EMPTY;
                    } elseif ($types[$type_id]->getCatalog()['id'] != $catalog['id']) {
                        $errors['type_id']['value'][$type_id] = Validator::ERR_MSG_INCORRECT;
                    } else {
                        $type_ids[$type_id] = $type_id;
                    }
                }
            }
        }
        if (empty($errors)) {
            $props = $this->getAllowProperties($type_ids);
            foreach($rule as $field => $data){
                if ($field == 'type_id')
                    continue;

                if (empty($props[$field])){
                    $errors[$field] = Validator::ERR_MSG_EMPTY;
                } elseif (!$this->isNumericProp($props[$field]) && (!empty($data['min']) || !empty($data['max']))) {
                    $errors[$field] = Validator::ERR_MSG_INCORRECT;
                }
            }
        }
        return empty($errors);
    }

    /**
     * Проверка, является ли пропертя числовой
     * @param Properties\Property[] $props
     * @return bool
     */
    private function isNumericProp($props){
        $numeric = TRUE;
        foreach($props as $prop) {
            if (!in_array($prop['data_type'], array(Properties\Int::TYPE_NAME, Properties\Float::TYPE_NAME))) {
                $numeric = false;
            }
        }
        return $numeric;
    }

    public function getRulesData(Array $rules, Type $catalog){
        $usedProps = array();
        $usedTypes = array();
        $notFoundProps = array();
        if (!empty($rules)){
            $prop_keys = array();
            foreach($rules as $rule){
                foreach($rule as $field => $data){
                    if ($field == 'type_id') {
                        $usedTypes = array_merge($usedTypes, Type::getIds(array('parents' => $catalog['id'], 'ids' => is_array($data['value']) ? $data['value'] : array($data['value']))));
                    } else {
                        $prop_keys[$field] = $field;
                    }
                }
            }
            $usedTypes = array_unique($usedTypes);
            $usedTypes = !empty($usedTypes) ? Type::factory($usedTypes) : array();

            $usedProps = $this->getAllowProperties(!empty($usedTypes) ? array_keys($usedTypes) : $catalog['id']);
            $notFoundProps = array_diff($prop_keys, array_keys($usedProps));
        }
        return array(
            'types' => $usedTypes,
            'props' => $usedProps,
            'not_found_props' => $notFoundProps
        );
    }

    /**
     * Возвращает список пропертей, которые могут участвовать в фильтрах
     * @TODO не допускать одновременного присутствия свойств с одним ключом несовместимых типов (например string и enum)
     * @param int|int[] $type_ids
     * @return Properties\Property[]
     * @throws \Exception
     */
    public function getAllowProperties($type_ids){
        $properties = PropertyFactory::search(
            $type_ids,
            PropertyFactory::P_NOT_ENTITY | PropertyFactory::P_NOT_DEFAULT,
            'idByKey',
            'type_group',
            'parents',
            array(
                'not_key' => self::$ignorePropList
            )
        );
        // вычищаем несовместимые свойства
        foreach($properties as $key => $props){
            $first = reset($props);
            $is_enum = $first['data_type'] == Properties\Enum::TYPE_NAME;
            $is_flag = $first['data_type'] == Properties\Flag::TYPE_NAME;
            foreach($props as $prop){
                if ($prop['data_type'] == Properties\Enum::TYPE_NAME xor $is_enum){
                    unset($properties[$key]);
                    break;
                }
                if ($prop['data_type'] == Properties\Flag::TYPE_NAME xor $is_flag){
                    unset($properties[$key]);
                    break;
                }
            }
        }
        return $properties;
    }

    /**
     * Возвращает отфильтрованный список пропертей, по одной на ключ
     * @param array $propInIdByKey
     * @return Properties\Property[]
     */
    public static function getPropsList($propInIdByKey){
        $result = array();
        if (!empty($propInIdByKey)){
            foreach($propInIdByKey as $key => $props){
                $result[$key] = reset($props);
            }
        }
        return $result;
    }

}