<?php
/**
 * Сегментированные поля свойства
 *
 * @author olga
 */
namespace Models\CatalogManagement\CatalogHelpers\Property;
use Models\CatalogManagement\Properties\Property;
class SegmentFields extends PropertyHelper{
    const TABLE_FIELDS = 'properties_fields';
    protected static $i = NULL;
    protected $cache = array();
    private static $additional_fields = array();
    protected $fields_list = array('title', 'filter_title', 'mask', 'public_description', 'segment_data');

    protected $as_array_field_list = array('title', 'filter_title', 'mask', 'public_description');
    private $loadItemsQuery = array();
    private $default_segment = NULL;
    
    protected function __construct() {
        $this->default_segment = \App\Segment::getInstance()->getDefault();
        parent::__construct();
    }
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Property $property, $field){
        $this->fieldCheck($field);
        $this->loadData();
        if (empty($this->cache[$property['id']])){
            return NULL;
        }
        if ($field == 'segment_data'){
            return $this->cache[$property['id']];
        }else{
            $segment_fields = $this->cache[$property['id']];
            $prop_segment = $property['segment_id'];
            if (!empty($segment_fields[$field][$prop_segment])){
                return $segment_fields[$field][$prop_segment];
            }
        }
        return NULL;
    }

    /**
     * уведомление, что данные для указанных Propertys попали в кеш данных и могут быть востребованы
     */
    public function onLoad(Property $property, &$data){
        if (!isset($this->cache[$property['id']])){
            $this->loadItemsQuery[$property['id']] = $property['id'];
        }
    }
    private function loadData(){
        if (empty ($this->loadItemsQuery))
            return;
        $segment_data = $this->db->query('
            SELECT `property_id`, `segment_id`, `field`, `value` 
            FROM `'.self::TABLE_FIELDS.'` 
                WHERE `property_id` IN (?i)', $this->loadItemsQuery
        )->getCol(array('property_id', 'field', 'segment_id'), 'value');
        if (!empty($segment_data)){
            $this->cache = $segment_data + $this->cache;
        }
		$this->loadItemsQuery = array();
    }
    public function onUpdate(Property $property){
        if (!empty(self::$additional_fields[$property['id']])){
            $this->loadData();
            foreach (self::$additional_fields[$property['id']] as $field_name => $data){
                if (empty($data)){
                    $this->db->query('DELETE FROM `'.self::TABLE_FIELDS.'` WHERE `property_id` = ?d AND `field` = ?s', $property['id'], $field_name);
                    if (!empty($this->cache[$property['id']])){
                        foreach ($this->cache[$property['id']] as $field_name => &$fields_data){
                            foreach ($fields_data as $s_id => &$val){
                                $val = NULL;
                            }
                        }
                    }
                    return $property['id'];
                }
                if (!empty($data) && !is_array($data)){
                    throw new \LogicException('Неверно переданы данные для редактирования свойства: field ' . $field_name);
                }
                $marker = \Models\CatalogManagement\Properties\Property::MARKER;
                foreach ($data as $s_id => $val){
                    if ($field_name == 'mask'){
                        if (empty($val))
                            $val = $marker;
                        elseif (strpos($val, $marker) === false)
                            $val = $marker . ' ' . $val;
                    }
                    if (!empty($val) && !is_string($val)){
                        throw new \LogicException('Неверно переданы данные для редактирования свойства: field ' . $field_name);
                    }
                    if (!empty($val)){
                        $this->db->query('REPLACE INTO `'.self::TABLE_FIELDS.'` SET `property_id` = ?d, `field` = ?s, `segment_id` = ?d, `value` = ?s', $property['id'], $field_name, $s_id, $val);
                        $this->cache[$property['id']][$field_name][$s_id] = $val;
                    }
                }
            }
            unset(self::$additional_fields[$property['id']]);
        }
    }
    public function preUpdate(Property $property, &$params, &$errors){
        foreach ($this->fields_list as $f){
            if (array_key_exists($f, $params)){
                if ($f == 'title'){
                    if (empty($params[$f])){
                        $errors['title'] = \Models\Validator::ERR_MSG_EMPTY;
                    }else{
                        $segments = \App\Segment::getInstance()->getAll();
                        foreach ($segments as $s){
                            if (empty($params[$f][$s['id']])){
                                $errors['title['.$s['id'].']'] = \Models\Validator::ERR_MSG_EMPTY;
                            }
                        }
                    }
                }
                self::$additional_fields[$property['id']][$f] = is_array($params[$f]) ? $params[$f] : array($this->default_segment['id'] => $params[$f]);
                unset($params[$f]);
            }
        }
        return $property;
    }
    public function preCreate(&$params, &$errors, $create_key){
        foreach ($this->fields_list as $f){
            if (array_key_exists($f, $params)){
                if ($f == 'title'){
                    if (empty($params[$f])){
                        $errors['title'] = \Models\Validator::ERR_MSG_EMPTY;
                    }else{
                        $segments = \App\Segment::getInstance()->getAll();
                        foreach ($segments as $s){
                            if (empty($params[$f][$s['id']])){
                                $errors['title['.$s['id'].']'] = \Models\Validator::ERR_MSG_EMPTY;
                            }
                        }
                    }
                }
                self::$additional_fields[$create_key][$f] = is_array($params[$f]) ? $params[$f] : array($this->default_segment['id'] => $params[$f]);
                unset($params[$f]);
            }
        }
    }
    public function onCreate($id, $create_key){
        if (!empty(self::$additional_fields[$create_key])){
            $this->loadData();
            foreach (self::$additional_fields[$create_key] as $field_name => $data){
                if (!empty($data) && !is_array($data)){
                    throw new \LogicException('Неверно переданы данные для редактирования свойства: field ' . $field_name);
                }
                $marker = \Models\CatalogManagement\Properties\Property::MARKER;
                foreach ($data as $s_id => $val){
                    if ($field_name == 'mask'){
                        if (empty($val))
                            $val = $marker;
                        elseif (strpos($val, $marker) === false)
                            $val = $marker . ' ' . $val;
                    }
                    if (!empty($val) && !is_string($val)){
                        throw new \LogicException('Неверно переданы данные для редактирования свойства: field ' . $field_name);
                    }
                    if (!empty($val)){
                        $this->db->query('REPLACE INTO `'.self::TABLE_FIELDS.'` SET `property_id` = ?d, `field` = ?s, `segment_id` = ?d, `value` = ?s', $id, $field_name, $s_id, $val);
                        $this->cache[$id][$field_name][$s_id] = $val;
                    }
                }
            }
            unset(self::$additional_fields[$create_key]);
        }
    }
    public function onDelete($property_id){
        $this->db->query('DELETE FROM `'.self::TABLE_FIELDS.'` WHERE `property_id` = ?d', $property_id);
    }

    public function asArray(Property $property, array &$data) {
        $segment_data = $this->get($property, 'segment_data');
        if (!empty($segment_data)) {
            $data = $segment_data + $data;
        }
    }
}