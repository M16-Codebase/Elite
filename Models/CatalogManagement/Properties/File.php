<?php
/**
 * Description of Post
 *
 * @author olga
 */
namespace Models\CatalogManagement\Properties;
use Models\FilesManagement\File as FileEntity;
class File extends Entity {
    const TYPE_NAME = 'file';
    /**
     * запрещаем использовать фильтрацию
     */
    const ALLOW_FILTER = FALSE;
    /**
     * запрещаем использовать подбор похожих
     */
    const ALLOW_MAJOR = FALSE;
    const VALUES_TYPE_ARRAY = TRUE;
    
    public function getFinalValue($v, $segment_id = NULL){
        if (!empty($v)){
            FileEntity::prepare(is_numeric($v) ? array($v) : $v);
        }
        return $v;
    }

    public function getCompleteValue($v, $segment_id = NULL){
        if ($this['set']){
            return empty($v['value']) ? array() : FileEntity::factory($v['value']);
        }
        return empty($v['value']) ? NULL : FileEntity::getById($v['value']);
    }
    /**
     * что делать при удалении значения
     * @param mixed $value
     * @return NULL
     */
    public function onValueDelete($value){
        FileEntity::del($value);
        return NULL;
    }
}
?>