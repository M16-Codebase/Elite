<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 25.06.15
 * Time: 17:05
 */

namespace Modules\Catalog;


use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Type as TypeEntity;

abstract class CatalogDynamic extends CatalogPublic{

    private function makeRules($search_params, &$rules = array()){
        if (empty($search_params)){
            return FALSE;
        }
        foreach($search_params as $r_params){
            foreach($r_params as $field => $params) {
                if (empty($rules[$field])){
                    $rule = Rule::make($field);
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
                    $rules[$field] = $rule;
                }
            }
        }
    }

    /**
     * @param TypeEntity $type
     * @return array
     */
    protected function getSearchRules(TypeEntity $type){
        $rules = array();
        $this->makeRules($type['rules'], $rules);
        $parents = $type->getParents();
        foreach($parents as $parent){
            if ($parent['id'] != TypeEntity::DEFAULT_TYPE_ID){
                $this->makeRules($parent['rules'], $rules);
            }
        }
        return $rules;
    }

    final public function viewItem(){
        return $this->notFound();
    }

}