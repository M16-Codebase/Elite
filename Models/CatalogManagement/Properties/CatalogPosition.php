<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 12.09.14
 * Time: 15:37
 */

namespace Models\CatalogManagement\Properties;


use App\Configs\CatalogConfig;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type;

abstract class CatalogPosition extends Entity{
    const VALUES_TYPE_ARRAY = TRUE;
    const ATTR_ALLOW_CATALOG_POSITION = 'UNDECLARED';

    const SELECT_MODE_LIST = 'list';
    const SELECT_MODE_EDIT_POPUP = 'edit_popup';
    const SELECT_MODE_SEARCH_POPUP = 'search_popup';
    const SELECT_MODE_NONE = NULL;

    protected static $allow_edit_mode = array(
        self::SELECT_MODE_NONE,
        self::SELECT_MODE_LIST,
//        self::SELECT_MODE_SEARCH_POPUP
    );

    public static function prepareValues($data, &$errors){
        $data = parent::prepareValues($data, $errors);
        if (empty($data['values']['catalog_id'])){
            $errors['values[catalog_id]'] = 'empty';
        } elseif (!is_numeric($data['values']['catalog_id'])){
            $catalog = Type::getByKey($data['values']['catalog_id']);
            if (!empty($catalog) && $catalog[static::ATTR_ALLOW_CATALOG_POSITION]) {
                $data['values']['catalog_id'] = $catalog['id'];
            } else {
                $errors['values[catalog_id]'] = 'incorrect_value';
            }
        }
        if (!empty($data['values']['catalog_id'])) {
            $catalog = Type::getById($data['values']['catalog_id']);
            if (empty($catalog) || !$catalog->isCatalog()) {
                $errors['values[catalog_id]'] = \Models\Validator::ERR_MSG_INCORRECT;
            }
        }
        if (empty($data['values']['edit_mode'])) {
            $data['values']['edit_mode'] = self::SELECT_MODE_NONE;
        } elseif (!in_array($data['values']['edit_mode'], static::$allow_edit_mode)) {
            $errors['values[edit_mode]'] = \Models\Validator::ERR_MSG_INCORRECT;
        } elseif ($data['values']['edit_mode'] == self::SELECT_MODE_EDIT_POPUP
            && !empty($catalog)
            && $catalog['allow_children']
        ) {
            // Мы не можем сделать попап создания/редактирования вложенного айтема
            // в каталоге с дочерними категориями, т.к. не знаем, в какой категории
            // создавать айтем
            $errors['values[edit_mode]'] = \Models\Validator::ERR_MSG_INCORRECT;
        }
        return $data;
    }

    public function getEntitiesList($segment_id = null, $public_only = true) {
        if ($this['values']['edit_mode'] == self::SELECT_MODE_LIST) {
            $entities_catalog = Type::getById($this['values']['catalog_id'], $segment_id);
            $entities_search = CatalogSearch::factory($entities_catalog['key'], $segment_id)->setPublicOnly($public_only);
            $entities_list = $this instanceof Item
                ? $entities_search->searchItems()->getSearch()
                : $entities_search->searchVariants()->getSearch();
            return $entities_list;
        } else {
            return array();
        }
    }
} 