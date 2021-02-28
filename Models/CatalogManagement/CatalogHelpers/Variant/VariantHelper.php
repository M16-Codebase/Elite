<?php
/**
 * Заглушка
 *
 * @author Alexander Shulman
 */
namespace Models\CatalogManagement\CatalogHelpers\Variant;
use Models\CatalogManagement\Variant;
use Models\CatalogManagement\CatalogHelpers\Interfaces\iVariantDataProvider;
use Models\CatalogManagement\Properties\Property;
abstract class VariantHelper implements iVariantDataProvider{
    protected static $i = NULL;
    /**
     * Список полей конкретного хелпера
     * @var array 
     */
    protected static $fieldsList = array();
    /**
     * Сюда сохраняем то, что надо подгрузить в формате variant_id => данные
     * @var array 
     */
    private $loadItemsQuery = array();
    /**
     * Сюда сохраняем уже загруженные данные в формате variant_id => array (данные)
     * @var array 
     */
    private $dataCache = array();

    /**
     * @return static
     */
    public static function factory(){
        if (empty (static::$i)){
            static::$i = new static();
        }
        return static::$i;
    }
	protected function __construct(){
        Variant::addDataProvider($this);
	}
    public function fieldsList(){return static::$fieldsList;}
    public function get(Variant $v, $field){}
    public function onLoad(Variant $v, $propertiesBySegments = NULL){}
    public function onPropertyLoad(Variant $v, &$propertiesBySegments){}
    public function preCreate($item_id, &$errors, $propValues, $segment_id){}
    public function onCreate($variant_id, $segment_id){}
    public function preUpdate($updateKey, Variant $variant, &$params, &$properties, $segment_id, &$errors){}
    public function onUpdate($updateKey, Variant $variant, $segment_id, $updatedProperties){}
	public function onValueChange($action, Variant $variant, Property $property, $v_id, $old_values, $additional_data, $segment_id){}
    public function preDelete(Variant $variant, &$errors = array()){}
    public function onDelete($id, $entity, $remove_from_db){}
    public function onCleanup(){}
    public function onClearCache($variant_id = NULL){
        if (empty($variant_id)){
            if (!empty($this->loadItemsQuery)){
                $this->loadItemsQuery = array();
            }
            if (!empty($this->dataCache)){
                $this->dataCache = array();
            }
        }else{
            if (!empty($this->loadItemsQuery[$variant_id])){
                unset($this->loadItemdQuery[$variant_id]);
            }
            if (!empty($this->dataCache[$variant_id])){
                unset($this->dataCache[$variant_id]);
            }
        }
        if (!empty($this->core)){
            $this->core->onClearCache($variant_id);
        }
    }
    protected function isValueSame($action, Property $property, Variant $entity, $v_id, $old_values, $segment_id = NULL){
        return \Models\CatalogManagement\CatalogHelpers\CatalogPosition\CatalogPositionHelper::factory()->isValueSame($action, $property, $entity, $v_id, $old_values, $segment_id);
    }
}
