<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 01.07.15
 * Time: 12:07
 *
 * Свойство параметры поиска каталога
 */

namespace Models\CatalogManagement\Properties;


use App\Configs\CatalogConfig;
use Models\CatalogManagement\Rules\RuleAggregator;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type;

class Rule extends Property {
    const TYPE_NAME = 'rule';
    protected static $values_fields = array('catalog', 'entity');
    /**
     * @var \Models\CatalogManagement\RulesConstructor
     */
    private $rules_constructor = NULL;

    protected function onLoad() {
        $this->rules_constructor = \Models\CatalogManagement\RulesConstructor::getInstance();
        return NULL;
    }

    /**
     * Набор рулов должен знать, из какого каталога ему брать объекты, и какие это должны быть объекты (айтемы/варианты)
     * @param array $data
     * @param array $e
     * @return array
     */
    protected static function checkData($id, &$data, &$e){
        if (empty($data['values']['catalog'])){
            $e['values[catalog]'] = 'empty';
        } else {
            $catalog = Type::getByKey($data['values']['catalog']);
            if (empty($catalog) || $data['values']['catalog'] == CatalogConfig::CONFIG_KEY) {
                $e['values[catalog]'] = \Models\Validator::ERR_MSG_INCORRECT;
            }
        }
        if (empty($data['values']['entity'])){
            $e['values[entity]'] = 'empty';
        } else {
            if (!in_array($data['values']['entity'], array('item', 'variant'))
                || !empty($catalog) && $data['values']['entity'] == 'variant' && $catalog['only_items']) {
                $errors['values[entity]'] = \Models\Validator::ERR_MSG_INCORRECT;
            }
        }
        return array();
    }

    /**
     * Проверка значения на соответствие типу данных
     * @param type $val
     * @return boolean
     */
    public function isValueFormatCorrect($val){
        if (parent::isValueFormatCorrect($val)){
            return \Models\CatalogManagement\RulesConstructor::getInstance()->validateDynamicRule($val, $this['values']['catalog'], $errors);
        } else {
            return FALSE;
        }
    }

    public function getCompleteValue($v, $segment_id = NULL){
        return $this->rules_constructor->getSearchResult($this['set'] ? $v['value'] : array($v['value']), $this['values']['catalog'], $this['values']['entity']);
    }

}