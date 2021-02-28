<?php
namespace Models\CatalogManagement\Properties;

use Models\CatalogManagement\Item as ItemEntity;
use Models\CatalogManagement\Variant as VariantEntity;
/**
 * Description of Entity
 *
 * @author charles manson
 */
abstract class Entity extends Property{
    const VALUES_TYPE_ARRAY = FALSE;
	const ALLOW_MASK = FALSE;
	const ALLOW_SORT = FALSE;
	const ALLOW_DEFAULT = FALSE;
    /**
     * Отдает TRUE для пропертей в values которых находится объект
     * @return bool
     */
    public function isDataTypeEntity(){
        return TRUE;
    }

	protected static function getDataTypeTable($multiple){
        return $multiple ? VariantEntity::TABLE_PROP_INT : ItemEntity::TABLE_PROP_INT;
    }
    public function explicitType($v){
		if ($v == ""){
			return NULL;
		}
        return (int) $v;
    }
//@TODO кажися не нужно
//    public function removeLinkedObject($remove_from_db = false){
//
//    }
}
