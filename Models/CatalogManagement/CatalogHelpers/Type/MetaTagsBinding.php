<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 13.07.15
 * Time: 19:25
 *
 * Переписываем привязку метатегов к категориями о объектам каталога при смене урла
 */

namespace Models\CatalogManagement\CatalogHelpers\Type;


use Models\CatalogManagement\Type;
use Models\Seo\PagePersister;

class MetaTagsBinding extends TypeHelper
{
    protected static $i = NULL;

    private static $update_cache = array();

    public function preUpdate(Type $type, &$params, &$errors){
        if (!empty($params['url']) && $params['url'] != $type['url']) {
            self::$update_cache[$type['id']] = array(
                'old_url' => $type['url'],
                'new_url' => $params['url']
            );
        }
    }

    public function onUpdate(Type $type){
        if (!empty(self::$update_cache[$type['id']])){
            PagePersister::getInstance()->updateMetaTagBinding(self::$update_cache[$type['id']]['old_url'], self::$update_cache[$type['id']]['new_url']);
            unset(self::$update_cache[$type['id']]);
        }
    }

}