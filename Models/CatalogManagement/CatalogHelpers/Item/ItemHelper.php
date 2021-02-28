<?php
/**
 * Заглушка
 *
 * @author Alexander Shulman
 */
namespace Models\CatalogManagement\CatalogHelpers\Item;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Variant;
use Models\CatalogManagement\CatalogHelpers\Interfaces\iItemDataProvider;
use Models\CatalogManagement\Properties\Property;

abstract class ItemHelper implements iItemDataProvider{
    protected static $i = NULL;
    /**
     * Список доступных полей конкретного хелпера
     * @var array 
     */
    protected static $fieldsList = array();
    /**
     * Сюда сохраняем то, что надо подгрузить в формате item_id => данные
     * @var array 
     */
    private $loadItemsQuery = array();
    /**
     * Сюда сохраняем уже загруженные данные в формате item_id => array (данные)
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
        Item::addDataProvider($this);
	}
    public function fieldsList(){return static::$fieldsList;}
    public function get(Item $i, $field){}
    public function onLoad(Item $i, $propertiesBySegments = NULL){}
    public function onPropertyLoad(Item $v, &$propertiesBySegments){}
    public function preCreate($type_id, $propValues, &$errors, $segment_id){}
    public function onCreate($item_id, $Segment_id){}
    public function onUpdate($updateKey, Item $item, $segment_id, $updatedProperties){}
    public function preUpdate($updateKey, Item $item, &$params, &$properties, $segment_id, &$errors){}
	public function onValueChange($action, Item $item, Property $property, $v_id, $old_values, $additional_data, $segment_id){}
    public function preVariantAdd(Item $item, $data, &$errors, $segment_id){}
    public function onVariantAdd(Item $item, $variant_id){}
    public function preVariantRemove(Item $item, Variant $variant){}
    public function onVariantRemove(Item $item, $variant_id){}
    public function preDelete(Item $item, &$errors = array()){}
    public function onDelete($id, $entity, $remove_from_db){}
    public function onCleanup(){}
    public function onClearCache($item_id = NULL){
        if (empty($item_id)){
            if (!empty($this->loadItemsQuery)){
                $this->loadItemsQuery = array();
            }
            if (!empty($this->dataCache)){
                $this->dataCache = array();
            }
        }else{
            if (!empty($this->loadItemsQuery[$item_id])){
                unset($this->loadItemdQuery[$item_id]);
            }
            if (!empty($this->dataCache[$item_id])){
                unset($this->dataCache[$item_id]);
            }
        }
        if (!empty($this->core)){
            $this->core->onClearCache($item_id);
        }
    }
    protected function isValueSame($action, Property $property, Item $entity, $v_id, $old_values, $segment_id = NULL){
        return \Models\CatalogManagement\CatalogHelpers\CatalogPosition\CatalogPositionHelper::factory()->isValueSame($action, $property, $entity, $v_id, $old_values, $segment_id);
    }
}
