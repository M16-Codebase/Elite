<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 18.08.15
 * Time: 19:50
 */

namespace Models\CatalogManagement\CatalogHelpers\Item;


use Models\CatalogManagement\CatalogHelpers\Property\NestedInSearchProps;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Properties\Factory as PropertyFactory;
use Models\CatalogManagement\Properties\Property;

class NestedInEditSearchValue extends ItemHelper
{
    protected static $i = null;
    /**
     * @var Property[][]
     */
    private $dataCache = array();

    public function preCreate($type_id, $propValues, &$errors, $segment_id){

    }
    public function onCreate($item_id, $Segment_id){
        $item = Item::getById($item_id);
        if (!empty($item)) {
            $type = $item->getType();
            if ($type['nested_in'] && $type['nested_in_final'] && $item['parent_id']) {
                NestedInSearchProps::factory()->copyValuesIntoNewItem($item);
            }
        }
    }

    public function preUpdate($updateKey, Item $item, &$params, &$properties, $segment_id, &$errors){
        if (empty($properties)) {
            return;
        }
        $type = $item->getType();
        $catalog = $type->getCatalog();
        if ($catalog['nested_in'] && !$catalog['nested_in_final']) { // Только в кустике, и только для неконечных кустистых категорий
            $props = PropertyFactory::search($type['id'], PropertyFactory::P_ALL, 'key', 'type_group', 'self', array('filter_visible' => 7));
            if (!empty($props)){
                $segment_props = $item->getSegmentProperties($segment_id);
                foreach($props as $p) {
                    if (!empty($properties[$p['key']])){
                        if ($p['set']) {
                            foreach ($properties[$p['key']] as $p_data) {
                                if (empty($p_data['val_id'])
                                    || !empty($p_data['options']['delete']) // delete проверяется до апдейта, т.к. может прийти два раза в разных сегментах
                                    || $p_data['value'] != $segment_props[$p['key']]['value'][$p_data['val_id']])
                                {
                                    $this->dataCache[$updateKey][$p['id']] = $p;
                                    break;
                                }
                            }
                        } else {
                            if ((empty($segment_props[$p['key']]) || $properties[$p['key']][0]['value'] != $segment_props[$p['key']]['value'])) {
                                $this->dataCache[$updateKey][$p['id']] = $p;
                            }
                        }
                    }
                }
            }
        }
    }

    public function onUpdate($updateKey, Item $item, $segment_id, $updatedProperties){
        if (!empty($this->dataCache[$updateKey])) {
            $helper = \Models\CatalogManagement\CatalogHelpers\Property\NestedInSearchProps::factory();
            foreach($this->dataCache[$updateKey] as $prop) {
                $helper->transferPropValues($prop, $item, $segment_id);
            }
            unset($this->dataCache[$updateKey]);
        }
    }
}