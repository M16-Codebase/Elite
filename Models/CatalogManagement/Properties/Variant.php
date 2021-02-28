<?php
namespace Models\CatalogManagement\Properties;

use App\Configs\CatalogConfig;
use Models\CatalogManagement\Type;
use Models\CatalogManagement\Variant as VariantEntity;
/**
 * Description of Variant
 *
 * @author charles manson
 */
class Variant extends CatalogPosition{
    const TYPE_NAME = 'variant';
    const FILTER_VIEW_KEY = CatalogConfig::KEY_VARIANT_TITLE;
    const ATTR_ALLOW_CATALOG_POSITION = 'allow_variant_property';
    public function getFinalValue($v, $segment_id = NULL){
        if (!empty($v)){
            VariantEntity::prepare(is_numeric($v) ? array($v) : $v);
        }
        return $v;
    }
    
	public function getCompleteValue($v, $segment_id = NULL){
		if ($this['set']){
            return empty($v['value']) ? array() : VariantEntity::factory(is_array($v['value']) ? $v['value'] : array($v['value']), $segment_id);
        }
        return empty($v['value']) ? NULL : VariantEntity::getById($v['value'], $segment_id);
	}
}
