<?php
/**
 * Заказы
 *
 * @author olya
 */
namespace Models\CatalogManagement\Positions;

use App\Configs\OrderConfig;
class Order extends \Models\CatalogManagement\Item{
    const ENTITY_TITLE_KEY = 'id';
    /**
     * Свои хелперы
     * @var array
     */
    static protected $dataProviders = array();
    static protected $dataProvidersByFields = array();
    /**
	 * Можно ли оплачивать заказ
	 * @return boolean
	 */
	public function canPayed(){
		$paySystems = \App\Payment::getSystems(TRUE, TRUE);
		if ($this['pay_type'] == OrderConfig::PAY_TYPE_ONLINE
            && in_array($this['status'], OrderConfig::$statusToPayed)
            && array_key_exists($this['pay_type'], $paySystems)
        ){
			return TRUE;
		}
		return FALSE;
	}
    /**
     * Забрать позиции заказа
     * @return OrderItem[]
     */
    public function getPositions(){
        return $this->getVariants(\Models\CatalogManagement\Variant::S_PUBLIC);
    }
    /**
     * Добавить позицию заказа, если такая уже есть, отдает уже существующую
     * @param \Models\CatalogManagement\iOrderData $entity
     * @param float $count количество (например, 0.5 метра)
     * @param array $errors
     * @return boolean
     */
    public function addPosition(\Models\CatalogManagement\iOrderData $entity, $count = NULL, &$errors = NULL){
        $data = $entity->getDataForOrder();
        $positions = $this->getPositions();
        $position_class = get_class($entity);
        $position_type = strtolower(substr(get_class($entity), strrpos($position_class, '\\')+1));
        $countProp = \Models\CatalogManagement\Properties\Factory::getSingleByKey(OrderConfig::KEY_POSITION_COUNT, $this->getType()->getId());
        if (!empty($positions)){
            foreach ($positions as $pos){
                /* @var $pos Position */
                if ($pos['entity_id'] == $data['entity'] 
                    && strtolower($pos['properties'][OrderConfig::KEY_POSITION_ENTITY_TYPE]['value_key']) == $position_type){
                    if ($pos[OrderConfig::KEY_POSITION_COUNT] != $count && ($count > $pos[OrderConfig::KEY_POSITION_COUNT])){
                        $pos->updateValue($countProp['id'], $count, $errors);
                    }
                    return NULL;
                }
            }
        }
        $data[OrderConfig::KEY_POSITION_COUNT] = !empty($count) ? $count : 1;
        //надо подготовить данные к обновлению
        $position_id = $this->createVariant(self::S_PUBLIC, OrderItem::prepareUpdateData($this['type_id'], $data), $errors);
        if (!empty($errors)){
            return FALSE;
        }
        $position = OrderItem::getById($position_id);
        $position->update(array('status' => static::S_PUBLIC), OrderItem::prepareUpdateData($this['type_id'], $data, NULL, $position->getSegmentProperties(0)));
        return $position;
    }
    /**
     * удалить позицию заказа
     * @param int $pos_id
     * @param array $errors
     * @return NULL
     */
    public function removePosition($pos_id, &$errors = array()){
        return $this->delVariant($pos_id, $errors, true);
    }
    /**
     * Проверяет, заказ оформлен на юр или физ
     * @param string $person_type org|fiz
     * @return boolean
     * @throws \Exception
     */
    public function isPerson($person_type){
        if ($person_type != 'fiz' && $person_type != 'org'){
            throw new \Exception('Неверно передан параметр $person_type. Должно быть одно из: org, fiz');
        }
        $parent_key = $this->getType()->getKey();
        return $parent_key == ($person_type == 'fiz' ? \App\Configs\CatalogConfig::CATALOG_KEY_ORDERS_FIZ : \App\Configs\CatalogConfig::CATALOG_KEY_ORDERS_ORG);
    }
    /**
     * Возвращает объект платежной системы
     * @return \Models\Payments\iPayment
     */
    public function getPayment(){
		if (!$this->canPayed()){
			return NULL;
		}
        return \App\Payment::get($this);
    }
    public function offsetGet($offset) {
        if ($offset == 'positions'){
            return $this->getPositions();
        }elseif ($offset == OrderConfig::KEY_ORDER_PAY_SYSTEM_METHOD . '_title'){
            $payment = \App\Payment::get($this);//не используем $this->getPayment(), т.к. нам надо получить название системы оплаты в независимости от стауса заказа
            if (empty($payment)){
                return NULL;
            }
            $payMethods = $payment->getPayMethods();
            if (!isset($payMethods[$this[OrderConfig::KEY_ORDER_PAY_SYSTEM_METHOD]])){
                return $this[OrderConfig::KEY_ORDER_PAY_SYSTEM_METHOD];
            }
            return !empty($payMethods[$this[OrderConfig::KEY_ORDER_PAY_SYSTEM_METHOD]]['title']) ? $payMethods[$this[OrderConfig::KEY_ORDER_PAY_SYSTEM_METHOD]]['title'] : $this[OrderConfig::KEY_ORDER_PAY_SYSTEM_METHOD];
        }
        return parent::offsetGet($offset);
    }
}
