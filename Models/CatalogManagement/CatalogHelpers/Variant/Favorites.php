<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 02.10.15
 * Time: 18:15
 */

namespace Models\CatalogManagement\CatalogHelpers\Variant;


use Models\CatalogManagement\Variant;

class Favorites extends VariantHelper
{
    protected static $i = null;

    protected static $fieldsList = array('in_favorites', 'favor_add_date');

    private $dataCache = array();

    public function get(Variant $v, $field){
        if (in_array($field, $this->fieldsList())) {
            $catalog = $v->getType()->getCatalog();
            if (empty($this->dataCache[$v['id']])) {
                $favor_data = \App\Builder::getInstance()->getAccount()->getFavoriteData($catalog['key']);
                $this->dataCache[$v['id']]['in_favorites'] = in_array($v['id'], $favor_data['entity_ids']);
                $this->dataCache[$v['id']]['favor_add_date'] = !empty($favor_data['dates'][$v['id']]) ? $favor_data['dates'][$v['id']] : null;
            }
            return $this->dataCache[$v['id']][$field];
        }
    }
}