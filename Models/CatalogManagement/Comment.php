<?php
/**
 * Комментарии к товару
 *
 * @author olga
 */
namespace Models\CatalogManagement;
class Comment implements \ArrayAccess{
    const TABLE = 'item_comments';
	const TABLE_VIEW = 'item_comments_user_view';
    const STATUS_NEW = 'new';
    const STATUS_PUBLIC = 'public';
    const STATUS_DELETE = 'delete';
    private static $allow_statuses = array(self::STATUS_NEW, self::STATUS_PUBLIC, self::STATUS_DELETE);
    private static $registry = array();
    private $data = NULL;
    private $needSave = false;
    private static $loadFields = array('id', 'status', 'item_id', 'user_id', 'date', 'text', 'important');
    private static $additionalFields = array('timestamp', 'new');
    private static $updateFields = array('status', 'text', 'important');
    /** id товаров, к которым прикреплять * @var array */
    private static $loadItemsIds = array();
	/** id пользователей, оставивших комментарии * @var array */
	private static $loadUserIds = array();
    /**
     * 
     * @param array $ids
     * @return Comment[]
     */
    public static function factory($ids){
        if (empty($ids)){
            return array();
        }
        $db = \App\Builder::getInstance()->getDB();
        if (!empty(self::$registry)){
            $getIds = array_diff($ids, array_keys(self::$registry));
        }else{
            $getIds = $ids;
        }
        if (!empty($getIds)){
            $db = \App\Builder::getInstance()->getDB();
            $comments = $db->query('
                SELECT `'.implode('`, `', self::$loadFields).'`, UNIX_TIMESTAMP(`date`) AS `timestamp`
                FROM `'. self::TABLE .'`
                WHERE `id` IN (?i)',
                    $getIds
            )->select('id');
            foreach ($getIds as $id){
                self::$registry[$id] = !empty($comments[$id]) ? new Comment($comments[$id]) : NULL;
                self::$loadItemsIds[self::$registry[$id]['item_id']] = self::$registry[$id]['item_id'];
				self::$loadUserIds[self::$registry[$id]['user_id']] = self::$registry[$id]['user_id'];
            }
        }
        $result = array();
        foreach ($ids as $id_result){
            $result[$id_result] = self::$registry[$id_result];
        }
        return $result;
    }
    /**
     * 
     * @param int $id
     * @return Comment
     */
    public static function getById($id){
        if (!empty(self::$registry[$id])){
            return self::$registry[$id];
        }
        $comment = self::factory(array($id));
        return !empty($comment) ? $comment[$id] : NULL;
    }
    /**
	 * 
	 * @param array $params
	 * @param int $count
	 * @param int $start
	 * @param int $limit
	 * @param bool $getOnlyCount
	 * @return self[]
	 * @throws \LogicException
	 */
    public static function search($params, &$count = 0, $start = 0, $limit = 1000000, $getOnlyCount = FALSE){
        $db = \App\Builder::getInstance()->getDB();
        $order_part = '';
        if (!empty($params['order'])){
            if (is_array($params['order'])){
                foreach ($params['order'] as $key => $desc){
                    $order[] = '`c`.`' . $key . '`' . (!empty($desc) ? ' DESC ' : ' ');
                }
                $order_part = implode(', ', $order);
            }else{
                throw new \LogicException('Order param must be an array("key" => "desc")');
            }
        }
        $result = $db->query('
            SELECT SQL_CALC_FOUND_ROWS `c`.`id` 
            FROM `'.self::TABLE.'` AS `c`
				{INNER JOIN `'.self::TABLE_VIEW.'` AS `tv` ON (`tv`.`user_id` = ?d AND `tv`.`item_id` = ?d AND `tv`.`date` < `c`.`date`)}
            WHERE 1
                { AND `c`.`item_id` IN (?i)}
                { AND `c`.`status` IN (?l)}
				{ AND `c`.`user_id` IN (?i)}
            ' . (!empty($order_part) ? ('ORDER BY ' . $order_part) : '') . 
            'LIMIT ?d, ?d',
				!empty($params['new']) && !empty($params['item_id']) ? $params['new'] : $db->skipIt(),
				!empty($params['new']) && !empty($params['item_id']) ? $params['item_id'] : $db->skipIt(),
                !empty($params['item_id']) ? (is_array($params['item_id']) ? $params['item_id'] : array($params['item_id'])) : $db->skipIt(),
                !empty($params['status']) ? (is_array($params['status']) ? $params['status'] : array($params['status'])) : $db->skipIt(),
				!empty($params['user_id']) ? (is_array($params['user_id']) ? $params['user_id'] : array($params['user_id'])) : $db->skipIt(),
                $start, 
                $limit)
        ;
        $count = $db->query('SELECT FOUND_ROWS()')->getCell();
		if ($getOnlyCount){
			return $count;
		}
		$comments = $result->getCol('id', 'id');
        return self::factory($comments);
    }
    public static function create($item_id, $user_id, $data = array()){
        $db = \App\Builder::getInstance()->getDB();
        $id = $db->query('INSERT INTO `'.self::TABLE.'` SET `item_id` = ?d, `user_id` = ?d, `status` = ?, `date` = NOW(){, ?a}', $item_id, $user_id, Comment::STATUS_PUBLIC, !empty($data) ? $data : $db->skipIt());
        return $id;
    }
    public static function getViewStatus(){
        return self::$view_status;
    }
    public static function delete($id = NULL, $item_id = NULL){
        if (empty($id) && empty($item_id)){
            return;
        }
        $db = \App\Builder::getInstance()->getDB();
        return $db->query('DELETE FROM '.self::TABLE.' WHERE 1{ AND `id` = ?d}{ AND `item_id` = ?d}', !empty($id) ? $id : $db->skipIt(), !empty($item_id) ? $item_id : $db->skipIt());
    }
	public static function getLastView($item_id, $user_id){
		$db = \App\Builder::getInstance()->getDB();
		return $db->query('SELECT `date` FROM `'.self::TABLE_VIEW.'` WHERE `item_id` = ?d AND `user_id` = ?d', $item_id, $user_id)->getCell();
	}
	public static function setLastView($item_id, $user_id){
		$db = \App\Builder::getInstance()->getDB();
		$db->query('REPLACE INTO `'.self::TABLE_VIEW.'` SET `item_id` = ?d, `user_id` = ?d, `date` = NOW()', $item_id, $user_id);
	}
    private function __construct($data){
        $allow_fields = array_merge(self::$loadFields, self::$additionalFields);
        foreach ($allow_fields as $field){
            $this->data[$field] = !empty($data[$field]) ? $data[$field] : NULL;
        }
    }
    public function __destruct(){
        $this->save();
    }
    public function getData($key){
        if ($key == 'item'){
            return $this->getItem();
        }elseif (in_array($key, self::$loadFields) || in_array($key, self::$additionalFields)){
            return $this->data[$key];
        }else{
            throw new \LogicException('No key ' . $key . ' in Comment');
        }
    }
    /**
     * Переписывает данные объекта
     * @param string $key
     * @param mixed $value
     * @throws \LogicException
     */
    private function setData($key, $value) {
        if (array_key_exists($key, $this->data)) {
            if (in_array($key, static::$updateFields)){
                $this->data[$key] = $value;
                $this->needSave = true;
            }else{
                throw new \LogicException('Key '.$key.' unchangable');
            }
        }else{
            throw new \LogicException('Key ' . $key . ' not allowed in ' . __CLASS__);
        }
    }
    public function setStatus($status){
        if (in_array($status, self::$allow_statuses)){
            $this->update(array('status' => $status));
        }else{
            throw new \LogicException('Not allow status '. $status);
        }
    }
    /**
     * Редактирование
     * @param array $params
     * @return boolean
     */
    public function update($params) {
        if (empty ($params))
            return true;
        foreach (self::$updateFields as $field){
            if (array_key_exists ($field, $params)){
                $this->setData($field, $params[$field]);
            }
        }
        return true;
    }
    public function getItem(){
        $items = Item::factory(self::$loadItemsIds);
        if (!empty($this['item_id'])){
            return !empty($items[$this['item_id']]) ? $items[$this['item_id']] : NULL;
        }else{
            return NULL;
        }
    }
	public function getUser(){
        $users = \App\Auth\Users\Factory::getInstance()->getUsers(array('ids' => self::$loadUserIds));
        if (!empty($this['user_id'])){
            return !empty($users[$this['user_id']]) ? $users[$this['user_id']] : NULL;
        }else{
            return NULL;
        }
    }
    /**
     * Сохраняет все поля объекта в базу
     */
    private function save() {
        if ($this->needSave) {
            $update_fields = array();
            foreach (self::$loadFields as $field) {
                $update_fields[$field] = ($field == 'important' && empty($this->data['important'])) ? 0 : $this->data[$field];
            }
            \App\Builder::getInstance()->getDB()->query('UPDATE `' . self::TABLE . '` SET ?a WHERE `id` = ?d', $update_fields, $this['id']);
        }
        $this->needSave = false;
    }
    
        /* ****************************** ArrayAccess **************************** */

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return isset($this->data[$offset]) || $offset == 'item';
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

?>
