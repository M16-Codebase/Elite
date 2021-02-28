<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 29.06.15
 * Time: 17:19
 */

namespace Models\CatalogManagement\CatalogHelpers\Type;


use Models\CatalogManagement\Type;
use Models\Seo\PageRedirect;

class PageRedirectHelper extends TypeHelper{
    protected static $i = NULL;

    private static $update_cache = array();

    public function preUpdate(Type $type, &$params, &$errors){
        if ($type->getCatalog()['allow_item_url'] && !empty($params['url']) && $params['url'] != $type['url']) {
            self::$update_cache[$type['id']] = array(
                'old_url' => $type['url'],
                'new_url' => $params['url']
            );
        }
    }

    public function onUpdate(Type $type){
        if (!empty(self::$update_cache[$type['id']])){
            PageRedirect::getInstance()->createTypeAutoRedirect($type, self::$update_cache[$type['id']]['old_url'], self::$update_cache[$type['id']]['new_url']);
            unset(self::$update_cache[$type['id']]);
        }
    }

}