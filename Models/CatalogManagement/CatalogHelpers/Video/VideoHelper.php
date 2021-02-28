<?php
/**
 * Заглушка
 *
 * @author Alexander Shulman
 */
namespace Models\CatalogManagement\CatalogHelpers\Video;
use Models\CatalogManagement\CatalogHelpers\Item\ItemHelper;
use Models\CatalogManagement\Positions\Video;

abstract class VideoHelper extends ItemHelper{
    protected static $i = NULL;
    protected function __construct(){
        Video::addDataProvider($this);
    }
}