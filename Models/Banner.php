<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Models;
use Models\ImageManagement\Image;
class Banner implements \ArrayAccess{
    const TABLE_BANNER = 'banners';
    const TABLE_URI = 'banners_uri';
    const FIELD_ID = 'id';    
    const FIELD_IMAGE_ID = 'image_id';
    const FIELD_DESTINATION = 'destination';
    const FIELD_POSITION = 'position';
    const FIELD_ACTIVE = 'active';
    const FIELD_BANNER_ID = 'banner_id';
    const FIELD_DATE_START = 'date_start';
    const FIELD_DATE_END = 'date_end';
    const FIELD_TOP = 'top';
    const FIELD_URI = 'uri';
    const FIELD_CANT_ACTIVATE = 'cant_activate';
    private static $registry = array();
    private $data = NULL;
    private $needSave = false;
    private $urlChanged = false;
    private $urlLoaded = false;
    private static $loadFields = array('id', 'image_id', 'destination', 'link_type', 'title', 'description', 'active', 'date_start', 'date_end', 'top', 'showmode', 'seconds', 'segment_id');
    private static $updateFields = array('image_id', 'destination', 'link_type', 'title', 'description', 'active', 'date_start', 'date_end', 'top', 'showmode', 'seconds', 'segment_id');
    private static $additionalFields = array('url', 'positions', 'current_url', 'position', 'cant_activate');
    private static $uriFields = array('banner_id', 'uri');

    /**
     *
     * @param array $ids
     * @return Banner[]
     */
    public static function factory(Array $ids, $uri = '/'){
        $getIds = array_diff($ids, array_keys(self::$registry));
        if (!empty($getIds)) {
            $db = \App\Builder::getInstance()->getDB();
            $entities = $db->query('
                SELECT `' . implode('`, `', self::$loadFields) . '`
                FROM `' . self::TABLE_BANNER . '`
                WHERE `'.self::FIELD_ID.'` IN (?i)', $getIds
            )->select('id');
            foreach ($getIds as $id) {
                self::$registry[$id] = isset($entities[$id]) ? new self($entities[$id], $uri) : NULL;
            }
        }
        $result = array();
        foreach ($ids as $id_result) {
            $result[$id_result] = self::$registry[$id_result];
        }
        return $result;
    }
    
    private function __construct(Array $data, $uri){
        Image::prepare(array($data['image_id']));
        $this->data = $data;
        $this->data[self::FIELD_DATE_START] = !empty($this->data[self::FIELD_DATE_START]) ? strtotime($this->data[self::FIELD_DATE_START]) : NULL;
        $this->data[self::FIELD_DATE_END] = !empty($this->data[self::FIELD_DATE_END]) ? strtotime($this->data[self::FIELD_DATE_END]) : NULL;
        $this->data['current_url'] = is_array($uri) ? reset($uri) : $uri;
        if (!empty($this->data[self::FIELD_DATE_END]) && time() > $this->data[self::FIELD_DATE_END]){
            $this->data[self::FIELD_CANT_ACTIVATE] = 1;
            if ($this->data[self::FIELD_ACTIVE]){
                $this->setData(self::FIELD_ACTIVE, 0);
            }
        } else {
            $this->data[self::FIELD_CANT_ACTIVATE] = 0;
        }
    }
    /**
     *
     * @param int $id
     * @param string $uri
     * @return Banner
     */
    public static function getById($id, $uri = '/'){
        if (!empty(self::$registry[$id])){
            return self::$registry[$id];
        }
        $entity = self::factory(array($id), $uri);
        return !empty($entity) ? $entity[$id] : NULL;
    }
    /**
     *
     * @param array $params
     * @return Banner[]
     * @throws \LogicException
     */
    public static function search($params = array(), $start = 0, $limit = 1000000){
        $db = \App\Builder::getInstance()->getDB();
        $where_urls = array();
        if (empty($params['url']) && empty($params['not_url'])) {
            $url = trim(preg_replace('~^(http://)?(www.)?(' . $_SERVER['SERVER_NAME'] . ')?~', '', $_SERVER['REQUEST_URI']));
            while (strpos($url, '/') !== FALSE) {
                if (empty($where_urls)) {
                    $where_urls[] = $url;
                }
                $where_urls[] = $url . '*';
                $url = substr($url, 0, strlen($url) - 1);
                $url = substr($url, 0, strrpos($url, '/') + 1);
            }
            $params['url'] = $where_urls;
        }
        if (empty($params['url'])){
            $params['url'] = '/';
        }
        if (!isset($params['date_filter'])) {
            $params['date_filter'] = true;
        }
        $secondary_order = ((!empty($params['order']) && $params['order'] == 'admin') ? '`tUri`.`position`' : 'RAND()');
        $entity_ids = $db->query(
            'SELECT `id`, IF(  `top` = 1,  `tUri`.`position` , 9999999 ) AS `split_pos` FROM '
                . '`'.self::TABLE_BANNER.'` AS `tB` INNER JOIN '
                . '`'.self::TABLE_URI.'` AS `tUri` ON `tB`.`id` = `tUri`.`banner_id` '
                . 'WHERE 1'
                . '{ AND `tUri`.`uri` IN (?l)}'
//                . '{ AND (`tUri`.`uri` NOT IN (?l) OR `tUri`.`uri` IS NULL)}'
                . '{ AND `tB`.`active` = ?d}'
                . '{ AND (`date_start` <= ?s OR `date_start` IS NULL)}'
                . '{ AND (`date_end` >= ?s OR `date_end` IS NULL)}'
                . '{ AND (`segment_id` = ?d OR `segment_id` IS NULL)}'
                . ' GROUP BY `tUri`.`banner_id`'
                . ' ORDER BY `split_pos`, ' . $secondary_order
                . ' LIMIT ?d, ?d',
                !empty($where_urls) ? $where_urls : (!empty($params['url']) ? (is_array($params['url']) ? $params['url'] : array($params['url'])) : $db->skipIt()),
//                !empty($params['not_url']) ? (is_array($params['not_url']) ? $params['not_url'] : array($params['not_url'])) : $db->skipIt(),
                isset($params['active']) ? $params['active'] : $db->skipIt(),
                $params['date_filter'] ? date('Y-m-d') : $db->skipIt(),
                $params['date_filter'] ? date('Y-m-d') : $db->skipIt(),
                !empty($params['segment_id']) ? $params['segment_id'] : $db->skipIt(),
                $start,
                $limit
        )->getCol('id', 'id');
        return static::factory($entity_ids, $params['url']);
    }
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $FILE
     * @param array|string $page_uris
     * @param string $dest
     * @param array $errors
     * @return Banner|false
     */
    public static function create(\Symfony\Component\HttpFoundation\File\UploadedFile $FILE, $page_uris = '/', $dest = NULL, &$errors = array()){
        $db = \App\Builder::getInstance()->getDB();
        $image = Image::add($FILE, array(), false, $error);
        if (!empty($error)){
            $errors['image'] = $error;
        }
        if (empty($page_uris) || is_array($page_uris) && empty(array_filter($page_uris))){
            $errors['url'] = 'empty';
        } elseif (!is_array($page_uris)){
            $page_uris = array($page_uris);
        }
        if (!empty($errors)){
            return FALSE;
        }
        $data = array(
            'image_id' => $image['id'],
            'destination' => $dest,
        );
        $id = $db->query('INSERT INTO `'.self::TABLE_BANNER.'` SET ?a', $data);
        foreach($page_uris as $uri){
            $position = $db->query('SELECT MAX(`position`) FROM `' . self::TABLE_URI . '` WHERE `uri` = ?s', $uri)->getCell() + 1;
            $db->query('INSERT INTO `'.self::TABLE_URI.'` SET `banner_id` = ?d, `uri` = ?s, `position` = ?d', $id, $uri, $position);
        }
        return self::getById($id);
    }
    /**
     * Удаляем объект
     * @param int $id
     * @param string $error
     * @return bool
     */
    public static function delete($id, &$error = NULL) {
        $entity = self::getById($id);
        if (empty($id)){
            $error = 'empty';
            return FALSE;
        }
        if (!empty($entity)) {
            $db = \App\Builder::getInstance()->getDB();
            $db->query('DELETE FROM `' . self::TABLE_URI . '` WHERE `'.self::FIELD_BANNER_ID.'`=?d', $id);
            $db->query('DELETE FROM `' . self::TABLE_BANNER . '` WHERE `'.self::FIELD_ID.'`=?d', $id);
            static::clearRegistry(array($id));
            return true;
        }else{
            $error = 'not_found';
            return false;
        }
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
                $entity = self::$registry[$id];
                $entity->save();
                unset(self::$registry[$id]);
            }
        }
    }
    /**
     * сменить порядок показа баннера
     * @param int $new_position новая позиция баннера
     */
    public function move($new_position){
        $url = $this['current_url'];
        if (empty($url)){
            throw new \Exception('Неизвестен урл группы для изменения сортировки');
        }
        if (!$this['top']){
            return FALSE; // Сортируются только зафиксированные баннеры
        }
        $db = \App\Builder::getInstance()->getDB();
        $old_position = $this->getData('position');
//        $ids = $db->query('SELECT `banner_id` AS `id` FROM `' . self::TABLE_URI . '` WHERE `uri` IN (?l) OR `uri` IS NULL', is_array($this['url']) ? $this['url'] : array($this['url']))->getCol('id', 'id');
        if ($new_position < $old_position) {
            $db->query('
                    UPDATE `' . self::TABLE_URI . '`
                    SET `position`=`position`+1
                    WHERE `position`>=?d AND `position`<?d AND `uri` = ?s', $new_position, $old_position, $url
            );
        } else {
            $db->query('
                    UPDATE `' . self::TABLE_URI . '`
                    SET `position`=`position`-1
                    WHERE `position`<=?d AND `position`>?d AND `uri` = ?s', $new_position, $old_position, $url
            );
        }
        $db->query('UPDATE `' . self::TABLE_URI . '` SET `position`=?d WHERE `banner_id` = ?d', $new_position, $this['id']);
        $this->data['positions'][$url] = $new_position;
        return true;
    }
    
    public function __destruct(){
        $this->save();
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getData($key){
        if ($key == 'image') {
            return Image::getById($this->data['image_id']);
        }
        if (in_array($key, array('url', 'positions', 'position')) && !$this->urlLoaded){
            $db = \App\Builder::getInstance()->getDB();
            $this->data['positions'] = $db->query('SELECT `uri`, `position` FROM `'.self::TABLE_URI.'` WHERE `banner_id` = ?d', $this['id'])->getCol('uri', 'position');
            $this->data['url'] = array_keys($this->data['positions']);
            $this->urlLoaded = true;
        }
        if ($key == 'position'){
            return !empty($this->data['positions'][$this->data['current_url']]) ? $this->data['positions'][$this->data['current_url']] : NULL;
        }
        if (in_array($key, self::$loadFields) || in_array($key, self::$additionalFields)){
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
        if (array_key_exists($key, $this->data)) {
            if (in_array($key, self::$loadFields)){
                $this->data[$key] = (!empty($value) || in_array($key, array('active', 'top'))) ? $value : null;
                $this->needSave = true;
            }else{
                throw new \LogicException('Key '.$key.' unchangable');
            }
        }else{
            throw new \LogicException('Key ' . $key . ' not allowed in ' . __CLASS__);
        }
    }
    /**
     * Редактирование
     * @param array $params
     * @return boolean
     */
    public function update($params, &$errors = array()) {
        if (empty ($params))
            return true;
        // Невозможно активировать баннер, временной диапазон показов которого в прошлом
        $date_end = !empty($params[self::FIELD_DATE_END]) ? $params[self::FIELD_DATE_END] : $this[self::FIELD_DATE_END];
        if (!empty($date_end) && $date_end < time()){
            if (!empty($params[self::FIELD_ACTIVE])){
                $errors[self::FIELD_ACTIVE] = 'cant_activate';
            } else {
                $params[self::FIELD_ACTIVE] = 0;
            }
        }
        if (array_key_exists('top', $params)) {
            $db = \App\Builder::getInstance()->getDB();
            if ($params['top'] == 1){
                $pos = $db->query(
                    'SELECT MAX(  `position` ) AS  `position` '
                    .' FROM  `'.self::TABLE_BANNER.'` AS `b` INNER JOIN `' . self::TABLE_URI . '` AS `u` ON `b`.`id` = `u`.`banner_id`'
                    .' WHERE  `b`.`top` = 1 AND `u`.`uri` = ?s',
                    $this['current_url']
                )->getCell();
                if (empty($pos)) $pos = 0;
                if ($pos > $this['position']) {
                    $this->move($pos);
                }
            } else {
                $pos = $db->query(
                    'SELECT MIN(  `position` ) AS  `position` '
                    .' FROM  `'.self::TABLE_BANNER.'` AS `b` INNER JOIN `' . self::TABLE_URI . '` AS `u` ON `b`.`id` = `u`.`banner_id`'
                    .' WHERE  `b`.`top` = 1 AND `u`.`uri` = ?s',
                    $this['current_url']
                )->getCell();
                if (empty($pos)) $pos = 65535;
                if ($pos < $this['position']) {
                    $this->move($pos);
                }
            }
        }
        foreach (self::$updateFields as $field){
            if (array_key_exists ($field, $params)){
                if ($field == 'destination'){
                    $this->makeUrl($params[$field]);
                } else {
                    $this->setData($field, $params[$field]);
                }
            }
        }
        if (!empty($params['url'])){
            $params['url'] = self::checkUrls($params['url']);
            if (!empty(array_diff($params['url'], $this['url'])) || !empty(array_diff($this['url'], $params['url']))){
                $this->data['url'] = $params['url'];
                $this->urlChanged = TRUE;
                $this->needSave = TRUE;
                $this->urlLoaded = TRUE;
            }
        }
        if (!empty($params['image'])){
            $error = $this['image']->reload($params['image']);
            if (!empty($error)){
                $errors['image'] = $error;
                return false;
            }
        }
        // Устанавливаем флаг невозможности активации баннера
        $this->data[self::FIELD_CANT_ACTIVATE] = !empty($this->data[self::FIELD_DATE_END]) && $this->data[self::FIELD_DATE_END] < time() ? 1 : 0;
        return true;
    }
    
    public function save(){
        if ($this->needSave) {
            $db = \App\Builder::getInstance()->getDB();
            if ($this->urlChanged) {
                $old_urls = $db->query('SELECT `uri` FROM `'.self::TABLE_URI.'` WHERE `'.self::FIELD_BANNER_ID.'` = ?d', $this['id'])->getCol(NULL, 'uri');
                $add_urls = array_diff($this->data['url'], $old_urls);
                $delete_urls = array_diff($old_urls, $this->data['url']);
                if (!empty($delete_urls)){
                    $db->query('DELETE FROM `'.self::TABLE_URI.'` WHERE `'.self::FIELD_BANNER_ID.'` = ?d AND `uri` IN (?l)', $this['id'], $delete_urls);
                }
                if (!empty($add_urls)){
                    foreach($add_urls as $uri){
                        $position = $db->query('SELECT MAX(`position`) FROM `' . self::TABLE_URI . '` WHERE `uri` = ?s', $uri)->getCell() + 1;
                        $db->query('INSERT INTO `'.self::TABLE_URI.'` SET `banner_id` = ?d, `uri` = ?s, `position` = ?d', $this['id'], $uri, $position);
                    }
                }
                $this->urlChanged = FALSE;
            }
            $db->query('UPDATE `'.self::TABLE_BANNER.'` SET ?a WHERE `id` = ?d',
                array_map(function($f){
                    $val = $this->getData($f);
                    return in_array($f, array('date_start', 'date_end')) && !empty($val) ? date('Y-m-d', $val) : $val;}, array_combine(static::$updateFields, static::$updateFields)),
                $this['id']);
        }
        $this->needSave = false;
    }
    /**
     * 
     * @param string $destination url на который ссылается баннер
     */
    private function makeUrl($destination){
        $local_domain = \LPS\Config::getParametr('site', 'url');
        $std_url = preg_replace('~^(http:\/\/)?(www\.)?~', '', $destination);
        if ($local_domain == substr($std_url, 0, strlen($local_domain))){
            $this->setData('destination', substr($std_url, strlen($local_domain)));
            $this->setData('link_type', 'local');
        } else {
            $dot_pos = strpos($std_url, '.');
            $slash_pos = strpos($std_url, '/');
            if ($dot_pos !== FALSE && ($dot_pos < $slash_pos || $slash_pos === FALSE)){
                $this->setData('destination', $std_url);
                $this->setData('link_type', 'external');
                return array(
                    'destination' => $std_url,
                    'type' => 'external'
                );
            } else {
                $this->setData('destination', (strpos($std_url, '/') !== 0) ? '/' . $std_url : $std_url);
                $this->setData('link_type', 'local');
            }
        }
    }

    public static function checkUrls($urls){
        if (!empty($urls)){
            foreach ($urls as &$url){
                $url = Validator::getInstance()->getRelativeUrl($url);
            }
        }
        return $urls;
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