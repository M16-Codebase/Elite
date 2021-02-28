<?php
namespace Models\CatalogManagement\CatalogHelpers\OrderItem;

use Models\CatalogManagement\Variant;
use Models\CatalogManagement\Positions\OrderItem;
use App\Configs\OrderConfig;
use App\Configs\CatalogConfig;
use Models\CatalogManagement\Properties\Factory AS PropertyFactory;
use Models\CatalogManagement\Properties\Property;
/**
 * Резерв остатков
 *
 * @author olya
 */
class Reserve extends OrderItemHelper{
    protected static $i = NULL;
    public function preUpdate($updateKey, Variant $variant, &$params, &$properties, $segment_id, &$errors) {
        if (!isset($properties[$variant['properties'][OrderConfig::KEY_POSITION_COUNT]['id']])){
            return;
        }
        $old_count = !empty($variant[OrderConfig::KEY_POSITION_COUNT]) ? $variant[OrderConfig::KEY_POSITION_COUNT] : 0;
        $new_count = !empty($properties[$variant['properties'][OrderConfig::KEY_POSITION_COUNT]['id']]['value']) ? $properties[$variant['properties'][OrderConfig::KEY_POSITION_COUNT]['id']]['value'] : 0;
        $count = $new_count - $old_count;
        $flag_count = OrderConfig::getParameter(\App\Configs\Settings::KEY_POSITION_COUNT_CONSIDER);
        //если остатки резервируются, то тем более нельзя заказать больше чем есть
        $flag_reserve = OrderConfig::getParameter(\App\Configs\Settings::KEY_POSITION_RESERVE);
        if (($flag_count || $flag_reserve) 
            && isset($variant['properties'][\App\Configs\CatalogConfig::KEY_VARIANT_COUNT]) 
            && ($count > 0 && $variant[\App\Configs\CatalogConfig::KEY_VARIANT_COUNT] < $count)
        ){
            $errors['count'] = 'not enough';
            return FALSE;
        }
    }
    public function onValueChange($action, Variant $position, Property $property, $v_id, $old_values, $additional_data, $segment_id){
        $param =  OrderConfig::getParameter(\App\Configs\Settings::KEY_POSITION_RESERVE);
        if (!$param || $property['key'] != OrderConfig::KEY_POSITION_COUNT){
            return;
        }
        //при изменении количества (если заказ уже оформлен!!!)
        $order = $position->getItem();
        $order_status = $order['properties'][OrderConfig::KEY_ORDER_STATUS]['value_key'];
        if ($order_status == OrderConfig::STATUS_TMP){
            return;
        }
        $new_count = $position[OrderConfig::KEY_POSITION_COUNT];
        $count_modify = $new_count - (!empty($old_values['value']) ? $old_values['value'] : 0);
        self::changeEntityReserve($position, $count_modify);
    }
    public static function changeEntityReserve(OrderItem $position, $modify){
        if (empty($modify)){
            return;
        }
        $entity = $position[OrderConfig::KEY_POSITION_ENTITY];
        $ent_reserve_prop = PropertyFactory::getSingleByKey(CatalogConfig::KEY_VARIANT_COUNT_RESERVED, $entity->getType()->getId());
        //обновили резервы
        $ent_reserve = $entity[CatalogConfig::KEY_VARIANT_COUNT_RESERVED] + $modify;
        $entity->updateValue($ent_reserve_prop['id'], $ent_reserve < 0 ? 0 : $ent_reserve);
        $ent_count_prop = PropertyFactory::getSingleByKey(CatalogConfig::KEY_VARIANT_COUNT, $entity->getType()->getId());
        //обновили количество
        $ent_count = $entity[CatalogConfig::KEY_VARIANT_COUNT] - $modify;
        $entity->updateValue($ent_count_prop['id'], $ent_count < 0 ? 0 : $ent_count);
        //теперь надо обновить время резерва заказа
        $order = $position->getItem();
        $order_reserve_prop = PropertyFactory::getSingleByKey(OrderConfig::KEY_ORDER_RESERVE_DATE, $order->getType()->getId());
        $order->updateValue($order_reserve_prop['id'], date('d.m.Y H:i:s'));
    }
}
