<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 24.09.15
 * Time: 19:23
 */

namespace Modules\Catalog;


use App\Configs\CatalogConfig;
use App\Configs\RealEstateConfig;
use Models\CatalogManagement\Item as ItemEntity;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type as TypeEntity;

class District extends CatalogPublic
{
    const DEFAULT_CATALOG_KEY = CatalogConfig::CATALOG_KEY_DISTRICT;
    const REAL_ESTATE_LIST_SIZE = 10;

    public function index(){
        $catalog = TypeEntity::getByKey(CatalogConfig::CATALOG_KEY_DISTRICT, TypeEntity::DEFAULT_TYPE_ID, $this->segment['id']);
        $spb_category = TypeEntity::getByKey(CatalogConfig::CATEGORY_KEY_DISTRICT_SPB, $catalog['id'], $this->segment['id']);
        $spb_districts = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_DISTRICT, $this->segment['id'])
            ->setTypeId($spb_category['id'])
//            ->setPublicOnly(!($this->account instanceof \App\Auth\Account\Admin))
            ->searchItems()
            ->getSearch();
        $this->getAns()
            ->add('saint_petersburg', $spb_category)
            ->add('spb_districts', $spb_districts);
    }

    public function items(){
        return $this->notFound();
        $type = TypeEntity::getById($this->routeTail, $this->segment['id']);
        $items = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_DISTRICT, $this->segment['id'])
            ->setTypeId($type['id'])
//            ->setPublicOnly(!($this->account instanceof \App\Auth\Account\Admin))
            ->searchItems()
            ->getSearch();
        $this->getAns()
            ->add('current_type', $type)
            ->add('items', $items);
    }

    public function viewItem(){
        $item_id = substr($this->routeTail, 1);
        $item = ItemEntity::getById($item_id, $this->segment['id']);
        if (empty($item)) {
            return $this->notFound();
        }
        $real_estate_catalog = TypeEntity::getByKey(CatalogConfig::CATALOG_KEY_REAL_ESTATE, TypeEntity::DEFAULT_TYPE_ID, $this->segment['id']);
        $complex_category = TypeEntity::getByKey(RealEstateConfig::CATEGORY_KEY_COMPLEX, $real_estate_catalog['id'], $this->segment['id']);
        $real_estate = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_REAL_ESTATE, $this->segment['id'])
            ->setTypeId($complex_category['id'])
            ->setRules(array(
                Rule::make(RealEstateConfig::KEY_OBJECT_DISTRICT)->setValue($item['id']),
                Rule::make(RealEstateConfig::KEY_OBJECT_PRIORITY)->setOrder(true)
            ))
            ->searchItems(0, self::REAL_ESTATE_LIST_SIZE)
            ->getSearch();
        $resale = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_RESALE, $this->segment['id'])
            ->setRules(array(
                Rule::make(RealEstateConfig::KEY_APPART_DISTRICT)->setValue($item['id'])
            ))
            ->searchItems()->getSEarch();
        $this->getAns()
            ->add('item', $item)
            ->add('real_estate', $real_estate)
            ->add('resale', $resale);
    }
}