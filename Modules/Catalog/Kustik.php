<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 24.03.15
 * Time: 18:13
 */

namespace Modules\Catalog;


use App\Configs\CatalogConfig;
use Models\CatalogManagement\Item as ItemEntity;
use Models\CatalogManagement\Type as TypeEntity;

class Kustik extends CatalogPublic{
    const DEFAULT_CATALOG_KEY = CatalogConfig::KUSTIK_KEY;

    public function index(){
        return 'index';
    }

    public function items(){
        $segment_id = \App\Segment::getInstance()->getDefault(true);
        $type = TypeEntity::getById($this->routeTail, $segment_id);
        var_dump($type['title']);return 'items';
    }

    public function viewItem(){
        $segment_id = \App\Segment::getInstance()->getDefault(true);
        $item = ItemEntity::getById($this->routeTail, $segment_id);
        var_dump($item['title'], $item->getType()['title']);return 'viewItem';
    }
} 