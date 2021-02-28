<?php
/**
 * Простой паттерн делегирования на \Models\CatalogManagement\CatalogHelpers\CatalogPosition\ValueComment
 *
 * @author olga
 */
namespace Models\CatalogManagement\CatalogHelpers\Variant;
use Models\CatalogManagement\Variant;
use Models\CatalogManagement\CatalogHelpers\Interfaces\iVariantDataProvider;
class ValueComment extends VariantHelper{
    /**
     * @var \Models\CatalogManagement\CatalogHelpers\CatalogPosition\ValueComment
     */
    private $core = NULL;
    protected static $i = NULL;
	protected function __construct(){
        parent::__construct();
        $this->core = \Models\CatalogManagement\CatalogHelpers\CatalogPosition\ValueComment::factory();
	}
    /**
     * предупреждение, что данные для указанных Variants попали в кеш данных чтобы можно было подготовить доп. данные
     * @param \Models\CatalogManagement\Variant $v
     * @param array $propertiesBySegments свойства по сегментам
     */
    public function onLoad(Variant $v, $propertiesBySegments = NULL){
        $this->core->onLoad($v, $propertiesBySegments);
    }
    public function onPropertyLoad(Variant $v, &$propertiesBySegments){
        return $this->core->onPropertyLoad($v, $propertiesBySegments);
    }
    private function loadData(){
        return $this->core->loadData();
    }

    public function onUpdate($updateKey, Variant $variant, $segment_id, $updatedProperties){
        return $this->core->onUpdate($updateKey, $variant, $segment_id, $updatedProperties);
    }

    public function preUpdate($updateKey, Variant $variant, &$params, &$properties, $segment_id, &$errors){
        return $this->core->preUpdate($updateKey, $variant, $params, $properties, $errors);
    }
	
	public function onValueChange($action, Variant $variant, \Models\CatalogManagement\Properties\Property $property, $v_id, $old_values, $additional_data, $segment_id){
		return $this->core->onValueChange($action, $variant, $property, $v_id, $old_values, $additional_data, $segment_id);
	}
    public function onCleanup(){
		return $this->core->onCleanup();
	}
}
?>