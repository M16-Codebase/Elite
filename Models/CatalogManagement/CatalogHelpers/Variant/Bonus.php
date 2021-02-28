<?php
/**
 * Бонус, который начисляется за покупку этого товара
 * в get только для текущего пользователя, если надо увидеть бонусы для другого пользователя, пользуемся getVariantBonus()
 *
 * @author olga
 */
namespace Models\CatalogManagement\CatalogHelpers\Variant;
use Models\CatalogManagement\Variant;
class Bonus extends VariantHelper{
    protected static $i = NULL;
    protected static $fieldsList = array('bonus');
    /**
     * возвращает значение дополнительного поля
     * не записываю результат в статику, т.к. значения варианта могут поменяться 
     * в зависимости от региона, поэтому каждый раз будем высчитывать заново
     */
    public function get(Variant $v, $field){
        if (!in_array($field, $this->fieldsList())){
            throw new \InvalidArgumentException('Неверное название дополнительного поля');
        }
        $account = \App\Builder::getInstance()->getAccount();
        $user = $account->getUser();
        return $this->getVariantBonus($v, $user);
    }
    /**
     * Посмотреть бонусы на варианты для любого пользователя
     * @param \Models\CatalogManagement\Variant $variant
     * @param \App\Auth\Users\RegistratedUser $user
     * @return int
     */
    public function getVariantBonus(Variant $variant, $user = NULL, $count = 1){
        if (empty($user)){
            $user_price = $variant[\App\Configs\CatalogConfig::KEY_VARIANT_PRICE];
        } else {
            $user_price = $user->getOrderPositionPrice($variant);//узнаем цену у конкретного пользователя
        }
        return floor(\Models\OrderManagement\Order::getBonus($user_price, $count, $user, $variant['segment_id']));
    }
}