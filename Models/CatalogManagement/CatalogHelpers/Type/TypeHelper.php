<?php
namespace Models\CatalogManagement\CatalogHelpers\Type;
use Models\CatalogManagement\CatalogHelpers\Interfaces\iTypeDataProvider;
use Models\CatalogManagement\Type;

abstract class TypeHelper implements iTypeDataProvider{
    protected static $i = NULL;
    /**
     * @return static
     */
    public static function factory(){
        if (empty (static::$i)){
            static::$i = new static();
        }
        return static::$i;
    }
    /**
     * @var \MysqlSimple\Controller
     */
    protected $db;
    protected function __construct(){
        $this->db = \App\Builder::getInstance()->getDB();
        Type::addDataProvider($this);
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
    public function get(Type $type, $field, $segment_id = NULL){
        return NULL;
    }
    /**
     * уведомление, что данные для указанных Types попали в кеш данных и могут быть востребованы
     */
    public function onLoad(Type $type){

    }

	public function preCreate(&$params, &$errors){
        return true;
    }

    public function onCreate(Type $type, $params){
    }

    public function onUpdate(Type $type){
    }
    public function preUpdate(Type $type, &$params, &$errors){

    }
    /**
     *
     * @param Type $type
     */
    public function onDelete(Type $type){}
    /**
     * Для некоторых хелперов возвращаем в массиве знаения доп полей
     * @param Type $type
     * @param array $data
     */
    public function asArray(Type $type, &$data){}
}
?>