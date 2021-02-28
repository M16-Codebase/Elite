<?php
/**
 * 
 */
namespace Models\CatalogManagement\CatalogHelpers\Item;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\CatalogHelpers\Item\ItemHelper;
class SegmentMask extends ItemHelper{
    protected static $i = NULL;
    /**
     *
     * @var \Models\CatalogManagement\CatalogHelpers\CatalogPosition\SegmentEnumTitles
     */
    private $core = NULL;
	protected function __construct(){
        $this->core = \Models\CatalogManagement\CatalogHelpers\CatalogPosition\SegmentMask::factory();
        parent::__construct();
	}
    /**
     * предупреждение, что данные для указанных Items попали в кеш данных чтобы можно было подготовить доп. данные
     * @param \Models\CatalogManagement\Item $i
     * @param array $propertiesBySegments свойства по сегментам
     */
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

}
?>