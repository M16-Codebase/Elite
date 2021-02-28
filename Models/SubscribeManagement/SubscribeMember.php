<?php
namespace Models\SubscribeManagement;
/**
 * Description of SubscribeMember
 *
 * @author Charles Manson
 */
use App\Configs\CatalogConfig;
class SubscribeMember implements \ArrayAccess{
    const MEMBERS_TABLE = 'subscribe_members';
    const MEMBERS_TO_LIST_TABLE = 'subscribe_list_members';
    
    private static $load_fields = array('email', 'name', 'surname', 'company_name', 'lockconfirm', 'lockremove', 'create_time', 'inner', 'groups', 'need_update');
    private static $update_fields = array('name', 'surname', 'company_name', 'lockconfirm', 'lockremove', 'inner', 'need_update');
    
    private static $members = array();
    
    private $data = array();
    private $db = NULL;
    /**
     * 
     * @param array $params
     * @param bool|int $count
     * @param int $start
     * @param int $page_size
     * @param array $sort
     * @return SubscribeMember
     */
    public static function search($params, &$count=false, $start=0, $page_size=100, $sort = array('field' => 'email', 'order' => 'asc')){
        $db = \App\Builder::getInstance()->getDB();
        $status_filter = '';
        $status_filter_params = !empty($params['status']) ? $params['status'] : array();
        if (!empty($status_filter_params['new'])){
            $status_filter = '`m`.`lockconfirm` = 1 AND `m`.`lockremove` IS NULL';
        }
        if (!empty($status_filter_params['lock'])){
            $status_filter .= (!empty($status_filter) ? ' OR ' : '') . '`m`.`lockremove` IS NOT NULL';
        }
        if (!empty($status_filter_params['active'])){
            $status_filter .= (!empty($status_filter) ? ' OR ' : '') . '`m`.`lockconfirm` != 1 AND `m`.`lockremove` IS NULL';
        }
        $emails = $db->query('SELECT '.($count !== false ? 'SQL_CALC_FOUND_ROWS ' : '').'LOWER(`m`.`email`) AS `email`'
            . ' FROM `' . self::MEMBERS_TABLE . '` AS `m` LEFT JOIN `' . self::MEMBERS_TO_LIST_TABLE . '` AS `ml`'
            . ' ON `m`.`email` = `ml`.`email`'
            . ' WHERE 1' . (!empty($status_filter) ? ' AND (' . $status_filter . ')' : '')
            . '{ AND `ml`.`group_id` = ?s}'
            . '{ AND `m`.`create_time` >= ?s}'
            . '{ AND `m`.`create_time` <= ?s}'
            . '{ AND `m`.`email` LIKE ?s}'
            . '{ AND `m`.`surname` LIKE ?s}'
            . '{ AND `m`.`company_name` LIKE ?s}'
            . '{ AND `m`.`need_update` = ?d}'
            . ' ORDER BY ' . $sort['field'] . ((!empty($sort['order']) && $sort['order'] == 'desc') ? ' DESC' : '')
            . ' LIMIT ?d, ?d',
            !empty($params['group_id']) ? $params['group_id'] : $db->skipIt(),
            !empty($params['date_min']) ? date('Y-m-d 00:00:00', strtotime($params['date_min'])) : $db->skipIt(),
            !empty($params['date_max']) ? date('Y-m-d 00:00:00', strtotime($params['date_max'])) : $db->skipIt(),
            !empty($params['email']) ? '%' . $params['email'] . '%' : $db->skipIt(),
            !empty($params['surname']) ? '%' . $params['surname'] . '%' : $db->skipIt(),
            !empty($params['company_name']) ? '%' . $params['company_name'] . '%' : $db->skipIt(),
            !empty($params['need_update']) ? 1 : $db->skipIt(),
            $start, $page_size
        )->getCol('email', 'email');
        if ($count!==false)
            $count = $db->query('SELECT FOUND_ROWS()')->getCell();
        return !empty($emails) ? self::factory($emails) : array();
    }
    /**
     * 
     * @param array $emails
     * @return SubscribeMember
     */
    public static function factory($emails){
        $loaded_emails = !empty(self::$members) ? array_keys(self::$members) : array();
        $all_emails = array_merge($emails, $loaded_emails);
        $emails4load = array_diff($all_emails, $loaded_emails);
        if (!empty($emails4load)){
            $db = \App\Builder::getInstance()->getDB();
            $new_members = $db->query('SELECT LOWER(`m`.`email`) AS `email`, `m`.`name`, `m`.`surname`, `m`.`company_name`, `m`.`lockconfirm`, `m`.`lockremove`, `m`.`create_time`, `m`.`inner`, `m`.`need_update`, GROUP_CONCAT(`ml`.`group_id` SEPARATOR "|") AS `groups`'
                . ' FROM `' . self::MEMBERS_TABLE . '` AS `m` LEFT JOIN `' . self::MEMBERS_TO_LIST_TABLE . '` AS `ml`'
                . ' ON `m`.`email` = `ml`.`email`'
                . ' WHERE `m`.`email` IN (?l)'
                . ' GROUP BY `m`.`email`', $emails4load)->select('email');
            foreach($new_members AS $email => $member){
                self::$members[$email] = new self($member);
            }
        }
        $result = array();
        foreach($emails as $email){
            $result[$email] = !empty(self::$members[$email]) ? self::$members[$email] : NULL;
        }
        return $result;
    }
    /**
     * 
     * @param string $email
     * @return SubscribeMember
     */
    public static function getByEmail($email){
        $members = self::factory(array(strtolower($email)));
        return reset($members);
    }
    
    private function __construct($member_data) {
        $this->db = \App\Builder::getInstance()->getDB();
        foreach($member_data as $key => $value){
            if (in_array($key, self::$load_fields)){
                $this->data[$key] = $value;
            }
        }
        $this->data['groups'] = !empty($this->data['groups']) ? explode('|', $this->data['groups']) : array();
    }
    
    public function getData($key){
        if (in_array($key, self::$load_fields)){
            return !empty($this->data[$key]) ? $this->data[$key] : NULL;
        }else{
            throw new \LogicException('Object does not have a parameter ' . $key);
        }
    }
    
    public static function create($email, $data = array(), $deferred_update = FALSE){
        $member = self::getByEmail($email);
        if (!empty($member)){
            return FALSE;
        }
        $fields = array('email' => $email);
        $fields['create_time'] = isset($data['create.time']) ? $data['create.time'] : date('Y-m-d H:i:s');
        if (empty($data['lockremove'])){
            $fields['lockremove'] = NULL;
        }
        if (empty($data['lockconfirm'])){
            $fields['lockconfirm'] = 0;
        }
        foreach($data as $key => $value){
            if (in_array($key, self::$load_fields)){
                $fields[$key] = $value;
            }
        }
        \App\Builder::getInstance()->getDB()->query('INSERT INTO `' . self::MEMBERS_TABLE . '` SET ?a', $fields);
        return self::getByEmail($email);
    }
    /**
     * 
     * @param array $params
     * @param bool $deferred_update
     */
    public function edit($params, $deferred_update = FALSE){
        $upd_fields = array();
        if (isset($params['create.time'])){
            $upd_fields['create_time'] = $params['create.time'];
        }
        if (empty($params['lockremove'])){
            $upd_fields['lockremove'] = NULL;
        }
        if (empty($params['lockconfirm'])){
            $upd_fields['lockconfirm'] = 0;
        }
        foreach($params as $key => $value){
            if (!array_key_exists($key, $upd_fields) && in_array($key, self::$update_fields, TRUE) && $value != $this[$key]){
                $upd_fields[$key] = $value;
                $this->data[$key] = $value;
            }
        }
        if (!empty($upd_fields)){
            if ($deferred_update){
                $upd_fields['need_update'] = 1;
            }
            $this->db->query('UPDATE `' . self::MEMBERS_TABLE . '` SET ?a WHERE `email`=?s', $upd_fields, $this->data['email']);
        }
        if (!empty($params['group'])){
            $group_ids = is_array($params['group']) ? $params['group'] : array($params['group']);
            foreach($group_ids as $group_id){
                $this->db->query('REPLACE INTO `' . self::MEMBERS_TO_LIST_TABLE . '` SET `group_id`=?s, `email`=?s', $group_id, $this->data['email']);
                if (!in_array($group_id, $this->data['groups'])){
                    $this->data['groups'][] = $group_id;
                }
            }
//            if ($deferred_update && !$this->data['need_update']){
//                $this->edit(array('need_update' => 1));
//            }
        }
    }
    
    public function asArray(){
        return $this->data;
    }
    
    public function getGroupSubscribeStatus($group_id){
        return in_array($this->getStdGroupId($group_id), $this['groups']);
    }
    
    public function getStdGroupId($group_id){
        if (in_array($group_id, array('tafirenews', 'tatoolsnews'))){
            return $group_id;
        }
        $user = \App\Auth\Users\Factory::getInstance()->getUser(null, array('email' => $this['email']));
        $group_suffix = CatalogConfig::KEY_VARIANT_PRICE;
        if (!empty($user)){
            $type_id = $group_id == 'tatoolsprice' ? CatalogConfig::TOOLS_SECTION_ID : CatalogConfig::FIRE_SECTION_ID;
            $type = \Models\CatalogManagement\Type::getById($type_id);
            $group_suffix = $user->getPriceKey($type);
        }
        return $group_id . str_replace('price', '', $group_suffix);
    }
    
    /*     * ***************************** ArrayAccess **************************** */

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset) {
        if (isset($this->data[$offset])){
            return TRUE;
        }
        return FALSE;
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        return $this->getData($offset);
    }

    /**
     * @param string $offset
     * @param mixed $value
     * @throws \Exception
     */
    public function offsetSet($offset, $value) {
        throw new \Exception(get_class($this) . ' has only immutable Array Access');
    }

    /**
     * @param string $offset
     * @throws \Exception
     */
    public function offsetUnset($offset) {
        throw new \Exception(get_class($this) . ' has only immutable Array Access');
    }
}
