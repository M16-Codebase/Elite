<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 29.06.15
 * Time: 17:47
 */

namespace Models\CatalogManagement\CatalogHelpers\CatalogPosition;


use Models\CatalogManagement\CatalogPosition;
use Models\CatalogManagement\Item;
use Models\Seo\PageRedirect;

class PageRedirectHelper extends CatalogPositionHelper{
    protected static $i = NULL;

    private static $update_cache = array();

    public function preUpdate($updateKey, CatalogPosition $entity, &$params, &$properties, $segment_id, &$errors){
        // при смене ключа меняется урл, нужно генерировать редиректы, кроме случая когда урла еще не было (ключ пустой)
        if (isset($params['key']) && $params['key'] != $entity['key']){
            $data = array();
            if (!empty($entity['key'])){
                $data['old_url'] = $entity->getUrl(); // Если ключ был ранее задан - нужно сгенерировать редиректы
            } else {
                $data['need_delete_old_redirect'] = TRUE; // Если нет - удалить возможно созданные ранее редиректы с урла объекта
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
            if ($entity->getType()->getCatalog()['allow_item_url'] && !empty(self::$update_cache[$cache_key]['old_url'])) {
                if ($entity instanceof Item){
                    PageRedirect::getInstance()->createItemAutoRedirect($entity, self::$update_cache[$cache_key]['old_url'], $entity->getUrl());
                } else{
                    PageRedirect::getInstance()->createVariantAutoRedirect(self::$update_cache[$cache_key]['old_url'], $entity->getUrl());
                }
            } elseif (!empty(self::$update_cache[$cache_key]['need_delete_old_redirect'])){
                PageRedirect::getInstance()->deleteWithAllSegments($entity->getUrl());
            }
            unset(self::$update_cache[$cache_key]);
        }
    }
}