<?php
namespace Models\CatalogManagement\Properties;

use App\Configs\CatalogConfig;
use Models\CatalogManagement\Item as ItemEntity;
use Models\CatalogManagement\Type;
/**
 * Description of Item
 *
 * @author charles manson
 */
class Item extends CatalogPosition{
    const TYPE_NAME = 'item';
    const FILTER_VIEW_KEY = CatalogConfig::KEY_ITEM_TITLE;
    const ATTR_ALLOW_CATALOG_POSITION = 'allow_item_property';

    protected static $allow_edit_mode = array(
        self::SELECT_MODE_NONE,
        self::SELECT_MODE_LIST,
        self::SELECT_MODE_EDIT_POPUP,
//        self::SELECT_MODE_SEARCH_POPUP
    );

    public function getFinalValue($v, $segment_id = NULL){
        if (!empty($v)){
            ItemEntity::prepare(is_numeric($v) ? array($v) : $v);
        }
        return $v;
    }

	public function getCompleteValue($v, $segment_id = NULL){
        if ($this['set']){
            return empty($v['value']) ? array() : ItemEntity::factory(is_array($v['value']) ? $v['value'] : array($v['value']), $segment_id);
        }
        return empty($v['value']) ? NULL : ItemEntity::getById($v['value'], $segment_id);
	}
}
