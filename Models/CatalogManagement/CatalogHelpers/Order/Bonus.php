<?php
namespace Models\CatalogManagement\CatalogHelpers\Order;

use Models\CatalogManagement\Item;
use App\Configs\OrderConfig;
/**
 * Бонус, который начисляется за весь заказ
 * в get только для текущего пользователя, если надо увидеть бонусы для другого пользователя, пользуемся getVariantBonus()
 *
 * @author olga
 */
class Bonus extends OrderHelper{
    protected static $i = NULL;
    protected static $fieldsList = array('bonus');
    /**
     * возвращает значение дополнительного поля
     * не записываю результат в статику, т.к. значения варианта могут поменяться 
     * в зависимости от региона, поэтому каждый раз будем высчитывать заново
     */
    public function get(Item $i, $field){
        if (!in_array($field, $this->fieldsList())){
            throw new \InvalidArgumentException('Неверное название дополнительного поля');
        }
        $positions = $i->getVariants();
        $bonus = 0;
        foreach ($positions as $pos){
            $bonus += $pos['bonus'];
        }
        return $bonus;
    }
	public function onValueChange($action, Item $item, \Models\CatalogManagement\Properties\Property $property, $v_id, $old_values, $additional_data, $segment_id) {
		if ($property['key'] != OrderConfig::KEY_ORDER_STATUS || $action != 'edit'){
			return;
		}
		/* @var $user \App\Auth\Users\RegistratedUser */
		$user = $item[OrderConfig::KEY_ORDER_USER];
		if (empty($user)){
			return;
		}
		$new_status = $item['properties'][OrderConfig::KEY_ORDER_STATUS]['value_key'];
		$old_status = $old_values['value_key'];
//        var_dump(OrderConfig::BONUS_SPEND_ORDER_PAY, OrderConfig::getParameter(\App\Configs\Settings::KEY_ORDER_BONUS_SPEND));
		//когда выставим заказ в статус "выполнен", можно начислять бонусы
		if ($new_status == OrderConfig::STATUS_COMPLETE){
			$user->increaseBonus($item['bonus'], NULL, OrderConfig::getParameter(\App\Configs\Settings::KEY_ORDER_BONUS_TEXT_ADD_NEW), $error);
			if ($error){
				throw new \Exception($error);
			}
		}elseif($old_status == OrderConfig::STATUS_COMPLETE){//если был выполнен, а поменяли на что-то другое, то бонусы у пользователя надо свистнуть
			$comment = OrderConfig::getParameter(\App\Configs\Settings::KEY_ORDER_BONUS_TEXT_CHANGE_STATUS);
			$replacer = array('{new_status}' => $item[OrderConfig::KEY_ORDER_STATUS], '{old_status}' => $old_values['complete_value']);
			$user->decreaseBonus($item['bonus'], NULL, str_replace(array_keys($replacer), array_values($replacer), $comment), $error);
			if ($error){
				throw new \Exception($error);
			}
		}
		if ($new_status == OrderConfig::STATUS_NEW){//если заказ только что оформили
			//записываем текущее состояние процента бонуса
			$item->updateValueByKey(OrderConfig::KEY_ORDER_BONUS_RATIO, self::getBonusRatio($user, $segment_id));
            //оплата бонусами (оформление заказа)
            if (in_array($old_status, array(OrderConfig::STATUS_TMP, OrderConfig::STATUS_DELETE))
                && array_key_exists(OrderConfig::BONUS_SPEND_ORDER_PAY, OrderConfig::getParameter(\App\Configs\Settings::KEY_ORDER_BONUS_SPEND))
                && !empty($item[OrderConfig::KEY_ORDER_BONUS_SPEND])
                && !empty($user)
            ){
                //снимаем у пользователя
                $user->decreaseBonus($item[OrderConfig::KEY_ORDER_BONUS_SPEND], NULL, str_replace('{order_number}', !empty($item[OrderConfig::KEY_ORDER_NUMBER]) ? $item[OrderConfig::KEY_ORDER_NUMBER] : $item['id'], OrderConfig::getParameter(\App\Configs\Settings::KEY_ORDER_BONUS_TEXT_SPEND)), $error);
                if ($error){
                    throw new \Exception($error);
                }
            }
		}
        //оплата бонусами (отмена заказа)
        if (!empty($user)
            && !empty($item[OrderConfig::KEY_ORDER_BONUS_SPEND])
            &&  array_key_exists(OrderConfig::BONUS_SPEND_ORDER_PAY, OrderConfig::getParameter(\App\Configs\Settings::KEY_ORDER_BONUS_SPEND))
            && (($new_status == OrderConfig::STATUS_DELETE 
                    && $old_status != OrderConfig::STATUS_TMP
                )
                || ($new_status == OrderConfig::STATUS_TMP 
                    && !empty($old_status) 
                    && $old_status != OrderConfig::STATUS_DELETE
                )
            )
        ){
            $user->increaseBonus($item[OrderConfig::KEY_ORDER_BONUS_SPEND], NULL, str_replace('{order_number}', !empty($item[OrderConfig::KEY_ORDER_NUMBER]) ? $item[OrderConfig::KEY_ORDER_NUMBER] : $item['id'], OrderConfig::getParameter(\App\Configs\Settings::KEY_ORDER_BONUS_TEXT_UNSPEND)), $error);
            if ($error){
                throw new \Exception($error);
            }
        }
	}
    /**
     * Узнать процент от суммы заказа, который пойдет в бонусы
     */
    public static function getBonusRatio(\App\Auth\Users\RegistratedUser $user = NULL, $segment_id = NULL){
        if (!empty($user)){
            return $user->getBonusRatio($segment_id);
        } else {
            return OrderConfig::getParameter(\App\Configs\Settings::KEY_ORDER_BONUS_RATIO);
        }
    }
}