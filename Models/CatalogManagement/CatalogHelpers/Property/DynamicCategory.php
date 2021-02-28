<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 12.10.15
 * Time: 16:38
 */

namespace Models\CatalogManagement\CatalogHelpers\Property;


use App\Configs\CatalogConfig;
use Models\CatalogManagement\Properties\Property;
use Models\CatalogManagement\Type;

class DynamicCategory extends PropertyHelper
{
    protected static $i = null;

    private $need_check = array();

    public function preUpdate(Property $property, &$params, &$errors){
        if ($property['key'] == CatalogConfig::KEY_ITEM_DYNAMIC_CATEGORY) {
            $this->need_check[$property['id']] = true;
        }
    }

    public function onUpdate(Property $property){
        if (!empty($this->need_check[$property['id']])) {
            $dynamic_catalog = Type::getByKey(CatalogConfig::DYNAMIC_CATEGORY_KEY);
            $child_categories = Type::search(array('parent_id' => $dynamic_catalog['id']));
            $type_by_val = array();
            if (!empty($child_categories)) {
                foreach($child_categories as $cat) {
                    if (!empty($cat['rules'])) {
                        foreach($cat['rules'] as $rule) {
                            foreach ($rule as $field => $val) {
                                if (!empty($val['value']) && $field == CatalogConfig::KEY_ITEM_DYNAMIC_CATEGORY) {
                                    $type_by_val[is_array($val['value']) ? reset($val['value']) : $val['value']] = $cat['id'];
                                }
                            }
                        }
                    }
                }
            }
            $helper = \Models\CatalogManagement\CatalogHelpers\Type\DynamicCategory::factory();
            foreach($property['values'] as $val_id => $val) {
                if (!empty($type_by_val[$val_id])) {
                    unset($type_by_val[$val_id]);
                } else {
                    $params['title'] = $val['value'];
                    $params['key'] = !empty($val['key']) ? $val['key'] : \LPS\Components\Translit::UrlTranslit($val['value']);
                    $params['parent_id'] = $dynamic_catalog['id'];
                    $category = Type::create($params);
                    $helper->addDynamicRule($category, array(CatalogConfig::KEY_ITEM_DYNAMIC_CATEGORY => array('value' => array($val_id))), $err);
                }
            }
            if (!empty($type_by_val)) {
                foreach($type_by_val as $type_id) {
                    $child_categories[$type_id]->delete();
                }
            }
            unset($this->need_check[$property['id']]);
        }
    }
}