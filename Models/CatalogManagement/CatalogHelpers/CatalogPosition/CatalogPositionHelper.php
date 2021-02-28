<?php
namespace Models\CatalogManagement\CatalogHelpers\CatalogPosition;
use Models\CatalogManagement\CatalogPosition;
use Models\CatalogManagement\CatalogHelpers\Interfaces\iCatalogPositionDataProvider;
use Models\CatalogManagement\Properties\Property;
/**
 * Description of CatalogPositionHelper
 *
 * @author charles manson
 */
class CatalogPositionHelper implements iCatalogPositionDataProvider{
    protected static $i = NULL;
    /**
     * 
     * @return static
     */
    public static function factory(){
        if (empty (static::$i)){
            static::$i = new static();
        }
        return static::$i;
    }
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList(){
        return array();
    }
    /**
     * возвращает значение дополнительного поля
     */
    public function get(CatalogPosition $c, $field){}
    /**
     * предупреждение, что данные для указанных CatalogPositions попали в кеш данных чтобы можно было подготовить доп. данные
     */
    public function onLoad(CatalogPosition $c, $propertiesBySegments = NULL){}
    public function onPropertyLoad(CatalogPosition $c, &$propValues){}
    /**
     * событие на создание нового CatalogPosition
     */
    public function onCreate($catalog_position_id, $entity_type, $segment_id){}
    /**
     * Событие до изменения CatalogPosition
     */
    public function preUpdate($updateKey, CatalogPosition $entity, &$params, &$properties, $segment_id, &$errors){}
    /**
     * событие на изменение CatalogPosition
     */
    public function onUpdate($updateKey, CatalogPosition $entity, $segment_id, $updatedProperties){}

    /**
     * Событие на изменение значения
     * @param CatalogPosition $entity
     * @param Property $property
     * @param $v_id
     * @param array $additional_data
     */
    public function onValueChange($action, CatalogPosition $entity, Property $property, $v_id, $old_values, $additional_data, $segment_id){}
    /**
     * событие на удаление CatalogPosition
     */
    public function onDelete($catalog_position_id, $entity_type, $entity, $remove_from_db){}
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
    }
    /**
     * Возникла проблема со сменой позиций. возможно значения не поменялись, а позиция сменилась
     * @param string $action
     * @param Property $property
     * @param CatalogPosition $entity
     * @param int $v_id
     * @param array $old_values array(val_id, value, position)
     * @return type
     */
    public function isValueSame($action, Property $property, CatalogPosition $entity, $v_id, $old_values, $segment_id = NULL){
        $segment_properties = $entity->getSegmentProperties($segment_id);
        $new_values = !empty($segment_properties[$property['key']]) ? $segment_properties[$property['key']] : array();
        return $action == 'edit' 
            && (($property['set'] 
                && $old_values['value'][$v_id] == $new_values['value'][$v_id]) 
            || (!$property['set'] 
                && $old_values['value'] == $new_values['value']
                )
            );
    }
}
