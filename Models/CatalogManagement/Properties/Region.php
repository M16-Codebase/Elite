<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 18.09.15
 * Time: 16:12
 */

namespace Models\CatalogManagement\Properties;


class Region extends Entity
{
    const VALUES_TYPE_ARRAY = true;
    const TYPE_NAME = 'region';

    public function getCompleteValue($v, $segment_id = NULL){
        if ($this['set']){
            return empty($v['value']) ? array() : \Models\Segments\Region::factory($v['value']);
        }
        return empty($v['value']) ? NULL : \Models\Segments\Region::getById($v['value']);
    }


    public static function prepareValues($data, &$errors){
        $data = parent::prepareValues($data, $errors);
        $data['values']['select_mode'] = CatalogPosition::SELECT_MODE_LIST;
        return $data;
    }

    public function getEntitiesList($segment_id = null, $public_only = true) {
        if ($this['values']['select_mode'] == CatalogPosition::SELECT_MODE_LIST) {
            return \Models\Segments\Region::getAll();
        } else {
            return array();
        }
    }
}