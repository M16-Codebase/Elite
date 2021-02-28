<?php
namespace Models\CatalogManagement\CatalogHelpers\OrderItem;

use Models\CatalogManagement\Variant;
use App\Configs\OrderConfig;
/**
 * Работаем с бонусами позиции заказа
 * @author olga
 */
class Bonus extends OrderItemHelper{
    protected static $i = NULL;
	public function onValueChange($action, Variant $variant, \Models\CatalogManagement\Properties\Property $property, $v_id, $old_values, $additional_data, $segment_id) {
		if ($property['key'] != OrderConfig::KEY_POSITION_PRICE && $property['key'] != OrderConfig::KEY_POSITION_COUNT){
			return;
		}
		$order = $variant->getItem();
		$bonus = self::getBonus(
			$variant[OrderConfig::KEY_POSITION_PRICE], 
			$variant[OrderConfig::KEY_POSITION_COUNT], 
			$order[OrderConfig::KEY_ORDER_USER], 
			$segment_id, 
			!empty($order['bonus_ratio']) ? $order['bonus_ratio'] : NULL
		);
		//свойство
		$property_bonus_id = $variant['properties'][OrderConfig::KEY_POSITION_BONUS]['id'];
		//записываем
		$variant->updateValue($property_bonus_id, $bonus);//т.к. цена уже изменилась, то второй раз в это условие не попадаем
	}
    /**
	 * Количество баллов за позицию заказа (указываем цену и количество)
	 * !!!ВНИМАНИЕ, число отдается без округления, чтобы при сумме всё сходилось
	 * @param type $price
	 * @param type $count
	 * @param \App\Auth\Users\RegistratedUser $user
	 * @param type $segment_id
	 * @return type
	 */
	public static function getBonus($price, $count, $user = NULL, $segment_id = NULL, $ratio = NULL){
        if (is_null($ratio)){
            $ratio = \Models\CatalogManagement\CatalogHelpers\Order\Bonus::getBonusRatio($user, $segment_id);
        }
		return $price * $count * ($ratio / 100);
	}
}