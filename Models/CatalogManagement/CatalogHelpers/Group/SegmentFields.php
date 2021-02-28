<?php
namespace Models\CatalogManagement\CatalogHelpers\Group;

use Models\CatalogManagement\Group;
/**
 * 
 */
class SegmentFields implements \Models\CatalogManagement\CatalogHelpers\Interfaces\iGroupDataProvider{
    const TABLE_FIELDS = 'property_groups_titles';
    protected static $i = NULL;
    private $loadItemsQuery = array();
    private $dataCache = array();
    private static $additional_fields = array();
    private static $fields_list = array('title', 'segment_data');
    /**
     * @return static
     */
    public static function factory($data = NULL){
        if (empty (static::$i)){
            static::$i = new static($data);
        }
        return static::$i;
    }
    protected function __construct($data = NULL){
        Group::addDataProvider($this);
    }
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList(){
        return self::$fields_list;
    }
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Group $group, $field){
        if (in_array($field, self::$fields_list)){
            $this->loadData();
            $segment_fields = !empty($this->dataCache[$group['id']]) ? $this->dataCache[$group['id']] : NULL;
            if ($field == 'segment_data'){
                return $segment_fields;
            }
            $group_segment = $group['segment_id'];
            if (!empty($segment_fields[$field][$group_segment])){
                return $segment_fields[$field][$group_segment];
            }
        }
        return NULL;
    }
    public function asArray(Group $group) {
        return $this->get($group, 'segment_data');
    }
    /**
     * уведомление, что данные для указанных Groups попали в кеш данных и могут быть востребованы
     */
    public function onLoad(Group $group){
        if (!isset($this->dataCache[$group['id']])){
            $this->loadItemsQuery[$group['id']] = $group['id'];
        }
    }
    public function loadData(){
        if (empty ($this->loadItemsQuery))
            return;
        $db = \App\Builder::getInstance()->getDB();
        $segment_data = $db->query('
            SELECT `group_id`, IF (`segment_id` IS NULL, 0, `segment_id`) AS `segment_id`, `value` , "title" AS `field_name`
            FROM `'.self::TABLE_FIELDS.'` 
                WHERE `group_id` IN (?i)
            ',  $this->loadItemsQuery
        )->getCol(array('group_id', "field_name", 'segment_id'), 'value');
        if (!empty($segment_data)){
            $this->dataCache = $segment_data + $this->dataCache;
        }
        $this->loadItemsQuery = array();
    }
    public function preCreate(&$params, &$errors){
        if (empty($params)){
            $errors['title'] = \Models\Validator::ERR_MSG_EMPTY;
        }else{
            $segments = \App\Segment::getInstance()->getAll();
            foreach ($segments as $s){
                if (empty($params[$s['id']])){
                    $errors['title['.$s['id'].']'] = \Models\Validator::ERR_MSG_EMPTY;
                }
            }
        }
        self::$additional_fields['create']['title'] = $params;
        unset($params);
    }
    /**
     * После создания, что делать с доп полями
     * @param int $id
     * @param array $params
     * @param array $additional_fields Уже проверенные данные в preCreate
     * @return int
     */
    public function onCreate($id, $params){
        if (!empty(self::$additional_fields['create'])){
            $db = \App\Builder::getInstance()->getDB();
            foreach (self::$additional_fields['create'] as $field_name => $data){
                if ($field_name == 'title'){
                    if (!is_array($data)){
                        throw new \LogicException('Неверно переданы данные для создания группы');
                    }
                    foreach ($data as $s_id => $val){
                        if (!is_string($val)){
                            throw new \LogicException('Неверно переданы данные для создания группы');
                        }
                        $db->query('INSERT INTO `'.self::TABLE_FIELDS.'` SET `group_id` = ?d, `segment_id` = ?d, `value` = ?s', $id, $s_id, $val);
                    }
                }
            }
            unset(self::$additional_fields['create']);
        }
    }
    public function preUpdate(Group $group, &$params, &$errors){
        foreach (self::$fields_list as $f){
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
                self::$additional_fields[$group['id']][$f] = $params[$f];
                unset($params[$f]);
            }
        }
        self::$additional_fields[$group['id']]['group_type_id'] = $group['type_id'];
    }
    public function onUpdate(Group $group){
        if (!empty(self::$additional_fields[$group['id']])){
            $this->loadData();
            $db = \App\Builder::getInstance()->getDB();
            foreach (self::$additional_fields[$group['id']] as $field_name => $data){
                if ($field_name == 'title'){
                    if (!is_array($data)){
                        throw new \LogicException('Неверно переданы данные для редактирования типа');
                    }
                    foreach ($data as $s_id => $val){
                        if (!is_string($val)){
                            throw new \LogicException('Неверно переданы данные для редактирования типа');
                        }
                        $db->query('REPLACE INTO `'.self::TABLE_FIELDS.'` SET `group_id` = ?d, `segment_id` = ?d, `value` = ?s', $group['id'], $s_id, $val);
                        $this->dataCache[$group['id']][$field_name][$s_id] = $val;
                    }
                }
            }
            unset(self::$additional_fields[$group['id']]);
        }
    }
    
    public function onDelete($id){
        $db = \App\Builder::getInstance()->getDB();
        $db->query('DELETE FROM `'.self::TABLE_FIELDS.'` WHERE `group_id` = ?d', $id);
    }
}
