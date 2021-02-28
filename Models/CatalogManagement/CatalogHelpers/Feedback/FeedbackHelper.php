<?php
namespace Models\CatalogManagement\CatalogHelpers\Feedback;
use Models\CatalogManagement\CatalogHelpers\Item\ItemHelper;
use Models\CatalogManagement\Positions\Feedback;

/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 21.07.15
 * Time: 15:32
 */
abstract class FeedbackHelper extends ItemHelper
{
    protected static $i = NULL;
    protected function __construct(){
        Feedback::addDataProvider($this);
    }

}