<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 02.10.15
 * Time: 18:11
 */

namespace Models\CatalogManagement\CatalogHelpers\Item;


use Models\CatalogManagement\Item;

class Favorites extends ItemHelper
{
    protected static $i = null;

    protected static $fieldsList = array('in_favorites', 'favor_add_date');

    private $dataCache = array();

    public function get(Item $i, $field){
        if (in_array($field, $this->fieldsList())) {
            $catalog = $i->getType()->getCatalog();
            if ($catalog['only_items']) {
                if (empty($this->dataCache[$i['id']])) {
                    $favor_data = \App\Builder::getInstance()->getAccount()->getFavoriteData($catalog['key']);
                    $this->dataCache[$i['id']]['in_favorites'] = in_array($i['id'], $favor_data['entity_ids']);
                    $this->dataCache[$i['id']]['favor_add_date'] = !empty($favor_data['dates'][$i['id']]) ? $favor_data['dates'][$i['id']] : null;
                }
                return $this->dataCache[$i['id']][$field];
            }
            return false;
        }
    }
}