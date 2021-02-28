<?php
namespace Models\CatalogManagement\CatalogHelpers\OrderItem;

use Models\CatalogManagement\Variant;
/**
 * Для удобства делаем возможность запрашивать некоторые свойства по другим ключам
 *
 * @author olya
 */
class ShiftProperties extends OrderItemHelper{
    protected static $fieldsList = array('title', 'entity_id');
    public function get(Variant $v, $field){
        if ($field == 'title'){
            return $v[\App\Configs\OrderConfig::KEY_POSITION_TITLE];
        }elseif($field == 'entity_id'){
            return $v['properties'][\App\Configs\OrderConfig::KEY_POSITION_ENTITY]['value'];
        }
        return NULL;
    }
}
