<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 03.08.15
 * Time: 18:31
 */

namespace Models\Segments;


class None implements iSegment, \ArrayAccess
{
    const DEFAULT_KEY = 'ru';
    const DEFAULT_TITLE = 'Русский';
    private static $i = NULL;
    private $data = NULL;
    private static $loadFields = array('id', 'title', 'key');
    private static $additionalFields = array();

    /**
     * @return self
     */
    private static function getInstance(){
        if (empty(self::$i)){
            self::$i = new self(array(
                'id' => 0,
                'title' => self::DEFAULT_TITLE,
                'key' => self::DEFAULT_KEY
            ));
        }
        return self::$i;
    }

    private function __construct($data){
        $allow_fields = array_merge(static::$loadFields, static::$additionalFields);
        foreach ($allow_fields as $field){
            $this->data[$field] = !empty($data[$field]) ? $data[$field] : NULL;
        }
    }

    /**
     *
     * @param bool $onSite
     * @return iSegment
     */
    public static function getDefault($onSite = false){
        return self::getInstance();
    }
    /**
     *
     * @param int $segment_id
     * @return iSegment
     */
    public static function getById($segment_id){
        return empty($segment_id) ? self::getInstance() : NULL;
    }
    /**
     *
     * @return iSegment[]
     */
    public static function getAll(){
        return array(0 => self::getInstance());
    }
    /**
     *
     * @param string $key
     * @return iSegment
     */
    public static function getByKey($key){
        return $key == self::DEFAULT_KEY
            ? self::getInstance()
            : NULL;
    }

    public static function create($key, $title = NULL, &$errors = array()){
        throw new \LogicException('Can\'t create segment in ' . get_class());
    }

    public function update(array $params, &$errors = array()){
        throw new \LogicException('Can\'t edit segment in ' . get_class());
    }

    public static function delete($id, &$error = NULL){
        throw new \LogicException('Can\'t delete segment in ' . get_class());
    }
    /**
     * Возвращает посты к страницам для указанного урла
     * @param string $uri - uri страницы, для которой нужны посты
     * @return \Models\ContentManagement\SegmentPost[]
     */
    public function getPagePosts($uri){
        return \Models\ContentManagement\SegmentPost::getPostsListByPageUrl($uri, $this['id']);
    }
    /**
     * @return string
     */
    public function getUrlPrefix(){
        return '';
    }

    public function getData($key){
        if (in_array($key, static::$loadFields) || in_array($key, static::$additionalFields)){
            return $this->data[$key];
        }else{
            throw new \LogicException('No key ' . $key . ' in ' . __CLASS__);
        }
    }

    public function asArray(){
        return $this->data;
    }

    /* ****************************** ArrayAccess **************************** */

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset) {
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