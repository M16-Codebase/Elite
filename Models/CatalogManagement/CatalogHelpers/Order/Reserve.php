<?php
namespace Models\CatalogManagement\CatalogHelpers\Order;

use Models\CatalogManagement\Item;
use Models\CatalogManagement\Variant;
use App\Configs\OrderConfig;
use Models\CatalogManagement\CatalogHelpers\OrderItem\Reserve AS PositionReserve;
use App\Configs\CatalogConfig;
/**
 * Резерв позиций заказа
 * на добавление позиции заказа ничего не делаем, т.к. резерв будет изменяться при редактировании количества самой позиции
 *
 * @author olya
 */
class Reserve extends OrderHelper{
    public function preVariantRemove(Item $item, Variant $variant){
        PositionReserve::changeEntityReserve($variant, -$variant[OrderConfig::KEY_POSITION_COUNT]);
    }
    public function onValueChange($action, Item $item, \Models\CatalogManagement\Properties\Property $property, $v_id, $old_values, $additional_data, $segment_id) {
        if ($action != 'edit' || $property['key'] != OrderConfig::KEY_ORDER_STATUS){
            return;
        }
        $positions = $item->getPositions();
        if ($item['properties'][OrderConfig::KEY_ORDER_STATUS]['value_key'] == OrderConfig::STATUS_NEW){
            foreach ($positions as $p){
                PositionReserve::changeEntityReserve($p, $p[OrderConfig::KEY_POSITION_COUNT]);
            }
        }elseif ($item['properties'][OrderConfig::KEY_ORDER_STATUS]['value_key'] == OrderConfig::STATUS_DELETE){
            foreach ($positions as $p){
                PositionReserve::changeEntityReserve($p, -$p[OrderConfig::KEY_POSITION_COUNT]);
            }
        }elseif ($item['properties'][OrderConfig::KEY_ORDER_STATUS]['value_key'] == OrderConfig::STATUS_COMPLETE){
            //списать остатки. т.к. они уже в резерве, то будут списываться только из резерва и просто не возвращаться в количество
            foreach ($positions as $p){
                $reserve_prop_id = $p['entity']['properties'][CatalogConfig::KEY_VARIANT_COUNT_RESERVED]['id'];
                $p['entity']->updateValue($reserve_prop_id, $p['entity'][CatalogConfig::KEY_VARIANT_COUNT_RESERVED] - $p[OrderConfig::KEY_POSITION_COUNT]);
            }
        }
    }
}
