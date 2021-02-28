<?php
/**
 * Description of Review
 *
 * @author olga
 */
namespace Models\CatalogManagement;
class Review implements \ArrayAccess{
    const TABLE = 'reviews';
    const STATUS_NEW = 'new';
    const STATUS_APPROVED = 'approved';
    const STATUS_DECLINE = 'decline';
    private static $view_status = array(self::STATUS_APPROVED);
    private static $allow_statuses = array(self::STATUS_NEW, self::STATUS_APPROVED, self::STATUS_DECLINE);
    private static $registry = array();
    private $data = NULL;
    private $needSave = false;
    private static $loadFields = array('id', 'status', 'item_id', 'name', 'date', 'mark', 'text', 'text_worth', 'text_fault');
    private static $additionalFields = array('timestamp');
    private static $updateFields = array('status', 'name', 'mark', 'text', 'text_worth', 'text_fault');
    /** id товаров, к которым прикреплять отзывы * @var array */
    private static $loadItemsIds = array();
    private static $itemMarks = array();
    /**
     * 
     * @param array $ids
     * @return Review[]
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
            $reviews = $db->query('
                SELECT `'.implode('`, `', self::$loadFields).'`, UNIX_TIMESTAMP(`date`) AS `timestamp`
                FROM `'. self::TABLE .'`
                WHERE `id` IN (?i)',
                    $getIds
            )->select('id');
            foreach ($getIds as $id){
                self::$registry[$id] = !empty($reviews[$id]) ? new Review($reviews[$id]) : NULL;
                self::$loadItemsIds[self::$registry[$id]['item_id']] = self::$registry[$id]['item_id'];
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
     * @return Review
     */
    public static function getById($id){
        if (!empty(self::$registry[$id])){
            return self::$registry[$id];
        }
        $review = self::factory(array($id));
        return !empty($review) ? $review[$id] : NULL;
    }
    /**
     * 
     * @param array $params
     * @return Review[]
     * @throws \LogicException
     */
    public static function search($params, &$count = 0, $start = 0, $limit = 1000000){
        $db = \App\Builder::getInstance()->getDB();
        $order_part = '';
        if (!empty($params['order'])){
            if (is_array($params['order'])){
                foreach ($params['order'] as $key => $desc){
                    $order[] = $key . !empty($desc) ? ' DESC ' : ' ';
                }
                $order_part = implode(', ', $order);
            }else{
                throw new \LogicException('Order param must be an array("key" => "desc")');
            }
        }
        $reviews = $db->query('
            SELECT SQL_CALC_FOUND_ROWS `id` 
            FROM `'.self::TABLE.'` 
            WHERE 1
                { AND `item_id` IN (?i)}
                { AND `status` IN (?l)}
            ' . (!empty($order_part) ? ('ORDER BY ' . $order_part) : '') . 
            'LIMIT ?d, ?d',
                !empty($params['item_ids']) ? (is_array($params['item_ids']) ? $params['item_ids'] : array($params['item_ids'])) : $db->skipIt(),
                !empty($params['status']) ? (is_array($params['status']) ? $params['status'] : array($params['status'])) : $db->skipIt(),
                $start, 
                $limit)
        ->getCol('id', 'id');
        $count = $db->query('SELECT FOUND_ROWS()')->getCell();
        return self::factory($reviews);
    }
    /**
     * 
     * @param int $item_id
     * @return array
     */
    public static function getItemMark($item_id){
        if (!isset(static::$itemMarks[$item_id])){
            static::$itemMarks = \App\Builder::getInstance()->getDB()->query('
                SELECT `item_id`, COUNT(`id`) AS `count_reviews`, AVG(`mark`) AS `user_rating` 
                FROM `'.self::TABLE.'` 
                WHERE `item_id` IN (?l) 
                    AND `status` IN (?l) 
                GROUP BY `item_id`', 
                    (!empty(self::$loadItemsIds) && in_array($item_id, self::$loadItemsIds)) ? self::$loadItemsIds : array($item_id), 
                    self::$view_status)->select('item_id');
        }
        return isset(static::$itemMarks[$item_id]) ? static::$itemMarks[$item_id] : NULL;
    }
    public static function create($item_id){
        $db = \App\Builder::getInstance()->getDB();
        $id = $db->query('INSERT INTO `'.self::TABLE.'` SET `item_id` = ?d, `status` = ?, `date` = NOW()', $item_id, Review::STATUS_NEW);
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
            throw new \LogicException('No key ' . $key . ' in Review');
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
    /**
     * Сохраняет все поля объекта в базу
     */
    public function save() {
        if ($this->needSave) {
            $update_fields = array();
            foreach (self::$loadFields as $field) {
                $update_fields[$field] = $this->data[$field];
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
