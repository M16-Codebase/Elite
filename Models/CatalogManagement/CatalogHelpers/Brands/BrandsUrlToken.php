<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 29.06.15
 * Time: 12:27
 */

namespace Models\CatalogManagement\CatalogHelpers\Brands;


use App\Configs\BrandsConfig;
use App\Configs\CatalogConfig;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Search\CatalogSearch;

class BrandsUrlToken extends BrandsHelper{
    protected static $i = NULL;
    protected static $fieldsList = array('banner_url');

    private static $update_cache = array();

    public function preUpdate($updateKey, Item $item, &$params, &$properties, $segment_id, &$errors){
        if (!empty($properties[BrandsConfig::KEY_BRAND_URL_TOKEN][0]['value'])){
            $route_map = \LPS\Config::getInstance()->getModulesRouteMap(TRUE);
            if (!empty($route_map[$properties[BrandsConfig::KEY_BRAND_URL_TOKEN][0]['value']])) {
                $errors[] = array(
                    'key' => BrandsConfig::KEY_BRAND_URL_TOKEN,
                    'error' => 'reserved'
                );
            } else {
                $another = CatalogSearch::factory(CatalogConfig::BRANDS_KEY)
                    ->setRules(array(
                        Rule::make('id')->setNot($item['id']),
                        Rule::make(BrandsConfig::KEY_BRAND_URL_TOKEN)->setValue($properties[BrandsConfig::KEY_BRAND_URL_TOKEN][0]['value'])
                    ))
                    ->searchItems()
                    ->getFirst();
                if (!empty($another)){
                    $errors[] = array(
                        'unique' => array(BrandsConfig::KEY_BRAND_URL_TOKEN => $another['id'])
                    );
                }
            }
            if ($properties[BrandsConfig::KEY_BRAND_URL_TOKEN][0]['value'] != $item[BrandsConfig::KEY_BRAND_URL_TOKEN]) {
                self::$update_cache[$item['id']] = array(
                    'old' => '/' . $item[BrandsConfig::KEY_BRAND_URL_TOKEN] . '/',
                    'new' => '/' . $properties[BrandsConfig::KEY_BRAND_URL_TOKEN][0]['value'] . '/',
                    'old_url' => $item->getUrl($segment_id)
                );
            }
        }
    }
    public function onUpdate($updateKey, Item $item, $segment_id, $updatedProperties){
        /** @var \Models\CatalogManagement\Positions\Brand $item */
        if (!empty(self::$update_cache[$item['id']])){
            \App\Builder::getInstance()->getDB()->query('UPDATE ?# SET `uri` = ?s WHERE `uri` = ?s',
                \Models\Banner::TABLE_URI,
                self::$update_cache[$item['id']]['new'],
                self::$update_cache[$item['id']]['old']
            );
            // Генерация автоматических редиректов
            $page_redirect = \Models\Seo\PageRedirect::getInstance();
            $page_redirect->createItemAutoRedirect($item, self::$update_cache[$item['id']]['old_url'], $item->getUrl());
            if (!$item->isMonoBrand()) {
                $child_items = CatalogSearch::factory(CatalogConfig::CATALOG_KEY, null)
                    ->setRules(array(Rule::make(CatalogConfig::KEY_ITEM_BRAND)->setValue($item['id'])))
                    ->setPublicOnly(false)
                    ->searchItems()
                    ->getSearch();
                if (!empty($child_items)) {
                    $new_brand_url = $item->getUrl();
                    foreach($child_items as $child) {
                        $new_url = $child->getUrl();
                        $old_url = str_replace($new_brand_url, self::$update_cache[$item['id']]['old_url'], $new_url);
                        $page_redirect->createItemAutoRedirect($child, $old_url, $new_url);
                    }
                }
            }
            unset(self::$update_cache[$item['id']]);
        }
    }
}