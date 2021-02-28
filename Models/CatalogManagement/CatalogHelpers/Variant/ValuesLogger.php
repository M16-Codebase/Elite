<?php
namespace Models\CatalogManagement\CatalogHelpers\Variant;

use Models\CatalogManagement\Variant;
use Models\CatalogManagement\Properties\Property;
/**
 * Логирование изменений
 */
class ValuesLogger extends VariantHelper{
    private $core = NULL;
    protected static $i = NULL;
	protected function __construct(){
        parent::__construct();
        $this->core = \Models\CatalogManagement\CatalogHelpers\CatalogPosition\ValuesLogger::factory();
	}
    
    public function onCreate($variant_id, $segment_id){
        $this->core->onCreate($variant_id, 'variant', $segment_id);
    }

    public function onUpdate($updateKey, Variant $variant, $segment_id, $updatedProperties){
        return $this->core->onUpdate($updateKey, $variant, $segment_id, $updatedProperties);
    }

    public function preUpdate($updateKey, Variant $variant, &$params, &$properties, $segment_id, &$errors){
        return $this->core->preUpdate($updateKey, $variant, $params, $properties, $segment_id, $errors);
    }
	
    public function onDelete($variant_id, $entity, $remove_from_db){
        $this->core->onDelete($variant_id, 'variant', $entity, $remove_from_db);
    }
    public function onValueChange($action, Variant $variant, Property $property, $v_id, $old_values, $additional_data, $segment_id){
        $this->core->onValueChange($action, $variant, $property, $v_id, $old_values, $additional_data, $segment_id);
    }
}