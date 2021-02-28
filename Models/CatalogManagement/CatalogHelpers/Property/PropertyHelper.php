<?php
/**
 * Description of PropertyHelper
 *
 * @author Alexander Shulman
 */
namespace Models\CatalogManagement\CatalogHelpers\Property;
use Models\CatalogManagement\CatalogHelpers\Interfaces\iPropertyDataProvider;
use Models\CatalogManagement\Properties\Property;
abstract class PropertyHelper implements iPropertyDataProvider{
    protected static $i = NULL;
    protected $fields_list = array();
    /**
     * @var \MysqlSimple\Controller
     */
    protected $db = NULL;
    
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
    
    /** конструктор закрыт ибо снглтон **/
    protected function __construct(){
        $this->fields_list = !empty($this->fields_list) ? array_combine($this->fields_list, $this->fields_list) : array(); //это позволяет использовать менее затратные isset
        $this->db = \App\Builder::getInstance()->getDB();
        Property::addDataProvider($this);
    }

    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList(){
        return $this->fields_list;
    }
    
    protected function fieldCheck ($offset){
        if (!isset($this->fields_list[$offset])){
            throw new \InvalidArgumentException('Неверное название дополнительного поля');
        }
    }
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Property $property, $field){
        $this->fieldCheck($field);
    }
    public function preCreate(&$params, &$errors, $create_key){}
    public function onCreate($id, $create_key){}
    public function onLoad(Property $property, &$data){}
    
    public function onUpdate(Property $property){}
    
    public function preUpdate(Property $property, &$params, &$errors){}

    /**
     * Вызывается перед удалением свойства, позволяет отменить удаление
     * @param Property $property
     * @return bool FALSE чтобы отменить удаление
     */
    public function preDelete(Property $property, &$error){
        return TRUE;
    }
    public function onDelete($property_id){}

    /**
     * Вызывается из Property::asArray, добавляет к конечному массиву доп поля всех хелперов
     * @param Property $property
     * @param array $data
     */
    public function asArray(Property $property, array &$data){
    }
    
    public function onEnumAdd(Property $property, $enum_id){}
    public function onEnumEdit(Property $property, $enum_id, $enum_data){}
    public function onEnumDelete(Property $property, $enum_data){}
}