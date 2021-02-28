<?php
/**
 * Тип данных - Дата
 *
 * @author olga
 */
namespace Models\CatalogManagement\Properties;
use Models\CatalogManagement\Variant as VariantEntity;
use Models\CatalogManagement\Item as ItemEntity;
class DateTime extends Date {
    const TYPE_NAME = 'dateTime';
    
    public function getFinalValue($v, $segment_id = NULL){
        return strpos($v, '.') ? $v : date('d.m.Y H:i:s', $v);
    }
}
