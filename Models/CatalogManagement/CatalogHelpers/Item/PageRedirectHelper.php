<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 29.06.15
 * Time: 17:48
 */

namespace Models\CatalogManagement\CatalogHelpers\Item;


use Models\CatalogManagement\Item;

class PageRedirectHelper extends ItemHelper{
    protected static $i = NULL;
    /**
     *
     * @var \Models\CatalogManagement\CatalogHelpers\CatalogPosition\PageRedirectHelper
     */
    private $core = NULL;
    protected function __construct(){
        $this->core = \Models\CatalogManagement\CatalogHelpers\CatalogPosition\PageRedirectHelper::factory();
        parent::__construct();
    }
    public function onLoad(Item $i, $propertiesBySegments = NULL){
        $this->core->onLoad($i, $propertiesBySegments);
    }
    public function onPropertyLoad(Item $i, &$propertiesBySegments){
        return $this->core->onPropertyLoad($i, $propertiesBySegments);
    }
    private function loadData(){
        return $this->core->loadData();
    }
    public function onUpdate($updateKey, Item $item, $segment_id, $updatedProperties){
        return $this->core->onUpdate($updateKey, $item, $segment_id, $updatedProperties);
    }
    public function preUpdate($updateKey, Item $item, &$params, &$properties, $segment_id, &$errors){
        return $this->core->preUpdate($updateKey, $item, $params, $properties, $segment_id, $errors);
    }
    public function onValueChange($action, Item $item, \Models\CatalogManagement\Properties\Property $property, $v_id, $old_values, $additional_data, $segment_id){
        return $this->core->onValueChange($action, $item, $property, $v_id, $old_values, $additional_data, $segment_id);
    }

    public function onDelete($id, $entity, $remove_from_db){}
    public function onCleanup(){
        return $this->core->onCleanup();
    }
}