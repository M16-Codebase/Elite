<?php
namespace Models\CatalogManagement\Properties;

use Models\ImageManagement\Collection;
/**
 * Description of Image
 *
 * @author mac-proger
 */
class Gallery extends Entity{
    const TYPE_NAME = 'gallery';
    const ALLOW_SET = FALSE;
    
    public function getFinalValue($v, $segment_id = NULL){
        if (!empty($v)){
            Collection::prepare(is_numeric($v) ? array($v) : $v);
        }
        return $v;
    }
    
    public function getCompleteValue($v, $segment_id = NULL){
        if ($this['set']){
            return empty($v['value']) ? array() : Collection::factory($v['value'], $segment_id);
        }
        return empty($v['value']) ? NULL : Collection::getById($v['value'], $segment_id);
	}
    /**
     * что делать при удалении значения
     * @param mixed $value
     * @return NULL
     */
    public function onValueDelete($value){
        Collection::delete($value);
        return NULL;
    }
}
