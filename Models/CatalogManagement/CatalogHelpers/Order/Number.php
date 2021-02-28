<?php
namespace Models\CatalogManagement\CatalogHelpers\Order;

use App\Configs\OrderConfig;
/**
 * Автонумератор оформленных заказов
 *
 * @author olya
 */
class Number extends OrderHelper{
    public function onValueChange($action, \Models\CatalogManagement\Item $item, \Models\CatalogManagement\Properties\Property $property, $v_id, $old_values, $additional_data, $segment_id) {
        if ($property['key'] != OrderConfig::KEY_ORDER_STATUS){
            return;
        }
        $new_status = $item['properties'][OrderConfig::KEY_ORDER_STATUS]['value_key'];
		$old_status = !empty($old_values) ? $old_values['value_key'] : NULL;
        if (!empty($item[OrderConfig::KEY_ORDER_NUMBER]) || !($new_status == OrderConfig::STATUS_NEW && $old_status == OrderConfig::STATUS_TMP)){
            return;
        }
        $db = \App\Builder::getInstance()->getDB();
        $property = \Models\CatalogManagement\Properties\Factory::getSingleByKey(OrderConfig::KEY_ORDER_NUMBER, $item->getType()->getCatalog()->getId());
        $last_number = $db->query('SELECT MAX(`value`) FROM ?# WHERE `property_id` = ?d', $property['table'], $property['id'])->getCell();
        if (empty($last_number)){
            $last_number = 0;
        }
        $item->updateValue($property['id'], $last_number+1);
    }
}
