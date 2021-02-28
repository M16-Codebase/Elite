<?php

/**
 * Класс картинки из коллекции
 *
 * @author olga
 */

namespace Models\ImageManagement;

class CollectionImage extends Image{
    /* Доступный поля объекта из arrayAccess */
    protected static $accessFields = array('id', 'collection_id', 'num', 'width', 'height', 'hidden', 'gravity', 'url', 'info', 'ext', 'last_update');
    /** Поля, которые можно редактировать */
    protected static $updateFields = array('num', 'width', 'height', 'hidden', 'gravity', 'info', 'ext');
    private static $loadCollectionsIds = array();
    protected static $collectionRegistrationQuery = array();
    /**
     * @var Collection
     */
    protected $collection = NULL;

    /**
     * Запоминаем id коллекций, картинки которых нам нужны
     * @param array $collection_ids
     */
    public static function prepare(array $collection_ids){
        self::$loadCollectionsIds = array_merge($collection_ids, self::$loadCollectionsIds);
    }
    /**
     * Загружает картинки
     * вызывется только из коллекции->getImages()
     * @return NULL
     */
    public static function _load(){
        if (empty(self::$loadCollectionsIds)){
            return;
        }
        $db = \App\Builder::getInstance()->getDB();
        //вычисляем все id картинок загружаемых коллекций
        $img_ids = $db->query('SELECT `id` FROM `' . self::TABLE . '` WHERE `collection_id` IN (?i) ORDER BY `num`', self::$loadCollectionsIds)->getCol('id', 'id');
        self::$loadCollectionsIds = array();
        static::factory($img_ids);
    }
    /**
     * При создании коллекции добавляем картинки, которые были созданы до создания коллекции и поставлены в очередь
     * @param \Models\ImageManagement\Collection $collection
     */
    public static function _onCollectionCreate(Collection $collection){
        if (isset(self::$collectionRegistrationQuery[$collection->getId()])){
            $collectionRegistrationQuery = self::$collectionRegistrationQuery[$collection->getId()];
            foreach ($collectionRegistrationQuery as $image){
                /* @var $image CollectionImage */
                $collection->_appendImage($image);
            }
            unset(self::$collectionRegistrationQuery[$collection->getId()]);
        }
    }
    /**
     * При чистке реестра коллекции, что делать с картинками коллекции
     * @param array $ids
     */
    public static function _onCollectionRegistryClear($appendedImages){
        foreach ($appendedImages as $c_id => $images){
            unset(self::$loadCollectionsIds[$c_id]);//намекаем на то, что надо заново загружать
            foreach ($images as $image){
                /* @var $image CollectionImage */
                $image->collection = NULL;//убираем ссылку на коллекцию
                self::$collectionRegistrationQuery[$c_id][$image['id']] = $image;//ставим в очередь на загрузку
            }
        }
    }
    /**
     * Залить картинку
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $FILE
     * @param array $data
     * @param bool $resize
     * @param string $error
     * @return static|NULL
     */
    public static function add(\Symfony\Component\HttpFoundation\File\UploadedFile $FILE, $data = array(), $resize = FALSE, &$error = NULL){
        $text = !empty($data['text']) ? $data['text'] : '';
        $collection_id = !empty($data['collection_id']) ? $data['collection_id'] : NULL;
        $collection = Collection::getById($collection_id);
        if (empty($collection)){
            throw new \InvalidArgumentException('collection #'.$collection_id.' not found');
        }
        $max_num = \App\Builder::getInstance()->getDB()->query('SELECT MAX(`num`) FROM `' . static::TABLE . '` WHERE `collection_id`=?d', $collection_id)->getCell();
        $image = static::create(
            $FILE,
            array(
                'collection_id' => $collection_id,
                'num' => $max_num + 1
            ),
            $resize,
            $error
        );
        if (!empty($error)){
            static::del($image['id']);
            return NULL;
        }
		$collection->_appendImage($image);
		return $image;
    }
    public static function del($id){
        $img = static::getById($id);
        if (!empty($img)){
            $db = \App\Builder::getInstance()->getDB();
            $db->query('UPDATE `' . static::TABLE . '` SET `num`=`num`-1 WHERE `collection_id`=?d AND `num`>?d', $img->getData('collection_id'), $img->getData('num'));
            Collection::getById($img['collection_id'])->_removeImage($img);
        }else{
            return 'already_deleted';
        }
        return parent::del($id);
    }

    /**
     * @param array $data
     * @param null $segment_id
     */
    public function __construct($data, $segment_id = NULL) {
        parent::__construct($data, $segment_id);
        $c_id = $data['collection_id'];
        if (Collection::_exists($c_id)){//если коллекция уже создана, добавляем картинку
            $this->getCollection()->_appendImage($this);
        }else{//если коллекция ещё не создана, ставим в очередь и коллекцию и картинку
            Collection::prepare(array($c_id));
            self::$collectionRegistrationQuery[$c_id][$data['id']] = $this;//ставим в очередь картинку
        }
    }
    public function getCollection(){
        if (empty($this->collection)){
            $this->collection = Collection::getById($this['collection_id']);
        }
        return $this->collection;
    }
    /**
     * Возвращает путь к картинкам
     * @param int $collection_id
     * @param string $type
     * @return string
     */
    protected function getPath($type = 'relative'){
        return $this->getCollection()->getImagesPath($type);
    }
    protected function getDefault(){
        return $this->getCollection()->getDefault();
    }

    /**
     * двигает изображение на заданную позицию
     * @param int $move на какой номер менять позицию
     */
    public function move($move){
        $image_id = $this->getData('id');
        if ($image_id && !empty($move)){
            $old_position = $this->getData('num');
            if ($old_position > $move){
                $this->db->query('UPDATE `' . static::TABLE . '` SET `num`=`num`+1 WHERE `collection_id`=?d AND `num`>=?d AND `num`<?d', $this->getData('collection_id'), $move, $old_position);
            }else{
                $this->db->query('UPDATE `' . static::TABLE . '` SET `num`=`num`-1 WHERE `collection_id`=?d AND `num`<=?d AND `num`>?d', $this->getData('collection_id'), $move, $old_position);
            }
            $this->setData('num', $move);
        }
    }

    static function cleanup(){
        $db = \App\Builder::getInstance()->getDB(); 
        $db->query('
            DELETE `img`
            FROM `'.static::TABLE.'` AS `img`
                LEFT JOIN `'.Collection::TABLE.'` AS `c` ON (`c`.`id` = `img`.`collection_id`)
            WHERE
                `c`.`id` IS NULL 
                AND `img`.`collection_id` IS NOT NULL
        ');
    }
    /******************************* работа с iImageDataProvider *****************************/
    /**
     * @var iImageDataProvider[]
     */
    static protected $dataProviders = array();
    /**
     * @var iImageDataProvider[]
     */
    static protected $dataProvidersByFields = array();
}
