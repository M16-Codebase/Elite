<?php
/**
 * Description of Post
 *
 * @author olga
 */
namespace Models\CatalogManagement\Properties;
use Models\ContentManagement\Post AS PostEntity;
class Post extends Entity {
    const TYPE_NAME = 'post';
    /**
     * запрещаем использовать фильтрацию
     */
    const ALLOW_FILTER = FALSE;
    /**
     * запрещаем использовать подбор похожих
     */
    const ALLOW_MAJOR = FALSE;
    const VALUES_TYPE_ARRAY = TRUE;

    const SELECT_MODE_LIST = 'list';
    const SELECT_MODE_EDIT_POPUP = 'edit_popup';
    const SELECT_MODE_SEARCH_POPUP = 'search_popup';

    protected static $allow_edit_mode = array(
        self::SELECT_MODE_LIST,
        self::SELECT_MODE_SEARCH_POPUP
    );
    
    public function getFinalValue($v, $segment_id = NULL){
        if (!empty($v)){
            PostEntity::prepare(is_numeric($v) ? array($v) : $v);
        }
        return $v;
    }
	
	public function getCompleteValue($v, $segment_id = NULL){
        if ($this['set']){
            return empty($v['value']) ? array() : PostEntity::factory($v['value']);
        }
        return empty($v['value']) ? NULL : PostEntity::getById($v['value']);
	}
    /**
     * что делать при удалении значения
     * @param mixed $value
     * @return NULL
     */
    public function onValueDelete($value){
        PostEntity::deleteFromDB($value);
        return NULL;
    }

    public static function prepareValues($data, &$errors){
        $data = parent::prepareValues($data, $errors);
        if (empty($data['values']['post_type']) || $data['values']['post_type'] == \Modules\Catalog\Item::POST_TYPE){
            $data['values']['post_type'] = \Modules\Catalog\Item::POST_TYPE;
            $data['values']['edit_mode'] = self::SELECT_MODE_EDIT_POPUP;
        } elseif (!array_key_exists($data['values']['post_type'], \App\Configs\PostConfig::getAllowPropertyPosts())) {
            $errors['values[post_type]'] = \Models\Validator::ERR_MSG_INCORRECT;
        }
        if (empty($data['values']['edit_mode'])) {
            $data['values']['edit_mode'] = self::SELECT_MODE_EDIT_POPUP;
        } elseif ($data['values']['post_type'] != \Modules\Catalog\Item::POST_TYPE && !in_array($data['values']['edit_mode'], static::$allow_edit_mode)) {
            $errors['values[edit_mode]'] = \Models\Validator::ERR_MSG_INCORRECT;
        }
        return $data;
    }

    public function getEntitiesList($segment_id = null, $public_only = true) {
        if (empty($this['values']['edit_mode']) || $this['values']['edit_mode'] == self::SELECT_MODE_LIST) {
            return PostEntity::search(array('type' => !empty($this['values']['post_type']) ? $this['values']['post_type'] : \Modules\Catalog\Item::POST_TYPE, 'status' => array(PostEntity::STATUS_CLOSE, PostEntity::STATUS_PUBLIC), 'segment_id' => $segment_id));
        } else {
            return array();
        }
    }
}
