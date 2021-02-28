<?php
namespace App;

use Models\Payments;
/**
 * Класс для делегирования полномочий нужной системе оплаты
 *
 * @author olga
 */
class Payment {
    const TABLE_PAY_TYPES = 'payment_types';
    const TABLE_PAY_GROUPS = 'payment_types_groups';
    private static function getPaymentClass(){
        $payment = Configs\OrderConfig::getParameter(Configs\Settings::KEY_PAY_ONLINE_SYSTEM);
        $payment_method =  !empty($payment) ? ('Payments\\' . $payment) : NULL;
        return $payment_method;
    }
	/**
	 * Объект системы оплаты
	 * @param \Models\CatalogManagement\Positions\Order $order
	 * @return Payments\iPayment
	 */
	public static function get(\Models\CatalogManagement\Positions\Order $order){
        $payment_method = self::getPaymentClass();
		return !empty($payment_method) ? $payment_method::get($order) : NULL;
	}
	/**
     * Возвращает все возможные для текущей системы оплаты методы оплаты
     * @param boolean $used используется ли на сайте (выставляется в админке)
     * @param boolean $system_used позволяет ли платежная система использовать метод оплаты
     * @return array
     */
	public static function getSystems($used = FALSE, $system_used = FALSE){
        $payment_method = self::getPaymentClass();
		return !empty($payment_method) ? $payment_method::getPayMethods($used, $system_used) : array();
	}
}
