<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 06.07.15
 * Time: 15:52
 */

namespace Models\CatalogManagement\CatalogHelpers\Review;


use Models\CatalogManagement\CatalogHelpers\Item\ItemHelper;
use Models\CatalogManagement\Positions\Review;

class ReviewHelper extends ItemHelper{
    protected static $i = NULL;
    protected function __construct(){
        Review::addDataProvider($this);
    }

}