<?php
/**
 * Тип данных - Дата
 *
 * @author olga
 */
namespace Models\CatalogManagement\Properties;
use Models\CatalogManagement\Variant as VariantEntity;
use Models\CatalogManagement\Item as ItemEntity;
class Date extends Property {
    const TYPE_NAME = 'date';
    const VALUES_TYPE_ARRAY = FALSE;
	const ALLOW_VALUES = FALSE;
	const ALLOW_MASK = FALSE;
	const ALLOW_SORT = FALSE;
	const ALLOW_DEFAULT = FALSE;
	protected static function getDataTypeTable($multiple){
        return $multiple ? VariantEntity::TABLE_PROP_INT : ItemEntity::TABLE_PROP_INT;
    }
    public function explicitType($v){
		if ($v == ""){
			return NULL;
		}
        return strpos($v, '.') ? strtotime($v) : $v;
    }
    
    public function getFinalValue($v, $segment_id = NULL){
        return strpos($v, '.') ? $v : date('d.m.Y', $v);
    }
    public function isValueFormatCorrect($val) {
        $time = strtotime($val);
        if (empty($time)){
            return FALSE;
        }
        return TRUE;
    }
}
?>