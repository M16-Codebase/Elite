<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 29.06.15
 * Time: 15:56
 */

namespace Models\CatalogManagement\CatalogHelpers\Item;


use App\Configs\BrandsConfig;
use App\Configs\CatalogConfig;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Positions\Brand;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Search\CatalogSearch;

class MonoBrand extends ItemHelper{
    protected static $i = NULL;

    /**
     * Препятствуем созданию более одного товара в монобренде
     * @param $updateKey
     * @param Item $item
     * @param array $params
     * @param array $properties
     * @param int|NULL $segment_id
     * @param array $errors
     */
    public function preUpdate($updateKey, Item $item, &$params, &$properties, $segment_id, &$errors){
        if (!empty($properties[CatalogConfig::KEY_ITEM_BRAND][0]['value'])
            && $properties[CatalogConfig::KEY_ITEM_BRAND][0]['value'] != $item['properties'][CatalogConfig::KEY_ITEM_BRAND]['value'])
        {
            $new_brand = Brand::getById($properties[CatalogConfig::KEY_ITEM_BRAND][0]['value'], $segment_id);
            if (empty($new_brand)) {
                $errors[] = array(
                    'segment_id' => !empty($segment_id) ? $segment_id : 0,
                    'key' => CatalogConfig::KEY_ITEM_BRAND,
                    'title' => $item['properties'][CatalogConfig::KEY_ITEM_BRAND]['title'],
                    'error' => \Models\Validator::ERR_MSG_NOT_FOUND
                );
            } elseif ($new_brand['properties'][BrandsConfig::KEY_BRAND_MONO]['value']) {
                $catalog = $item->getType()->getCatalog();
                $count = CatalogSearch::factory($catalog['key'], $segment_id)
                    ->setRules(array(
                        Rule::make('id')->setNot($item['id']),
                        Rule::make(CatalogConfig::KEY_ITEM_BRAND, $new_brand['id'])
                    ))
                    ->setPublicOnly(false)
                    ->searchItems()
                    ->count();
                if ($count) {
                    $errors[] = array(
                        'segment_id' => !empty($segment_id) ? $segment_id : 0,
                        'key' => CatalogConfig::KEY_ITEM_BRAND,
                        'title' => $item['properties'][CatalogConfig::KEY_ITEM_BRAND]['title'],
                        'error' => \Models\Validator::ERR_MSG_UNIQUE
                    );
                }
            }
        }
    }
}