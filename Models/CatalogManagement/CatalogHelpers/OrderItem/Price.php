<?php
/**
 * Реальная цена позиции.
 * При распределении цены всего заказа на все позиции, цена будет отличаться.
 * Уникально для каждого проекта
 * @author olga
 */
namespace Models\CatalogManagement\CatalogHelpers\OrderItem;
use Models\CatalogManagement\Variant;
class Price extends OrderItemHelper{
    protected static $i = NULL;
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList(){
        return array('real_price');
    }
    /**
     * возвращает значение дополнительного поля
     * не записываю результат в статику, т.к. значения варианта могут поменяться 
     * в зависимости от региона, поэтому каждый раз будем высчитывать заново
     */
    public function get(Variant $v, $field){
        if (!in_array($field, $this->fieldsList())){
            throw new \InvalidArgumentException('Неверное название дополнительного поля');
        }
        return $v['count'] * $v['price'];//упрощенная схема
    }
}