<?php
/**
 * Description of PropertyImage
 *
 * @author olga
 */
namespace Models\CatalogManagement\CatalogHelpers\Property;
use Models\CatalogManagement\Properties\Property;
use Models\ImageManagement\Image AS ImageEntity;
class Image extends PropertyHelper{
    protected static $i = NULL;
    protected $dataCache = array();
    
    protected function __construct(){
        Property::addDataProvider($this);
    }
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList(){
        return array('image');
    }
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Property $property, $field){
        if (!in_array($field, $this->fieldsList())){
            throw new \InvalidArgumentException('Неверное название дополнительного поля');
        }
        $i_id = $property['id'];
        if (!isset($this->dataCache[$i_id])){
            $this->dataCache[$i_id] = !empty($property['image_id']) ? ImageEntity::getById($property['image_id']) : NULL;
        }
        return $this->dataCache[$i_id];
    }

    /**
     * уведомление, что данные для указанных Propertys попали в кеш данных и могут быть востребованы
     */
    public function onLoad(Property $property, &$data){
        ImageEntity::prepare(array($property['image_id']));
    }
    
    public function onDelete($property_id){
        $property = \Models\CatalogManagement\Properties\Factory::getById($property_id);
        if (!empty($property['image_id'])){
            ImageEntity::del($property['image_id']);
        }
    }
}
?>