<?php
/**
 * Класс работы с видимостью свойства в фильтрах
 *
 * @author olga
 */
namespace Models\CatalogManagement\CatalogHelpers\Property;
use Models\CatalogManagement\Properties\Property;
use Models\CatalogManagement\Properties\Factory AS PropertyFactory;
use App\Configs\CatalogConfig;
class FilterVisible extends PropertyHelper{
    const FV_PUBLIC = CatalogConfig::FV_PUBLIC;
    const FV_ADMIN = CatalogConfig::FV_ADMIN;
    const FV_OPT = CatalogConfig::FV_OPT;
    
    private static $allow_visibles = array(self::FV_PUBLIC, self::FV_ADMIN, self::FV_OPT);
    
    protected static $i = NULL;
    protected $dataCache = array();
    
    protected function __construct(){
        PropertyFactory::addDataProvider($this);
    }
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList(){
        return array('visibility_in_filter');
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
            $visibility = $property['filter_visible'];
            $result = array();
            foreach (self::$allow_visibles as $v){
                if ($v & $visibility){
                    $result[$v] = $v;
                }
            }
            $this->dataCache[$i_id] = $result;
        }
        return $this->dataCache[$i_id];
    }
}
