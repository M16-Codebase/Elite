<?php
/**
 * Заглушка
 *
 * @author Pochepochka
 */
namespace Models\CatalogManagement\CatalogHelpers\Brands;
use Models\CatalogManagement\CatalogHelpers\Item\ItemHelper;
use Models\CatalogManagement\Positions\Brand;

abstract class BrandsHelper extends ItemHelper{
    protected static $i = NULL;
    protected function __construct(){
        Brand::addDataProvider($this);
    }
}