<?php
/**
 * Description of SegmentEnumTitles
 *
 * @author olga
 */
namespace Models\CatalogManagement\CatalogHelpers\Property;
use Models\CatalogManagement\Properties\Property;
use Models\CatalogManagement\Properties\Enum;
class SegmentEnumTitles extends PropertyHelper{
    const TABLE_SEGMENT_TITLES = 'enum_segment_titles';
    protected static $i = NULL;
    private $dataCache = array();
    private static $additional_fields = array();
    private static $delete_fields = array();
    protected $fields_list = array('segment_enum');
    private $loadItemsQuery = array();
    private $delete_data_log = array();
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Property $property, $field){
        $this->fieldCheck($field);
        if (!array_key_exists($property['id'], $this->dataCache)){
            $this->loadData();
        }
        return !empty($this->dataCache[$property['id']]) ? $this->dataCache[$property['id']] : NULL;
    }

    /**
     * При создании
     * @param \Models\CatalogManagement\Properties\Property $property
     * @param array $data
     * @return void
     */
    public function onLoad(Property $property, &$data){
        if (!$property instanceof Enum){
            return;
        }
        if (!isset($this->dataCache[$property['id']]) && is_array($property['values'])){
            $this->loadItemsQuery[$property['id']] = array_keys($property['values']);
        }
    }
    
    private function loadData(){
        if (empty($this->loadItemsQuery)){
            return;
        }
        $enum_ids = array();
        foreach ($this->loadItemsQuery as $res){
            $enum_ids = array_merge($enum_ids, $res);
        }
        if (!empty($enum_ids)){
            $segment_enums = $this->db->query('SELECT * FROM `'.self::TABLE_SEGMENT_TITLES.'` WHERE `enum_id` IN (?i)', $enum_ids)->getCol(array('enum_id', 'segment_id'), 'value');
        }
        $segment_data = array();
        foreach ($this->loadItemsQuery as $property_id => $res){
            foreach ($res as $enum_id){
                if (!empty($segment_enums[$enum_id])){
                    $segment_data[$property_id][$enum_id] = $segment_enums[$enum_id];
                }
            }
        }
        if (!empty($segment_data)){
            $this->dataCache = $segment_data + $this->dataCache;
        }
        $this->loadItemsQuery = array();
    }
    
    public function onUpdate(Property $property){
        if (!empty(self::$additional_fields[$property['id']]['segment_enum'])){//если разбиваем на сегменты
            $default_segment = \Models\Segments\Lang::getDefault();
            $enum_synchronize = array();//синхронизируем реальные enum_id и номера у только что добавленных
            foreach (self::$additional_fields[$property['id']]['segment_enum'] as $segment_id => $d){//@TODO проверки и exception
                foreach ($d as $e_id => $title){
                    if (empty($enum_synchronize[$e_id])){//при первом сегменте, обновляем\добавляем позиции enum названий
                        if (!empty($property['values'][$e_id])){
                            $enum_synchronize[$e_id] = $e_id;
                        }else{
                            foreach($property['values'] as $enum_id => $enum_val) {
                                if ($enum_val['value'] == self::$additional_fields[$property['id']]['segment_enum'][$default_segment['id']][$e_id]) {
                                    $enum_synchronize[$e_id] = $enum_id;
                                }
                            }
                            if (empty($enum_synchronize[$e_id])) {
                                throw new \ErrorException('Не удалось добавить сегментное значение элемента перечисления');
                            }
                        }
                    }
                    unset(self::$delete_fields[$property['id']][$enum_synchronize[$e_id]]);
                    self::setTitle($enum_synchronize[$e_id], $segment_id, $title);
                }
            }
            if (!empty(self::$delete_fields[$property['id']])) {
//                $this->db->query('DELETE FROM ?# WHERE `enum_id` IN (?i)', self::TABLE_SEGMENT_TITLES, self::$delete_fields[$property['id']]);
                unset(self::$delete_fields[$property['id']]);
            }
            unset(self::$additional_fields[$property['id']]);
            if ($property['multiple']){
                \Models\CatalogManagement\Variant::clearCache();
            }else{
                \Models\CatalogManagement\Item::clearCache();
            }
        }
    }
    public function preUpdate(Property $property, &$params, &$errors){
        if (($property instanceof Enum || !empty($params['data_type']) && $params['data_type'] == Enum::TYPE_NAME) && \LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE && !empty($params['values']['values'])){
            $params['segment_enum'] = $params['values']['values'];
            unset($params['values']['values']);
        }
        if (!empty($params['segment_enum'])){
            foreach($params['segment_enum'] as $s_id => $values) {
                $val_list = array();
//                var_dump($values);
                foreach($values as $e_id => $title) {
                    if (empty($title)) {
                        $errors['values[values]['.$s_id.']['.$e_id.']'] = \Models\Validator::ERR_MSG_EMPTY;
                    } elseif (in_array($title, $val_list)) {
                        $errors['values[values]['.$s_id.']['.$e_id.']'] = \Models\Validator::ERR_MSG_EXISTS;
                    }
                    $val_list[] = $title;
                }
            }
            if (!empty($errors)) {
                unset($params['segment_enum']);
                return false;
            }
            self::$additional_fields[$property['id']]['segment_enum'] = $params['segment_enum'];
            self::$delete_fields[$property['id']] = !empty($property['values']) ? array_keys($property['values']) : array();
            $default_segment = \Models\Segments\Lang::getDefault();
            if (!empty($params['segment_enum'][$default_segment['id']])) {
                foreach($params['segment_enum'][$default_segment['id']] as $val_id => $value) {
                    $params['values']['values'][$val_id] = $value;
                    unset(self::$delete_fields[$property['id']][$val_id]);
                }
            }
            unset($params['segment_enum']);
        }
//        elseif ($property instanceof Enum){
//            $errors = 'Для типа свойства "Перечисление" должно быть заполнено хотя бы одно "Значение"';
//        }
        return $property;
    }
    public function preDelete(Property $property, &$error){
        if ($property['data_type'] == Enum::TYPE_NAME) {
            $this->delete_data_log[$property['id']] = $property['id'];
        }
        return TRUE;
    }
    public function onDelete($property_id){
        if (!empty($this->delete_data_log[$property_id])){
            $this->db->query('DELETE FROM `'.self::TABLE_SEGMENT_TITLES.'` WHERE `enum_id` IN (SELECT `id` FROM ?# WHERE `property_id` = ?d)', Enum::TABLE_PROP_ENUM, $property_id);
        }
    }
    public function setTitle($enum_id, $segment_id, $title){
        $this->db->query('REPLACE INTO `'.self::TABLE_SEGMENT_TITLES.'` SET `enum_id` = ?d, `segment_id` = ?d, `value` = ?s', $enum_id, $segment_id, $title);
        $property_id = $this->db->query('SELECT `property_id` FROM `'. Enum::TABLE_PROP_ENUM .'` WHERE `id` = ?d', $enum_id)->getCell();
        $this->dataCache[$property_id][$enum_id][$segment_id] = $title;
    }
}
?>