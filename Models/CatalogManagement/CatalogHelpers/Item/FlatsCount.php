<?php
/**
 * Возвращает количество квартир жилого комплекса (общее и в продаже)
 *
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 22.09.15
 * Time: 18:11
 */

namespace Models\CatalogManagement\CatalogHelpers\Item;


use App\Configs\CatalogConfig;
use App\Configs\RealEstateConfig;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type;

class FlatsCount extends ItemHelper
{
    const FLAT_FOR_SALE_LIST_COUNT = 10;

    protected static $i = null;
    protected static $fieldsList = array('flats_count', 'flats_for_sale_count', 'flats_for_sale');

    private $cache = array();

    public function get(Item $i, $field){
        if (in_array($field, $this->fieldsList())){
            $type = $i->getType();
            $catalog = $type->getCatalog();
            if ($catalog['key'] != CatalogConfig::CATALOG_KEY_REAL_ESTATE || $type['key'] == RealEstateConfig::CATEGORY_KEY_FLAT) {
                return null;
            } else {
                if (!isset($this->cache[$i['id']][$field])) {
                    $flat_category = Type::getByKey(RealEstateConfig::CATEGORY_KEY_FLAT, $catalog['id'], $i['segment_id']);
                    if ($field != 'flats_for_sale') {
                        $this->cache[$i['id']]['flats_count'] = !empty($i[RealEstateConfig::KEY_OBJECT_APART_IN_COMPLEX])
                            ? $i[RealEstateConfig::KEY_OBJECT_APART_IN_COMPLEX]
                            : CatalogSearch::factory(CatalogConfig::CATALOG_KEY_REAL_ESTATE, $i['segment_id'])
                                ->setRules(array(
                                    'type_id' => Rule::make('type_id')->setValue($flat_category['id']),
                                    'parents' => Rule::make('parents')->setValue('.'.$type['id'].':'.$i['id'].'.', ($type['key'] == RealEstateConfig::CATEGORY_KEY_COMPLEX ? Rule::SEARCH_LIKE_LEFT : Rule::SEARCH_LIKE))
                                ))
                                ->searchItemIds()
                                ->count();
                        
                        $this->cache[$i['id']]['flats_for_sale_count'] = !empty($i[RealEstateConfig::KEY_OBJECT_APART_IN_SALE])
                            ? $i[RealEstateConfig::KEY_OBJECT_APART_IN_SALE]
                            : CatalogSearch::factory(CatalogConfig::CATALOG_KEY_REAL_ESTATE, $i['segment_id'])
                                ->setRules(array(
                                    'type_id' => Rule::make('type_id')->setValue($flat_category['id']),
                                    'parents' => Rule::make('parents')->setValue('.'.$type['id'].':'.$i['id'].'.', ($type['key'] == RealEstateConfig::CATEGORY_KEY_COMPLEX ? Rule::SEARCH_LIKE_LEFT : Rule::SEARCH_LIKE)),
                                    RealEstateConfig::KEY_APPART_STATE => Rule::make(RealEstateConfig::KEY_APPART_STATE)->setValue('for_sale')->setSearchByEnumKey()
                                ))
                                ->searchItemIds()
                                ->count();
                        //var_dump($i[RealEstateConfig::KEY_OBJECT_APART_IN_SALE]);
                    } else {
                        $this->cache[$i['id']][$field] = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_REAL_ESTATE, $i['segment_id'])
                            ->setTypeId($flat_category['id'])
                            ->setRules(array(
                                Rule::make('parents')->setValue('.'.$type['id'].':'.$i['id'].'.', ($type['key'] == RealEstateConfig::CATEGORY_KEY_COMPLEX ? Rule::SEARCH_LIKE_LEFT : Rule::SEARCH_LIKE)),
                                Rule::make(RealEstateConfig::KEY_APPART_STATE)->setValue(RealEstateConfig::KEY_APPART_STATE_FOR_SALE)->setSearchByEnumKey()
                            ))
                            ->setSortMode(CatalogSearch::SORT_RANDOM)
                            ->searchItems(0, self::FLAT_FOR_SALE_LIST_COUNT)
                            ->getSearch();
                    }
                }
                return isset($this->cache[$i['id']][$field]) ? $this->cache[$i['id']][$field] : null;
            }
        }
    }

}