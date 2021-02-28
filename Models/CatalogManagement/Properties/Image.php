<?php
namespace Models\CatalogManagement\Properties;

use Models\ImageManagement\Image as ImageEntity;
/**
 * Description of Image
 *
 * @author mac-proger
 */
class Image extends Entity{
    const TYPE_NAME = 'image';
    const ALLOW_SET = TRUE;
    
    public function getFinalValue($v, $segment_id = NULL){
		if (!empty($v)){
			ImageEntity::prepare(is_numeric($v) ? array($v) : $v);
		}
        return $v;
    }
    
    public function getCompleteValue($v, $segment_id = NULL){
        if ($this['set']){
            return empty($v['value']) ? array() : ImageEntity::factory($v['value'], $segment_id);
        }
        return empty($v['value']) ? NULL : ImageEntity::getById($v['value'], $segment_id);
	}
    /**
     * что делать при удалении значения
     * @param mixed $value
     * @return NULL
     */
    public function onValueDelete($value){
        ImageEntity::del($value);
        return NULL;
    }
}
