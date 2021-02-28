<?php
/**
 * Коллекция картинок
 *
 * @author olga
 */
namespace Models\ImageManagement;
use LPS\Config;
use Models\ImageManagement\CollectionImage;
class Collection implements \ArrayAccess{
    const TABLE = 'image_collection';
    const PATH = '/data/images/';
    const COLLECTION_PATH = 'collections';
    const DEFAULT_COLLECTION_ID = 1;
    const DEFAULT_IMAGE_ID = NULL;
    const MAX_REGISTRY_LEN = 300;
    protected static $registry = array();
    private static $loadIds = array();
    private $id = NULL;
	private $data = NULL;
    protected  $needSave = false;
    private $images = array();
    private $sorted = false;
    protected static $loadFields = array('id', 'cover', 'data', 'default', 'type', 'position');
    protected static $updateFields = array('cover', 'data', 'default', 'type', 'position');
    protected static $allowInfoFields = array();
    protected static $additionalFields = array('cover_id');
    /* типы коллекций (прописаны enum в базе) */
    const TYPE_DEFAULT = 'Default'; //дефолтные картинки для обложек (постов, типов, товаров, файлов, производителей)
    const TYPE_COMMENT = 'Comment'; //картинки комментов
    const TYPE_ITEMS_DEFAULT = 'ItemsDefault'; //дефолтные картинки обложек товаров в зависимости от типа
    const TYPE_TYPE_COVER = 'TypeCover'; //картинки обложек типов товаров
    const TYPE_FILES = 'Files'; //обложки файлов
    const TYPE_MANUF = 'Manuf'; //обложки производителей
    const TYPE_PROPERTY = 'Property'; //обложки свойств
    const TYPE_PROPERTY_VALUE = 'PropertyValue';//картинки в свойствах-галереях
    
    private static $classTypes = array(
        self::TYPE_DEFAULT => 'Models\ImageManagement\DefaultImageCollection',
        self::TYPE_COMMENT => 'Models\ContentManagement\CommentImageCollection',
        self::TYPE_ITEMS_DEFAULT => 'Models\CatalogManagement\ItemsDefaultImageCollection',
        self::TYPE_TYPE_COVER => 'Models\CatalogManagement\TypeCoverImageCollection',
        self::TYPE_FILES => 'Models\FilesManagement\FilesImageCollection',
        self::TYPE_MANUF => 'Models\CatalogManagement\ManufImageCollection',
        self::TYPE_PROPERTY => 'Models\CatalogManagement\PropertyImageCollection',
        self::TYPE_PROPERTY_VALUE => 'Models\CatalogManagement\PropertyValueImageCollection'
    );
    /**
     * Подготовка ids для загрузки
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

    /**
     * Фабрика коллекций
     * @param array $ids
     * @param null $segment_id
     * @return Collection[]
     */
    public static function factory($ids = array(), $segment_id = NULL){
        $getIds = array_unique(array_merge($ids, self::$loadIds));
        if (count(self::$registry) + count($getIds) > self::MAX_REGISTRY_LEN){
            self::clearRegistry();
        }
        if (!empty(self::$registry)){
            $getIds = array_diff($getIds, array_keys(self::$registry));
        }
        if (!empty($getIds)){
            $db = \App\Builder::getInstance()->getDB();
            $collections = $db->query(
                'SELECT `'.implode('`,`', static::$loadFields).'`
                FROM `'.static::TABLE.'`
                WHERE `id` IN (?i)',
                $getIds
            )->select('id');
            foreach ($getIds as $id){
                if (!empty($collections[$id])){
                    $class = self::$classTypes[$collections[$id]['type']];
                    self::$registry[$id] = !empty($collections[$id]) ? new $class($collections[$id], $segment_id): NULL;
                }else{
                    self::$registry[$id] = NULL;
                }
            }
            self::$loadIds = array();
            CollectionImage::prepare($getIds);//подготавливаем картинки из коллекций к загрузке
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
     * @param null $segment_id
     * @return Collection
     */
    public static function getById($id, $segment_id = NULL){
        if (!empty(self::$registry[$id])){
            return self::$registry[$id];
        }
        $collections = static::factory(array($id), $segment_id);
        return !empty($collections[$id]) ? $collections[$id] : NULL;
    }
    /**
     * Внутренняя функция для использования в CollectionImage
     * @see \Models\ImageManagement\CollectionImage::__construct()
     * @param int $id коллекции
     * @return Boolean
     */
    public static function _exists($id){
        if (isset(self::$registry[$id])){
            return true;
        }else{
            return false;
        }
    }
    /**
     * Создаём коллекцию
     * @param array $data доп инфа
     * @return bool
     */
    public static function create($type = self::TYPE_DEFAULT, $data = array(), $position = NULL, &$errors = NULL){
        foreach (static::$dataProviders as $p){
            $p->preCreate($type, $data, $position, $errors);
        }
        $classType = self::$classTypes[$type];
        $data = $classType::packData($data);
        $db = \App\Builder::getInstance()->getDB();
        $collection_id = $db->query('INSERT INTO `'.self::TABLE.'` SET `data` = ?s, `type` = ?s, `position` = ?d', $data, $type, $position);
        foreach (static::$dataProviders as $p){
            $p->onCreate($collection_id, $type, $data, $position, $errors);
        }
        return $collection_id;
    }

    /**
     * @param null $errors
     * @param array $img_url_match – соответствие урлов картинки для замены ссылок в тексте
     * @return bool|Collection
     */
    public function copy(&$errors = NULL, &$img_url_match = array()) {
        $collection_id = static::create($this['type'], $this['data'], $this['position'], $errors);
        $collection = !empty($collection_id) ? static::getById($collection_id) : NULL;
        if (empty($collection)) {
            return FALSE;
        }
        $images = $this->getImages();
        foreach($images as $image){
            $new_image = $image->copy(array('collection_id' => $collection_id), $errors);
            $img_url_match['old'][$image['id']] = $image->getUrl();
            $img_url_match['new'][$image['id']] = $new_image->getUrl();
            if ($image['id'] == $this->data['cover']) {
                $collection->setCover($new_image['id']);
            }
        }
        return $collection;
    }

    public static function delete($id){
        $collection = static::getById($id);
        if (empty($collection)){
            return 'no exists';
        }
        $images = $collection->getImages();
        foreach ($images as $image){
            CollectionImage::del($image['id']);
        }
        $db = \App\Builder::getInstance()->getDB();
        $db->query('DELETE FROM `'.static::TABLE.'` WHERE `id` = ?d', $id);
        foreach (self::$dataProviders as $p){
            $p->onDelete($collection);
        }
        self::clearRegistry(array($collection['id']));
        return true;
    }

    /**
     * Очищаем реестр
     * @param type $ids
     */
    public static function clearRegistry($ids = array()){
        if (empty($ids)){
            $ids = !empty(self::$registry) ? array_keys(self::$registry) : array();
        }
        $appendedImages = array();
        foreach ($ids as $c_id){
            if (!empty(self::$registry[$c_id])){
                $collection = self::$registry[$c_id];
                /* @var $collection Collection */
                $collection->save();
                $appendedImages[$c_id] = $collection->images;
                unset(self::$registry[$c_id]);
            }
        }
        CollectionImage::_onCollectionRegistryClear($appendedImages);
    }

    protected static function packData($data){
        return serialize($data);
    }
    protected static function unpackData($data){
        return unserialize($data);
    }
    protected function __construct($data, $segment_id = NULL){
        $this->id = $data['id'];
		$this->data = $data;
		$this->data['data'] = static::unpackData($this->data['data']);
        $this->data['segment_id'] = $segment_id;
        CollectionImage::_onCollectionCreate($this);
    }
    public function __destruct(){
        $this->save();
    }
    public function getId(){
        return $this->id;
    }
    public function getCover(){
        $images = $this->getImages();//Удостоверяемся, что все картинки загружены
        return !empty($this->data['cover']) && !empty($images[$this->data['cover']]) ? $images[$this->data['cover']] : NULL;
    }
    protected function sortImages(){
        if ($this->sorted){
            return true;
        }
        uasort($this->images, function(Image $a, Image $b){
            if ($a['num'] == $b['num']){
                return 0;
            }elseif ($a['num'] > $b['num']){
                return 1;
            }else{
                return -1;
            }
        });
        $this->sorted = true;
    }
    /**
     *
     * @return Image[]
     */
    public function getImages(){
        CollectionImage::_load();//перед тем как взять картинки, надо быть уверенными, что они загружены в реестр
        $this->sortImages();
        return $this->images;
    }
    public function getPath(){
        return $this['path'];
    }
    public function getDefault(){
        return !empty($this->data['default']) ? static::getById($this->data['default']) : Image::getById(static::DEFAULT_IMAGE_ID);
    }
    /**
     * Добавляем информацию о картинках в коллекцию
     * (вызывается только в factory картинок.)
     * @param CollectionImage $image
     */
    public function _appendImage(CollectionImage $image){
        $this->images[$image['id']] = $image;
        $this->sorted = false;
    }
    public function _removeImage($image){
        if (!empty($this->images[$image['id']])){
            if($this->data['cover'] == $image['id']){
                $this->data['cover'] = NULL;
                $this->needSave = true;
            }
            unset($this->images[$image['id']]);
        }
    }
	/**
     * Возвращает данные объекта
     * @param string $key
     * @return mixed
     * @throws \LogicException
     */
    public function getData($key) {
        if (in_array($key, static::$loadFields)){
            return $this->data[$key];
		}elseif($key == 'path'){
			return $this->getImagesPath();
        }elseif($key == 'cover_id'){
            return $this->data['cover'];
		}elseif ($key == 'cover'){
			return $this->getCover();
		}elseif ($key == 'default'){
			return $this->getDefault();
		}elseif ($key == 'images') {
			return $this->getImages();
		} else {
            throw new \LogicException('Для объекта изображения не предусмотрен параметр ' . $key);
        }
    }

    /**
     * Переписывает данные объекта
     * @param string $key
     * @param mixed $value
     * @throws \LogicException
     */
    protected function setData($key, $value) {
        if (!array_key_exists($key, $this->data)) {
            throw new \LogicException('Для объекта коллекции не предусмотрен параметр ' . $key);
        }
        if (!in_array($key, static::$updateFields)){
            throw new \LogicException('Поле '.$key.' нельзя редактировать');
        }
        if ($this->data[$key] == $value){
            return FALSE;
        }
        $this->data[$key] = $value;
        $this->needSave = TRUE;
        return TRUE;
    }

    /**
     * Обновляем данные коллекции
     * @param array $params
     * @param array $errors
     * @return bool
     * @throws \Exception
     */
    public function update($params, &$errors = array()){
        foreach (static::$dataProviders as $p){
            $p->preUpdate($this, $params, $errors);
        }
        $changed = FALSE;
        foreach ($params as $pk => $pv){
            if (!in_array($pk, static::$updateFields)){
                throw new \Exception('Нельзя редактировать поле «' . $pk . '»');
            }
            if ($this->setData($pk, $pv)){
                $changed = TRUE;
            }
        }
        foreach (static::$dataProviders as $p){
            $p->onUpdate($this, $params, $errors);
        }
        return $changed;
    }
    public function setCover($id){
        return $this->update(array('cover' => $id));
    }
    public function save(){
        if ($this->needSave){
            $db = \App\Builder::getInstance()->getDB();
            $update_fields = array();
            foreach(static::$updateFields as $field) {
			    $update_fields[$field] = !empty($this->data[$field]) ? $this->data[$field] : null;
            }
            $update_fields['data'] = static::packData($update_fields['data']);
            $db->query('UPDATE `'.self::TABLE.'` '
                . 'SET ?a '
                . ' WHERE `id` = ?d',
                $update_fields, 
                $this->id);
        }
        $this->needSave = false;
    }
    /**
     * Добавить картинку в коллекцию
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $FILE
     * @param string $text
     * @param array $error
	 * @return CollectionImage
     */
    public function addImage(\Symfony\Component\HttpFoundation\File\UploadedFile $FILE = null, $text = '', &$error = NULL, $resize = FALSE){
        $images = $this->getImages();
        foreach ($images as $img){
			if (empty($img['ext'])){//это только что загруженная картинка
				continue;
			}
            $full_path = Config::getRealDocumentRoot() . $img->getUrl();
			if (!file_exists($full_path)){
				continue;
			}
            if (md5_file($full_path) == md5_file($FILE->getRealPath())){
                $error = 'В коллекции уже есть такое изображение';
                return $img;
            }
        }
        $image = CollectionImage::add($FILE, array('text' => $text, 'collection_id' => $this->id), $resize, $error);
        return $image;
    }

    public function addImageByUrl($url, &$error){
		$tmp_path = Config::getRealDocumentRoot() . '/data/images/temp/';
		$tmp_name = basename($url);
		if (!file_exists($tmp_path)){
			\LPS\Components\FS::makeDirs($tmp_path);
		}
		try{
			$Headers = @get_headers($url);
			// проверяем ответ от сервера. с кодом 200 - ОК
			if(strpos($Headers[0], '200')) {
				copy($url, $tmp_path . $tmp_name);
				$FILE = new \Symfony\Component\HttpFoundation\File\UploadedFile($tmp_path . $tmp_name, basename($url));
			} else {
				$error = 'файл не найден';
				return FALSE;
			}
		} catch (\Exception $e) {
			$error = 'файл не найден';
			return FALSE;
		}
		$result = $this->addImage($FILE, '', $error);
		if (file_exists($tmp_path . $tmp_name)){
			unlink($tmp_path . $tmp_name);
		}
        return $result;
    }

    /**
     * Путь к картинкам коллекции
     * @param int $collection_id
     * @param string $type
     * @return string
     */
    public function getImagesPath($type='relative'){
        $collection_id = $this->getId();
    	$path = self::COLLECTION_PATH . sprintf('/%02d/%d', $collection_id % 100, $collection_id);
    	if ($type != 'short'){
            $path=  self::PATH . $path;
            if ($type == 'absolute'){
                if (!file_exists(Config::getRealDocumentRoot()) or !is_dir(Config::getRealDocumentRoot())){
                    if (!file_exists(Config::getRealDocumentRoot()) or !is_dir(Config::getRealDocumentRoot()))
                        trigger_error('path not found:"'.$path.'"');
                }
                $path=Config::getRealDocumentRoot().$path;
            }
    	}
        return $path;
    }
	public function asArray(){
		return $this->data;
	}
    /*     * ***************************** ArrayAccess **************************** */

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return isset($this->data[$offset]) || in_array($offset, self::$additionalFields);
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
        

/******************************* работа с iCollectionDataProvider *****************************/
    /**
     * @var iCollectionDataProvider[]
     */
    static protected $dataProviders = array();
    /**
     * @var iCollectionDataProvider[]
     */
    static protected $dataProvidersByFields = array();

    /**
     * @static
     * @param iCollectionDataProvider $provider
     */
    static function addDataProvider($provider){
        static::$dataProviders[get_class($provider)] = $provider;
        foreach ($provider->fieldsList() as $field){
            static::$dataProvidersByFields[$field] = $provider;
        }
    }

    /**
     * @static
     * @param iCollectionDataProvider $provider
     */
    static function delDataProvider($provider){
        unset(static::$dataProviders[get_class($provider)]);
    }
}
