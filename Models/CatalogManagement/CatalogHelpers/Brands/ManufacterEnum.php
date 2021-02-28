<?php
/**
 * Было написано для приввязки брендов к элементам перечисления. Мы передумали это использовать.
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 25.06.15
 * Time: 19:24
 */

namespace Models\CatalogManagement\CatalogHelpers\Brands;


use App\Configs\BrandsConfig;
use App\Configs\CatalogConfig;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Properties\Factory as PropertyFactory;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type;

class ManufacterEnum extends BrandsHelper{
    protected static $i = NULL;

    private $enum_update_cache = array();
    /** @var \Models\CatalogManagement\Properties\Property */
    private $enum_prop = NULL;

    public function onUpdate($updateKey, Item $item, $segment_id, $updatedProperties){
        $enum = $this->getEnumProp();
        if (!empty($enum) && !empty($this->enum_update_cache[$item['id']])){
            $enum->update($this->enum_update_cache[$item['id']]);
        }
    }

    public function preUpdate($updateKey, Item $item, &$params, &$properties, $segment_id, &$errors)
    {
        unset($this->enum_update_cache[$item['id']]);
        if (!empty($properties['title'][0]['value']) && $properties['title'][0]['value'] != $item['title']) {
            $enum = $this->getEnumProp();
            if (empty($enum)) {
                return;
            }
            if (empty($item['enum_id'])) {
                // Создаем элемент проперти
                $data = $enum->asArray();
                $values = array();
                $enum_id = NULL;
                foreach ($data['values'] as $v_id => $val_data) {
                    if ($val_data['value'] == $properties['title'][0]['value']) {
                        $enum_id = $v_id;
                        break;
                    }
                    $values[$v_id] = $val_data['value'];
                }
                if (!empty($enum_id)) {
                    // Привязка к существующему
                    $properties[BrandsConfig::KEY_BRAND_ENUM_ID][0] = array('val_id' => 0, 'value' => $enum_id);
                } else {
                    // Создание нового
                    $values[] = $properties['title'][0]['value'];
                    $data['values'] = array('values' => $values);
                    $enum->update($data, $errors);
                    $enum_id = NULL;
                    foreach ($enum->asArray()['values'] as $val_id => $val_data) {
                        if ($val_data['value'] == $properties['title'][0]['value']) {
                            $enum_id = $val_id;
                            break;
                        }
                    }
                    $properties[BrandsConfig::KEY_BRAND_ENUM_ID][0] = array('val_id' => 0, 'value' => $enum_id);
                }
            } else {
                // Редактируем
                $data = $enum->asArray();
                $values = array();
                foreach ($data['values'] as $val_id => $val_data) {
                    if ($val_id == $item['enum_id']) {
                        $values[$val_id] = $properties['title'][0]['value'];
                    } else {
                        $values[$val_id] = $val_data['value'];
                    }
                }
                $data['values'] = array('values' => $values);
                $this->enum_update_cache[$item['id']] = $data;
            }
        }
    }

    public function preDelete(Item $item, &$errors = array()){
        $count = CatalogSearch::factory(CatalogConfig::CATALOG_KEY)->setRules(array(Rule::make(BrandsConfig::MANUFACTER_ENUM_KEY)->setValue($item[BrandsConfig::KEY_BRAND_ENUM_ID])))->searchItemIds()->count();
        if ($count){
            $errors['brand'] = 'not_empty';
        }
    }

    public function onDelete($id, $entity, $remove_from_db){
        if (BrandsConfig::TRIGGER_DELETE && !empty($entity[BrandsConfig::KEY_BRAND_ENUM_ID])) {
            $enum = $this->getEnumProp();
            $data = $enum->asArray();
            unset($data['values'][$entity[BrandsConfig::KEY_BRAND_ENUM_ID]]);
            $values = array();
            foreach($data['values'] as $val_data){
                $values[$val_data['id']] = $val_data['value'];
            }
            $data['values'] = array('values' => $values);
            $enum->update($data);
        }
    }

    /**
     * @return \Models\CatalogManagement\Properties\Property|null
     * @throws \Exception
     */
    private function getEnumProp(){
        if (empty($this->enum_prop)){
            $manuf_enums = PropertyFactory::search(Type::DEFAULT_TYPE_ID, PropertyFactory::P_ALL, 'id', 'type_group', 'children', array('key' => BrandsConfig::MANUFACTER_ENUM_KEY));
            if (empty($manuf_enums)){
                return NULL;
            } elseif (count($manuf_enums) > 1) {
                if (BrandsConfig::SCREAM_ON_MULTIPLE_ENUM){
                    throw new \Exception('Обнаружено '.count($manuf_enums).' свойств '.BrandsConfig::MANUFACTER_ENUM_KEY.', допустимо только одно');
                }
                return NULL;
            }
            /** @var \Models\CatalogManagement\Properties\Property $enum */
            $enum = reset($manuf_enums);
            if ($enum['data_type'] != \Models\CatalogManagement\Properties\Enum::TYPE_NAME){
                throw new \Exception('Свойство '.BrandsConfig::MANUFACTER_ENUM_KEY.' должно быть перечислением');
            }
            $this->enum_prop = $enum;
        }
        return $this->enum_prop;
    }
}