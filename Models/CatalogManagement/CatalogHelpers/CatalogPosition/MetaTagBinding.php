<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 13.07.15
 * Time: 19:36
 */

namespace Models\CatalogManagement\CatalogHelpers\CatalogPosition;


use Models\CatalogManagement\CatalogPosition;
use Models\CatalogManagement\Item;
use Models\Seo\PagePersister;

class MetaTagBinding extends CatalogPositionHelper
{
    protected static $i = NULL;

    private static $update_cache = array();

    public function preUpdate($updateKey, CatalogPosition $entity, &$params, &$properties, $segment_id, &$errors){
        // при смене ключа меняется урл, нужно генерировать редиректы, кроме случая когда урла еще не было (ключ пустой)
        if (isset($params['key']) && $params['key'] != $entity['key']){
            $data = array();
            if (!empty($entity['key'])){
                $data['old_url'] = $entity->getUrl(); // Если ключ был ранее задан - нужно сгенерировать редиректы
            }
            self::$update_cache[($entity instanceof Item ? 'i' : 'v') . $entity['id']] = $data;
        }
    }
    /**
     * событие на изменение CatalogPosition
     */
    public function onUpdate($updateKey, CatalogPosition $entity, $segment_id, $updatedProperties){
        $cache_key = ($entity instanceof Item ? 'i' : 'v') . $entity['id'];
        if (!empty(self::$update_cache[$cache_key])) {
            if (!empty(self::$update_cache[$cache_key]['old_url'])) {
                PagePersister::getInstance()->updateMetaTagBinding(self::$update_cache[$cache_key]['old_url'], $entity->getUrl());
            }
            unset(self::$update_cache[$cache_key]);
        }
    }

}