<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 25.06.15
 * Time: 17:32
 */

namespace Modules\Catalog;


use Models\CatalogManagement\Type as TypeEntity;

class Dynamic extends CatalogDynamic{
    const DEFAULT_CATALOG_KEY = 'dynamic';

    public function index(){
//        return $this->notFound();
    }

    public function items(){
        $type_id = $this->routeTail;
        if ($type_id == TypeEntity::DEFAULT_TYPE_ID){
            return $this->redirect($this->getModuleUrl());
        }
        $segment = \App\Segment::getInstance()->getDefault(true);
        $type = TypeEntity::getById($type_id, $segment['id']);
        if (empty($type)){
            return $this->notFound();
        }
        $main_catalog = TypeEntity::getByKey($type->getCatalog()['dynamic_for']);
        $rules = $this->getSearchRules($type);
        $search = \Models\CatalogManagement\Search\CatalogSearch::factory($main_catalog['key'])->setRules($rules)->searchItems();
    }
}