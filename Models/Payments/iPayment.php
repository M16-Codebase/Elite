<?php
namespace Models\Payments;

use Models\CatalogManagement\Positions\Order;
/**
 *
 * @author olya
 */
interface iPayment {
    public static function get(Order $order);
    public static function getPayMethods($usedOnSite = FALSE, $usedInSystem = FALSE);
}
