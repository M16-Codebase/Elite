<?php
namespace Models\CatalogManagement\CatalogHelpers\OrderItem;

use Models\CatalogManagement\CatalogHelpers\Variant\VariantHelper;
/**
 * 
 */
abstract class OrderItemHelper extends VariantHelper{
    protected static $i = NULL;
	protected function __construct(){
        \Models\CatalogManagement\Positions\OrderItem::addDataProvider($this);
	}
}
