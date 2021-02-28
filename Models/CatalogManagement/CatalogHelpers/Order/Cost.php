<?php
namespace Models\CatalogManagement\CatalogHelpers\Order;

use Models\CatalogManagement\Item;
use App\Configs\OrderConfig;
/**
 * Общая стоимость заказа
 *
 * @author olga
 */
class Cost extends OrderHelper{
    const NDS_PERCENT = 18;//если нигде не указан, то считем его равным 18%
    protected static $i = NULL;
    private $dataCache = array();
    protected static $fieldsList = array('total_cost', 'positions_price', 'real_positions_price', 'nds', 'commission_online_pay', 'commission_online_percent');
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Item $i, $field){
        if (!in_array($field, $this->fieldsList())){
            throw new \InvalidArgumentException('Неверное название дополнительного поля');
        }
        if (empty($this->dataCache[$i['id']])){
            $positions = $i->getVariants();
            $this->dataCache[$i['id']]['real_positions_price'] = 0;
            $this->dataCache[$i['id']]['positions_price'] = 0;
            foreach ($positions as $pos){
                $this->dataCache[$i['id']]['real_positions_price'] += $pos['real_price'];//реальная цена (со скидками и всякой фигней)
                $this->dataCache[$i['id']]['positions_price'] += $pos['price'];//цена за товар, объявленная при покупке
            }
            $total_cost = $this->dataCache[$i['id']]['real_positions_price'];
            $nds_percent = self::NDS_PERCENT;//@TODO брать из конфига
            $nds = $total_cost * (1-(1/(1+($nds_percent/100))));
            //добавляем стоимость доставки
            if (!empty($i[OrderConfig::KEY_ORDER_DELIVERY_PRICE])){
                $total_cost += $i[OrderConfig::KEY_ORDER_DELIVERY_PRICE];
            }
            //добавляем процент комиссии (всегда в конце)
            if (OrderConfig::getParameter(\App\Configs\Settings::KEY_PAY_ONLINE_COMMISION_PLUS)
                && $i[OrderConfig::KEY_ORDER_PAY_TYPE] == OrderConfig::PAY_TYPE_ONLINE){
                $payments = $i->getPayment();
                if (!empty($payments)){
                    $methods = $payments->getPayMethods();//методы оплаты в платежной системе
                    //commission_project - это процент, который снимут с продавца, т.е. деньги за заказ x = y-y*(p/100), где y - сумма, которую должен заплатить пользователь.
                    if (!empty($methods[$i[OrderConfig::KEY_ORDER_PAY_SYSTEM_METHOD]]) && !empty($methods[$i[OrderConfig::KEY_ORDER_PAY_SYSTEM_METHOD]]['commission_project'])){
                        $percent = $methods[$i[OrderConfig::KEY_ORDER_PAY_SYSTEM_METHOD]]['commission_project'];
                        $new_total_cost = $total_cost / (1 - ($percent / 100));
                        $pay_online_commision = $new_total_cost - $total_cost;
                    }
                }
            }
            $this->dataCache[$i['id']]['total_cost'] = $total_cost;
            $this->dataCache[$i['id']]['nds'] = round($nds, 2);
            $this->dataCache[$i['id']]['commission_online_pay'] = !empty($pay_online_commision) ? $pay_online_commision : 0;
            $this->dataCache[$i['id']]['commission_online_percent'] = !empty($percent) ? $percent : 0;
        }
        if (!isset($this->dataCache[$i['id']][$field])){
            throw new \LogicException('Проверка на возможность запросить поле уже была, рассинхрон в списке полей и отдаваемых полях. Поле: ' . $field);
        }
        return $this->dataCache[$i['id']][$field];
    }
}