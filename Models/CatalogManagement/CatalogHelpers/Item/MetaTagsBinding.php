<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 13.07.15
 * Time: 19:33
 */

namespace Models\CatalogManagement\CatalogHelpers\Item;


use Models\CatalogManagement\Item;

class MetaTagsBinding extends ItemHelper
{
    protected static $i = NULL;
    /**
     * @var \Models\CatalogManagement\CatalogHelpers\CatalogPosition\MetaTagBinding
     */
    private $core = NULL;

    protected function __construct(){
        $this->core = \Models\CatalogManagement\CatalogHelpers\CatalogPosition\MetaTagBinding::factory();
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
        $this->core->onUpdate($updateKey, $item, $segment_id, $updatedProperties);
    }
    public function preUpdate($updateKey, Item $item, &$params, &$properties, $segment_id, &$errors){
        $this->core->preUpdate($updateKey, $item, $params, $properties, $segment_id, $errors);
    }
    public function onValueChange($action, Item $item, \Models\CatalogManagement\Properties\Property $property, $v_id, $old_values, $additional_data, $segment_id){
        $this->core->onValueChange($action, $item, $property, $v_id, $old_values, $additional_data, $segment_id);
    }

    public function onDelete($id, $entity, $remove_from_db){}
    public function onCleanup(){
        $this->core->onCleanup();
    }

}