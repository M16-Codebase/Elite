<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 09.07.15
 * Time: 15:20
 */

namespace Models;


use Models\ImageManagement\Image;

class Teaser implements \ArrayAccess{
    const TABLE = 'teasers';
    const FIELD_ID = 'id';
    const FIELD_IMAGE_ID = 'image_id';
    const FIELD_DESTINATION = 'url';
    const FIELD_ACTIVE = 'active';
    const FIELD_DATE_START = 'date_start';
    const FIELD_DATE_END = 'date_end';
    const FIELD_CANT_ACTIVATE = 'cant_activate';
    private static $loadFields = array('id', 'image_id', 'url', 'link_type', 'title', 'active', 'date_start', 'date_end');
    private static $updateFields = array('image_id', 'url', 'link_type', 'title', 'active', 'date_start', 'date_end');
    private static $additional_fields = array('cant_activate');
    private static $registry = array();

    private $needSave = FALSE;

    private $data = array();

    /**
     * @param array $params
     * @return Teaser[]
     */
    public static function search($params = array()){
        $db = \App\Builder::getInstance()->getDB();
        $public = !empty($params['public']);
        $start = isset($params['start']) && !empty($params['limit']) ? $params['start'] : 0;
        $limit = !empty($params['limit']) ? $params['limit'] : 10000000;
        $entity_ids = $db->query(
            'SELECT `id` FROM `'.self::TABLE.'`'
            . 'WHERE 1'
            . '{ AND `active` = ?d}'
            . '{ AND (`date_start` <= ?s OR `date_start` IS NULL)}'
            . '{ AND (`date_end` >= ?s OR `date_end` IS NULL)}'
            . ($public ? ' ORDER BY RAND()' : '')
            . ' LIMIT ?d, ?d',
            ($public ? 1 : (isset($params['active']) ? $params['active'] : $db->skipIt())),
            $public ? date('Y-m-d') : $db->skipIt(),
            $public ? date('Y-m-d') : $db->skipIt(),
            $start,
            $limit
        )->getCol('id', 'id');
        return static::factory($entity_ids);
    }

    /**
     * Возвращает $count случайных тизеров для паблика
     * @param $count
     * @return Teaser[]
     */
    public static function getPublicTeasers($count){
        return self::search(array('public' => 1, 'limit' => $count));
    }

    /**
     * Возвращает случайный тизер для паблика
     * @return Teaser|null
     */
    public static function getPublicTeaser(){
        $teasers = self::getPublicTeasers(1);
        return reset($teasers);
    }

    /**
     * @param $id
     * @return Teaser|null
     */
    public static function getById($id){
        if (empty($id)){
            return NULL;
        }
        $teasers = self::factory(array($id));
        return reset($teasers);
    }

    /**
     * @param $ids
     * @return Teaser[]
     */
    public static function factory($ids){
        $getIds = array_diff($ids, array_keys(self::$registry));
        if (!empty($getIds)) {
            $db = \App\Builder::getInstance()->getDB();
            $entities = $db->query('
                SELECT `' . implode('`, `', self::$loadFields) . '`
                FROM `' . self::TABLE . '`
                WHERE `id` IN (?i)', $getIds
            )->select('id');
            $image_ids = array_column($entities, 'image_id');
            if (!empty($image_ids)){
                Image::prepare($image_ids);
            }
            foreach ($entities as $id => $entity) {
                self::$registry[$id] = new self($entity);
            }
        }
        $result = array();
        foreach ($ids as $id_result) {
            $result[$id_result] = self::$registry[$id_result];
        }
        return $result;
    }

    private function __construct($data){
        $this->data = $data;
        $this->data[self::FIELD_DATE_START] = !empty($this->data[self::FIELD_DATE_START]) ? strtotime($this->data[self::FIELD_DATE_START]) : NULL;
        $this->data[self::FIELD_DATE_END] = !empty($this->data[self::FIELD_DATE_END]) ? strtotime($this->data[self::FIELD_DATE_END]) : NULL;
        if (!empty($this->data[self::FIELD_DATE_END]) && time() > $this->data[self::FIELD_DATE_END]){
            $this->data[self::FIELD_CANT_ACTIVATE] = 1;
            if ($this->data[self::FIELD_ACTIVE]){
                $this->setData(self::FIELD_ACTIVE, 0);
            }
        } else {
            $this->data[self::FIELD_CANT_ACTIVATE] = 0;
        }
    }

    public function __destruct(){
        $this->save();
    }
    /**
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $FILE
     * @param string $title
     * @param array $errors
     * @return Teaser|false
     */
    public static function create(\Symfony\Component\HttpFoundation\File\UploadedFile $FILE, $title, &$errors = array()){
        $db = \App\Builder::getInstance()->getDB();
        $image = Image::add($FILE, array(), false, $error);
        if (!empty($error)){
            $errors['image'] = $error;
        }
        if (empty($title)){
            $errors['title'] = 'empty';
        }
        if (!empty($errors)){
            return FALSE;
        }
        $data = array(
            'image_id' => $image['id'],
            'title' => $title
        );
        $id = $db->query('INSERT INTO `'.self::TABLE.'` SET ?a', $data);
        return self::getById($id);
    }

    /**
     * @param array $params
     * @param array $errors
     * @return bool
     */
    public function update($params = array(), &$errors = array()){
        if (empty ($params))
            return true;
        if (array_key_exists('title', $params) && empty($params['title'])){
            $errors['title'] = 'empty';
        }
        // Невозможно активировать баннер, временной диапазон показов которого в прошлом
        $date_end = !empty($params[self::FIELD_DATE_END]) ? $params[self::FIELD_DATE_END] : $this[self::FIELD_DATE_END];
        if (!empty($date_end) && $date_end < time()){
            if (!empty($params[self::FIELD_ACTIVE])){
                $errors[self::FIELD_ACTIVE] = 'cant_activate';
            } else {
                $params[self::FIELD_ACTIVE] = 0;
            }
        }
        if (!empty($errors)) {
            return FALSE;
        }
        foreach (self::$updateFields as $field){
            if (array_key_exists ($field, $params)){
                if ($field == 'url' && !empty($params[$field])){
                    $this->makeUrl($params[$field]);
                } else {
                    $this->setData($field, $params[$field]);
                }
            }
        }
        return TRUE;
    }

    /**
     * Удаляем объект
     * @param int $ids
     * @param string $error
     * @return bool
     */
    public static function delete($ids, &$errors = array()) {
        if (empty($ids)){
            $errors['id'] = 'empty';
            return FALSE;
        }
        $ids = is_array($ids) ? $ids : array($ids);
        $deleted_count = \App\Builder::getInstance()->getDB()->query('DELETE FROM `' . self::TABLE . '` WHERE `id` IN (?i)', $ids);
        static::clearRegistry($ids);
        return !empty($deleted_count);
    }
    /**
     * вычистить информацию из реестра
     * @param array $ids
     */
    public static function clearRegistry($ids = array()) {
        if (empty($ids)) {
            $ids = !empty(self::$registry) ? array_keys(self::$registry) : array();
        }
        foreach ($ids as $id) {
            if (!empty(self::$registry[$id])) { //не используем getById, т.к. данная функция может быть использована в factory, т.е. получится бесконечная рекурсия
                /** @var self $entity */
                $entity = self::$registry[$id];
                $entity->save();
                unset(self::$registry[$id]);
            }
        }
    }

    /**
     *
     * @param string $destination url на который ссылается тизер
     * @return array
     */
    private function makeUrl($destination){
        $local_domain = \LPS\Config::getParametr('site', 'url');
        $std_url = preg_replace('~^(http:\/\/)?(www\.)?~', '', $destination);
        if ($local_domain == substr($std_url, 0, strlen($local_domain))){
            $this->setData('url', substr($std_url, strlen($local_domain)));
            $this->setData('link_type', 'local');
        } else {
            $dot_pos = strpos($std_url, '.');
            $slash_pos = strpos($std_url, '/');
            if ($dot_pos !== FALSE && ($dot_pos < $slash_pos || $slash_pos === FALSE)){
                $this->setData('url', $std_url);
                $this->setData('link_type', 'external');
                return array(
                    'url' => $std_url,
                    'type' => 'external'
                );
            } else {
                $this->setData('url', (strpos($std_url, '/') !== 0) ? '/' . $std_url : $std_url);
                $this->setData('link_type', 'local');
            }
        }
    }

    public function save(){
        if ($this->needSave) {
            \App\Builder::getInstance()->getDB()->query('UPDATE `'.self::TABLE.'` SET ?a WHERE `id` = ?d',
                array_map(function($f){
                    $val = $this->getData($f);
                    return in_array($f, array('date_start', 'date_end')) && !empty($val) ? date('Y-m-d', $val) : $val;}, array_combine(static::$updateFields, static::$updateFields)),
                $this['id']);
        }
        $this->needSave = false;
    }

    public function getData($key){
        if ($key == 'image') {
            return Image::getById($this->data['image_id']);
        }
        if (in_array($key, self::$loadFields) || in_array($key, self::$additional_fields)){
            return isset($this->data[$key]) ? $this->data[$key] : NULL;
        }else{
            throw new \LogicException('No key ' . $key . ' in ' . __CLASS__);
        }
    }

    private function setData($key, $value){
        if (array_key_exists($key, $this->data)) {
            if (in_array($key, self::$updateFields)){
                if ($this->data[$key] != $value){
                    $this->data[$key] = $value;
                    $this->needSave = true;
                }
            }else{
                throw new \LogicException('Key '.$key.' unchangable');
            }
        }else{
            throw new \LogicException('Key ' . $key . ' not allowed in ' . __CLASS__);
        }
    }


    /* ****************************** ArrayAccess **************************** */

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return isset($this->data[$offset]) || ($offset == 'image');
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