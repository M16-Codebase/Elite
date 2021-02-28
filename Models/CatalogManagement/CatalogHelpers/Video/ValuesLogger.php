<?php
/**
 * Логирование изменения свойств в базу
 */
namespace Models\CatalogManagement\CatalogHelpers\Video;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Properties\Property;
class ValuesLogger extends VideoHelper{
    protected static $i = NULL;
    /**
     *
     * @var \Models\CatalogManagement\CatalogHelpers\CatalogPosition\ValuesLogger
     */
    private $core = NULL;
	protected function __construct(){
		parent::__construct();
        $this->core = \Models\CatalogManagement\CatalogHelpers\CatalogPosition\ValuesLogger::factory();
	}
    public function onUpdate($updateKey, Item $item, $segment_id, $updatedProperties){
        return $this->core->onUpdate($updateKey, $item, $segment_id, $updatedProperties);
    }
    public function preUpdate($updateKey, Item $item, &$params, &$properties, $segment_id, &$errors){
        return $this->core->preUpdate($updateKey, $item, $params, $properties, $segment_id, $errors);
    }
    public function onCreate($item_id, $segment_id){
        $this->core->onCreate($item_id, 'item', $segment_id);
    }
    public function onDelete($item_id, $entity, $remove_from_db){
        $this->core->onDelete($item_id, 'item', $entity, $remove_from_db);
    }
    public function onValueChange($action, Item $item, Property $property, $v_id, $old_values, $additional_data, $segment_id){
        $this->core->onValueChange($action, $item, $property, $v_id, $old_values, $additional_data, $segment_id);
    }
}
