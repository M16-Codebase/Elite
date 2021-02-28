<?php
namespace Models\CatalogManagement\CatalogHelpers\Settings;

use App\Configs\CatalogConfig;
use App\Configs\Settings;
use App\Configs\OrderConfig;
use Models\CatalogManagement\Properties\Factory AS PropertyFactory;
use Models\CatalogManagement\Properties\Property;
use Models\CatalogManagement\Type;
/**
 * Действия при смене некоторых настроек
 *
 * @author pochepochka
 */
class OrderChanger extends SettingsHelper{
    protected static $i = NULL;
    public function onValueChange($action, \Models\CatalogManagement\Item $item, Property $property, $v_id, $old_values, $additional_data, $segment_id) {
        $item_type = $item->getType();
        if ($this->isValueSame($action, $property, $item, $v_id, $old_values) || $item_type['key'] != CatalogConfig::CONFIG_ORDERS_KEY){
            return;
        }
        if ($property['set']){
            if (!empty($item['properties'][$property['key']]['val_id'])){
                $num = array_search($v_id, $item['properties'][$property['key']]['val_id']);
                $new_value = !empty($item['properties'][$property['key']]['value_key'][$num]) ? $item['properties'][$property['key']]['value_key'][$num] : NULL;
            }else{
                $new_value = NULL;
            }
        }else{
            $new_value = $item['properties'][$property['key']]['value_key'];
        }
        $order_catalog = Type::getByKey(CatalogConfig::ORDERS_KEY);
        //при смене типа клиентов (типа доставки и т.д.), надо убрать лишние\добавить нужные
        if (in_array($property['key'], 
                array(
                    Settings::KEY_PERSON_TYPE, 
                    Settings::KEY_DELIVER_TYPES, 
                    Settings::KEY_PAY_TYPE_FIZ, 
                    Settings::KEY_PAY_TYPE_ORG
                )
            ) 
        ){
            if ($property['set']){
                if (!empty($old_values['val_id'])){
                    $num = array_search($v_id, $old_values['val_id']);
                    $old = !empty($old_values['value_key'][$num]) ? $old_values['value_key'][$num] : NULL;
                }else{
                    $old = NULL;
                }
            }else{
                $old = $old_values['value_key'];
            }
            $this->change($order_catalog, $property, $new_value, $old);
        }
    }
    /**
     * @TODO при изменении параметра "На что можно потратить бонусы" надо менять доступ к модулю подарочных карт и трате бонусов на заказ
     * Основной механизм изменения свойств заказов в зависимости от изменения настроек ( по сути прячем лишнее либо добавляем нужное)
     * @param Type $order_catalog
     * @param Property $property
     * @param type $new_value
     * @param type $old_value
     */
    private function change(Type $order_catalog, Property $property, $new_value, $old_value){
		$children = $order_catalog->getChildren(array(Type::STATUS_HIDDEN, Type::STATUS_VISIBLE));
		$chByKey = array();
		foreach ($children as $ch){
			$chByKey[$ch['key']] = $ch;
		}
        if ($property['key'] == Settings::KEY_PERSON_TYPE){
            //меняем видимость типов
            if ($new_value == 'fiz'){
                $chByKey[CatalogConfig::CATALOG_KEY_ORDERS_FIZ]->setStatus(Type::STATUS_VISIBLE);
            }elseif ($new_value == 'org'){
                $chByKey[CatalogConfig::CATALOG_KEY_ORDERS_ORG]->setStatus(Type::STATUS_VISIBLE);
            }elseif (is_null($new_value)){
                if ($old_value == 'fiz'){
                    $chByKey[CatalogConfig::CATALOG_KEY_ORDERS_FIZ]->setStatus(Type::STATUS_HIDDEN);
                }elseif($old_value == 'org'){
                    $chByKey[CatalogConfig::CATALOG_KEY_ORDERS_ORG]->setStatus(Type::STATUS_HIDDEN);
                }
            }
        }else{
            //меняем видимость свойств
			$in_type = $order_catalog;
			$prop_key = $property['key'];
			if ($property['key'] == Settings::KEY_PAY_TYPE_FIZ){
				$in_type = $chByKey[CatalogConfig::CATALOG_KEY_ORDERS_FIZ];
				$prop_key = OrderConfig::KEY_ORDER_PAY_TYPE;
			}elseif($property['key'] == Settings::KEY_PAY_TYPE_ORG){
				$in_type = $chByKey[CatalogConfig::CATALOG_KEY_ORDERS_ORG];
				$prop_key = OrderConfig::KEY_ORDER_PAY_TYPE;
			}
            $order_prop = PropertyFactory::getSingleByKey($prop_key, $in_type['id']);
            if (is_null($new_value)){
                $enum_id = $order_prop->getEnumIdByKey($old_value);
				foreach ($chByKey as $t){
					$t->setSingleEnumUse($order_prop['id'], $enum_id, FALSE);
				}
            }else{
                $enum_id = $order_prop->getEnumIdByKey($new_value);
				foreach ($chByKey as $t){
					$t->setSingleEnumUse($order_prop['id'], $enum_id, TRUE);
				}
            }
        }
    }
}
