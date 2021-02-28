<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 03.08.15
 * Time: 20:20
 */

namespace Models\Segments;


use Models\Logger;

class Region implements iSegment, \ArrayAccess
{
    const TABLE = 'region';
    const DEFAULT_KEY = 'msk';
    private static $registry = array();
    private $data = NULL;
    private $needSave = array();
    private static $loadFields = array('id', 'title', 'key', 'country', 'fips', 'title_second');
    private static $additionalFields = array();
    private static $updateFields = array('title', 'key', 'country', 'fips', 'title_second');
    private static $default = array();
    private static $loadIds = array();

    /**
     *
     * @param array $ids
     */
    public static function prepare(array $ids){
        if (!empty($ids)){
            $ids = array_diff($ids, array_keys(self::$registry), self::$loadIds);
            if (!empty($ids)){
                self::$loadIds = array_merge($ids, self::$loadIds);
            }
        }
    }
    public static function getDefault($onSite = false){
        $on = $onSite ? 'site' : 'admin';
        if (empty(self::$default[$on])){
            $account = \App\Builder::getInstance()->getAccount();
            if (!($account instanceof \App\Auth\Account\Guest)){
//                self::$default['admin'] = $account->getUser()->getRegion();
                self::$default['admin'] = self::getByKey(self::DEFAULT_KEY);
            }else{
                self::$default['admin'] = self::getByKey(self::DEFAULT_KEY);
            }
            self::$default['site'] = \LPS\Config::isCLI() ? self::getByKey(self::DEFAULT_KEY) : \App\Builder::getInstance()->getRouter()->getSegment();
            if (empty(self::$default['site'])){
                self::$default['site'] = self::getByKey(self::DEFAULT_KEY);
            }
        }
        return self::$default[$on];
    }

    /**
     * @return Region
     * @deprecated т.к. перешли на филиалы. вместо этой используется FilialMapController->getCookieFilial()
     */
    public static function getCookieRegion(){
        $request = \App\Builder::getInstance()->getRequest();
        $region_id = $request->cookies->get('region_id');
        if(!empty($region_id)){
            $def_id = !empty($region_id) ? $region_id : NULL;
            return self::getById($def_id);
        }
        return NULL;
    }
    /**
     *
     * @param array $ids
     * @return Region[]
     */
    public static function factory($ids){
        if (empty($ids)){
            return array();
        }
        $getIds = array_unique(array_merge($ids, self::$loadIds));
        if (!empty(self::$registry)){
            $getIds = array_diff($getIds, array_keys(self::$registry));
        }
        if (!empty($getIds)){
            $db = \App\Builder::getInstance()->getDB();
            $entities = $db->query('
                SELECT `'.implode('`, `', static::$loadFields).'`
                FROM `'. static::TABLE .'`
                WHERE `id` IN (?i)',
                $getIds
            )->select('id');
            foreach ($getIds as $id){
                static::$registry[$id] = !empty($entities[$id]) ? new static($entities[$id]) : NULL;
            }
        }
        $result = array();
        foreach ($ids as $id_result){
            $result[$id_result] = static::$registry[$id_result];
        }
        return $result;
    }
    /**
     *
     * @param int $id
     * @return Region
     */
    public static function getById($id){
        if (!empty(static::$registry[$id])){
            return static::$registry[$id];
        }
        $entity = static::factory(array($id));
        return !empty($entity) ? $entity[$id] : NULL;
    }

    public static function getAll(){
        return self::search();
    }
    /**
     *
     * @param string $key
     * @return Region
     */
    public static function getByKey($key){
        $result = self::search(array('key' => $key));
        return !empty($result) ? reset($result) : NULL;
    }
    public static function getByTitle($title){
        $result = self::search(array('title' => $title));
        return !empty($result) ? reset($result) : NULL;
    }
    public static function getBySecurityKey($security_key){
        $result = self::search(array('security_key' => $security_key));
        return !empty($result) ? reset($result) : NULL;
    }
    /**
     *
     * @param array $params
     * @return Region[]
     * @throws \LogicException
     */
    public static function search($params = array(), &$count = 0, $start = 0, $limit = 1000000){
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
        $entity_ids = $db->query('
            SELECT SQL_CALC_FOUND_ROWS `id`
            FROM `'.static::TABLE.'`
            WHERE 1
            { AND `key` = ?s}
            { AND `title` = ?s}
            ' . (!empty($order_part) ? ('ORDER BY ' . $order_part) : '') .
            'LIMIT ?d, ?d',
            !empty($params['key']) ? $params['key'] : $db->skipIt(),
            !empty($params['title']) ? $params['title'] : $db->skipIt(),
            $start,
            $limit)
            ->getCol('id', 'id');
        $count = $db->query('SELECT FOUND_ROWS()')->getCell();
        return static::factory($entity_ids);
    }
    public static function create($key, $title = NULL, &$errors = array()){
        if (empty($title)){
            $errors['title'] = \Models\Validator::ERR_MSG_EMPTY;
        }
        if (empty($key)){
            $errors['key'] = \Models\Validator::ERR_MSG_EMPTY;
        } elseif (self::checkKeyExists($key)) {
            $errors['key'] = \Models\Validator::ERR_MSG_EXISTS;
        }
        if (!empty($errors)){
            return FALSE;
        }
        $db = \App\Builder::getInstance()->getDB();
        $id = $db->query('INSERT INTO `'.static::TABLE.'` SET `key` = ?s, `title` = ?s', $key, $title);
        Logger::add(array(
            'type' => Logger::LOG_TYPE_CREATE,
            'entity_type' => 'region',
            'entity_id' => $id,
            'additional_data' => array('key' => $key, 'title' => $title)
        ));
        return $id;
    }

    /**
     * @param string $key
     * @param int|null $not_id
     * @return bool
     * @throws \MysqlSimple\Exceptions\InvalidArgumentException
     */
    private static function checkKeyExists($key, $not_id = NULL){
        $db = \App\Builder::getInstance()->getDB();
        return $db->query('SELECT 1 FROM ?# WHERE `key` = ?s{ AND `id` != ?d}',
            self::TABLE,
            $key,
            !empty($not_id) ? $not_id : $db->skipIt())->getCell();
    }
    /**
     * Удаляем объект
     * @param int $id
     * @param string $error
     * @return bool
     */
    public static function delete($id, &$error = NULL) {
        $entity = static::getById($id);
        if (!empty($entity)) {
            $data = $entity->asArray();
            $db = \App\Builder::getInstance()->getDB();
            $db->query('DELETE FROM `' . static::TABLE . '` WHERE `id`=?d', $entity['id']);
            $stores = $entity->getStore();
            if (!empty($stores)){
                foreach ($stores as $s_id => $s){
                    Store::delete($s_id);
                }
            }
            static::clearRegistry(array($id));
            \Models\CatalogManagement\Catalog::onSegmentDelete($id);
            \App\Builder::getInstance()->getAccountController()->onSegmentDelete($id);
            \Models\CatalogManagement\Import\CSV::onSegmentDelete($id);
            \Models\CatalogManagement\Export\FullCSV::onSegmentDelete($id);
            Logger::add(array(
                'type' => Logger::LOG_TYPE_DEL,
                'entity_type' => 'region',
                'entity_id' => $id,
                'additional_data' => $data
            ));
            return true;
        }else{
            $error = 'already deleted';
            return false;
        }
    }
    /**
     * вычистить информацию из реестра
     * @param array $ids
     */
    public static function clearRegistry($ids = array()) {
        if (empty($ids)) {
            $ids = !empty(static::$registry) ? array_keys(static::$registry) : array();
        }
        foreach ($ids as $id) {
            if (!empty(static::$registry[$id])) { //не используем getById, т.к. данная функция может быть использована в factory, т.е. получится бесконечная рекурсия
                $entity = static::$registry[$id];
                $entity->save();
                $entity->clearStores();
                unset(static::$registry[$id]);
            }
        }
    }
    private function __construct($data){
        $allow_fields = array_merge(static::$loadFields, static::$additionalFields);
        foreach ($allow_fields as $field){
            $this->data[$field] = !empty($data[$field]) ? $data[$field] : NULL;
        }
    }
    public function __destruct(){
        $this->save();
    }
    public function getData($key){
        if (in_array($key, static::$loadFields) || in_array($key, static::$additionalFields)){
            return $this->data[$key];
        }else{
            throw new \LogicException('No key ' . $key . ' in ' . __CLASS__);
        }
    }
    /**
     * Переписывает данные объекта
     * @param string $key
     * @param mixed $value
     * @throws \LogicException
     */
    private function setData($key, $value) {
        if (array_key_exists($key, $this->data) && in_array($key, static::$loadFields)) {
            if ($this->data[$key] != $value){
                $this->data[$key] = $value;
                $this->needSave[$key] = $value;
            }
        }else{
            throw new \LogicException('Key ' . $key . ' not allowed in ' . __CLASS__);
        }
    }
    /**
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $FILE
     */
    public function setRegionRouteImage(\Symfony\Component\HttpFoundation\File\UploadedFile $FILE){
        $image_id = $this->getData('region_route_image_id');
        if (empty($image_id)){
            $image = \Models\ImageManagement\Image::add($FILE);
            $this->setData('region_route_image_id', $image['id']);
        } else {
            $image = \Models\ImageManagement\Image::getById($image_id);
            $image->reload($FILE);
        }
    }

    /**
     * Редактирование
     * @param array $params
     * @param array $errors
     * @return bool
     */
    public function update(array $params, &$errors = array()) {
        if (empty ($params))
            return true;
        if (array_key_exists('title', $params) && empty($params['title'])) {
            $errors['title'] = \Models\Validator::ERR_MSG_EMPTY;
        }
        if (array_key_exists('key', $params)){
            if (empty($params['key'])){
                $errors['key'] = \Models\Validator::ERR_MSG_EMPTY;
            } elseif (self::checkKeyExists($params['key'], $this['id'])) {
                $errors['key'] = \Models\Validator::ERR_MSG_EXISTS;
            }
        }
        if (!empty($errors)){
            return FALSE;
        }
        foreach (static::$updateFields as $field){
            if (array_key_exists ($field, $params)){
                $this->setData($field, $params[$field]);
            }
        }
        $this->save();
        return true;
    }
    /**
     * Сохраняет все поля объекта в базу
     */
    public function save() {
        if (!empty($this->needSave)){
            \App\Builder::getInstance()->getDB()->query('UPDATE `' . static::TABLE . '` SET ?a WHERE `id` = ?d', $this->needSave, $this['id']);
            foreach ($this->needSave as $field => $value) {
                Logger::add(array(
                    'type' => Logger::LOG_TYPE_EDIT,
                    'entity_type' => 'region',
                    'entity_id' => $this['id'],
                    'attr_id' => $field,
                    'comment' => $value,
                    'additional_data' => $this->asArray()
                ));
            }
        }
        $this->needSave = array();
    }

    public function asArray(){
        return $this->data;
    }

    /**
     * Возвращает посты к страницам для указанного uri
     * @param string $uri - uri страницы, для которой нужны посты
     * @return \Models\ContentManagement\SegmentPost[]
     */
    public function getPagePosts($uri){

    }
    /**
     * @return string
     */
    public function getUrlPrefix(){
        return $this['key'] != self::DEFAULT_KEY ? '/'.$this['key'] : '';
    }

    /* ****************************** ArrayAccess **************************** */

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset) {
        if ($offset == 'region_route_image'){
            return isset($this->data['region_route_image_id']);
        }
        return isset($this->data[$offset]);
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