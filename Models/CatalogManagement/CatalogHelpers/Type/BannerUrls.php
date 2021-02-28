<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 29.06.15
 * Time: 17:28
 *
 * Поддерживаем урлы баннеров в актуальном состоянии (изменился урл категории каталога - изменились и урлы баннеров,
 * расположенных на странице категории)
 */

namespace Models\CatalogManagement\CatalogHelpers\Type;


use Models\CatalogManagement\Type;

class BannerUrls extends TypeHelper{
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
            $this->db->query('UPDATE ?# SET `uri` = REPLACE(`uri`, ?s, ?s) WHERE `uri` LIKE ?s',
                \Models\Banner::TABLE_URI,
                self::$update_cache[$type['id']]['old_url'],
                self::$update_cache[$type['id']]['new_url'],
                self::$update_cache[$type['id']]['old_url'].'%'
            );
            unset(self::$update_cache[$type['id']]);
        }
    }

}