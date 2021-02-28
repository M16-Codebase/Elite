<?php
/**
 * Автосоздание бреднов при создании элемента перечисления брендов. Также передумали использовать.
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 26.06.15
 * Time: 12:48
 */

namespace Models\CatalogManagement\CatalogHelpers\Property;


use App\Configs\BrandsConfig;
use App\Configs\CatalogConfig;
use Models\CatalogManagement\Positions\Brand;
use Models\CatalogManagement\Properties\Property;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type;

class BrandsHelper extends PropertyHelper{
    protected static $i = NULL;

    public function onEnumAdd(Property $property, $enum_id){
        if ($property['key'] == BrandsConfig::MANUFACTER_ENUM_KEY){
            $this->createBrand($property['values'][$enum_id]['value'], $enum_id);
        }
    }

    public function onEnumEdit(Property $property, $enum_id, $enum_data){
        if ($property['key'] == BrandsConfig::MANUFACTER_ENUM_KEY){
            $brand = CatalogSearch::factory(CatalogConfig::BRANDS_KEY)->setRules(array(Rule::make(BrandsConfig::KEY_BRAND_ENUM_ID)->setValue($enum_id)))->searchItems()->getFirst();
            if (empty($brand)) {
                $this->createBrand($property['values'][$enum_id]['value'], $enum_id);
            } else {
                $brand->update(array(), array(
                    BrandsConfig::KEY_BRAND_TITLE => array(array('val_id' => 0, 'value' => $property['values'][$enum_id]['value'])),
                    BrandsConfig::KEY_BRAND_ENUM_ID => array(array('val_id' => 0, 'value' => $enum_id))));
            }
        }
    }

    public function onEnumDelete(Property $property, $enum_data){
        if ($property['key'] == BrandsConfig::MANUFACTER_ENUM_KEY && BrandsConfig::TRIGGER_DELETE) {
            $brand = CatalogSearch::factory(CatalogConfig::BRANDS_KEY)->setRules(array(Rule::make(BrandsConfig::KEY_BRAND_ENUM_ID)->setValue($enum_data['id'])))->searchItems()->getFirst();
            if (!empty($brand)){
                $brand->delete($errors);
            }
        }
    }

    private function createBrand($title, $enum_id){
        $brands_catalog = Type::getByKey(CatalogConfig::BRANDS_KEY);
        $b_id = Brand::create($brands_catalog['id']);
        $brand = Brand::getById($b_id);
        $brand->update(array('status' => Brand::S_PUBLIC), array(
            BrandsConfig::KEY_BRAND_TITLE => array(array('val_id' => 0, 'value' => $title)),
            BrandsConfig::KEY_BRAND_ENUM_ID => array(array('val_id' => 0, 'value' => $enum_id))));
    }

}