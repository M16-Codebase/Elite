<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 19.05.15
 * Time: 12:35
 */

namespace App\Auth\Users\Helpers;

use \App\Auth\Users\RegistratedUser;

class CustomFields extends RegistratedUserHelper{
    const TABLE_CUSTOM_FIELDS = 'users_custom_fields';

    const SEARCH_LIKE = 'like';
    const SEARCH_EQUAL = 'equal';

    protected static $i = NULL;
    protected static $fields_list = array('company_name', 'inn', 'ogrn', 'okpo', 'kpp', 'image_id', 'phone', 'city');

    protected static $search_params = array(
        'company_name' => self::SEARCH_LIKE
    );

    protected $db = NULL;
    protected $cache = array();

    protected $load_ids = array();

    protected $edit_cache = array();
    protected $create_cache = array();

    protected function __construct(){
        $this->db = \App\Builder::getInstance()->getDB();
        parent::__construct();
    }

    public function prepare(array $ids)
    {
        if (!empty($ids)) {
            $this->load_ids = array_merge($this->load_ids, $ids);
        }
    }

    private function loadData(){
        if (!empty($this->load_ids)) {
            $user_data = $this->db->query('SELECT `user_id`, `field`, `value` FROM ?# WHERE `user_id` IN (?i)',
                    self::TABLE_CUSTOM_FIELDS,
                    $this->load_ids)
                ->getCol(array('user_id', 'field'), 'value');
            $this->cache = $this->cache + $user_data;
            $this->load_ids = array();
        }
    }

    public function get(RegistratedUser $user, $field){
        if (in_array($field, self::$fields_list)){
            $this->loadData();
            return !empty($this->cache[$user['id']][$field]) ? $this->cache[$user['id']][$field] : NULL;
        }
    }

    public function preCreate(&$params, &$errors, $hash){
        $user_creation_params = array();
        foreach (self::$fields_list as $f){
            if (array_key_exists($f, $params)){
                $user_creation_params[$f] = $params[$f];
                unset($params[$f]);
            }
        }
        if (!empty($user_creation_params)){
            $this->create_cache[$hash] = $user_creation_params;
            return TRUE;
        }
        return FALSE;
    }
    /**
     * событие на создание нового User
     */
    public function onCreate($id, $hash){
        if (!empty($this->create_cache[$hash])){
            $values = array();
            foreach($this->create_cache[$hash] as $f => $val){
                if (!empty($val)){
                    $values[] = '(' . $this->db->escape_value($id) . ', ' . $this->db->escape_value($f) . ', ' . $this->db->escape_value($val) . ')';
                }
            }
            if (!empty($values)){
                $this->db->query('INSERT INTO ?# (`user_id`, `field`, `value`) VALUES ' . implode(', ', $values) . ' ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)', self::TABLE_CUSTOM_FIELDS);
            }
            unset($this->create_cache[$hash]);
        }
    }
    /**
     * событие перед изменением
     */
    public function preUpdate(RegistratedUser $user, &$params, $segment_id, &$errors){
        $user_edit_params = array();
        foreach (self::$fields_list as $f){
            if (array_key_exists($f, $params)){
                $user_edit_params[$f] = $params[$f];
                unset($params[$f]);
            }
        }
        if (!empty($user_edit_params)){
            //@TODO почему сразу в цикле нельзя собирать? а потом return !empty($this->edit_cache[$user['id']])
            $this->edit_cache[$user['id']] = $user_edit_params;
            return TRUE;
        }
        return FALSE;
    }
    /**
     * событие после изменения User
     * @param \App\Auth\Users\RegistratedUser $user
     * @param array $old_data
     * @param int $segment_id
     */
    public function onUpdate(RegistratedUser $user, $old_data, $segment_id){
        if (!empty($this->edit_cache[$user['id']])){
            $values = array();
            $delete_fields = array();
            foreach($this->edit_cache[$user['id']] as $f => $val){
                if (!empty($val)){
                    $this->cache[$user['id']][$f] = $val;
                    $values[] = '(' . $this->db->escape_value($user['id']) . ', ' . $this->db->escape_value($f) . ', ' . $this->db->escape_value($val) . ')';
                } else {
                    unset($this->cache[$user['id']][$f]);
                    $delete_fields[$f] = true;
                }
            }
            if (!empty($values)){
                $this->db->query('INSERT INTO ?# (`user_id`, `field`, `value`) VALUES ' . implode(', ', $values) . ' ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)', self::TABLE_CUSTOM_FIELDS);
            }
            if (!empty($delete_fields)){
                $this->db->query('DELETE FROM ?# WHERE `user_id` = ?d AND `field` IN (?l)', self::TABLE_CUSTOM_FIELDS, $user['id'], array_keys($delete_fields));
            }
            unset($this->edit_cache[$user['id']]);
        }
    }

    /**
     * Событие перед поиском, возвращает список id, в которых нужно искать
     * @param array $params
     * @param array $order
     * @param bool $use_in_search флаг, сообщает о том, что данный хелпер участвует в поиске (на случай пустого результата)
     * @return array ids пользователей
     */
    public function preSearch(array &$params, &$order, &$use_in_search = FALSE){
        $use_in_search = FALSE;
        $search_params = array();
        if (!empty($params['order'])){
            if (is_array($params['order'])){
                foreach ($params['order'] as $key => $desc){
                    if (in_array($key, self::$fields_list)){
                        $line = $this->getFieldOrderLine($key, $desc);//$key . (!empty($desc) ? ' DESC ' : ' ');
                        if (!empty($line)) {
                            $order[] = $line;
                        }
                        unset($params['order'][$key]);
                    }
                }
            }else{
                throw new \LogicException('Order param must be an array("key" => "desc")');
            }
        }
        foreach(self::$fields_list as $f){
            if (!empty($params[$f])){
                $search_params[$f] = $params[$f];
                unset($params[$f]);
            }
        }
        $use_in_search = !empty($search_params);
        return $this->buildQuery($search_params);
    }

    private function getFieldOrderLine($field, $desc){
        $ids = $this->db->query('SELECT `user_id` FROM ?# WHERE `field` = ?s ORDER BY `value`' . (!empty($desc) ? ' DESC' : ''), self::TABLE_CUSTOM_FIELDS, $field)->getCol(NULL, 'user_id');
        return !empty($ids) ? 'FIELD(`u`.`id`, ' . implode(',', $ids) . ')' : '';
    }

    private function buildQuery($search_params){
        if (empty($search_params)){
            return NULL;
        }
        $select = 'SELECT DISTINCT `ucf`.`user_id` as `id` FROM `' . self::TABLE_CUSTOM_FIELDS . '` AS `ucf`';
        $where = array();
        foreach($search_params as $f => $v){
            $select .= ' LEFT JOIN `' . self::TABLE_CUSTOM_FIELDS . '` AS `ucf_' . $f . '` ON `ucf`.`user_id` = `ucf_' . $f . '`.`user_id` AND `ucf_' . $f . '`.`field` = ' . $this->db->escape_value($f);
            $where[] = $this->getWhereRule($f, $v);
        }
        return $this->db->query($select . ' WHERE ' . implode(' AND ', $where))->getCol('id', 'id');
    }

    private function getWhereRule($field, $value){
        $rule_type = !empty(self::$search_params[$field]) ? self::$search_params[$field] : self::SEARCH_EQUAL;
        $rule = '`ucf_'.$field.'`.`value`';
        switch ($rule_type){
            case self::SEARCH_LIKE:
                $rule .= ' LIKE ' . $this->db->escape_value('%'.$value.'%');
                continue;
            case self::SEARCH_EQUAL:
                $rule .= ' = ' . $this->db->escape_value($value);
                continue;
            default:
                throw new \LogicException("Unknown rule type #${rule_type}");
        }
        return $rule;
    }
    /**
     * событие на удаление user
     * @param int $user_id
     */
    public function onDelete($user_id){
        $this->db->query('DELETE FROM ?# WHERE `user_id` = ?d', self::TABLE_CUSTOM_FIELDS, $user_id);
    }
}