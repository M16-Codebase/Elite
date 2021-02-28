<?php
namespace Models\CatalogManagement\CatalogHelpers\Order;
use Models\CatalogManagement\CatalogHelpers\Item\ItemHelper;

/**
 * Заглушка
 *
 * @author Alexander Shulman
 */
abstract class OrderHelper extends ItemHelper{
    protected static $i = NULL;
	protected function __construct(){
        \Models\CatalogManagement\Positions\Order::addDataProvider($this);
	}
}
