<?php
namespace Models\ImageManagement\Helpers\CollectionImage;

use Models\ImageManagement\CollectionImage;
/**
 * Description of Helper
 *
 * @author olya
 */
abstract class Helper implements \Models\ImageManagement\Helpers\Interfaces\iCollectionImageDataProvider{
    protected static $i = NULL;
    protected static $fieldList = array();
    /**
     * @return static
     */
    public static function factory(){
        if (empty (static::$i)){
            static::$i = new static();
        }
        return static::$i;
    }
    protected function __construct(){
        CollectionImage::addDataProvider($this);
    }
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList(){
        return static::$fieldList;
    }
    /**
     * возвращает значение дополнительного поля
     */
    public function get(CollectionImage $image, $field, $segment_id = NULL){}
    /**
     * предупреждение, что данные для указанных CollectionImages попали в кеш данных
     */
    public function onLoad(CollectionImage $image){}
	/**
	 * Событие до создания
	 * @param array $params
	 * @param array $errors
	 */
    public function preCreate($file, &$params, &$errors){}
    /**
     * событие на создание нового CollectionImage
     */
    public function onCreate(CollectionImage $image, $params){}
    /**
     * Событие после загрузки картинки
     * @param CollectionImage $image
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $FILE
     */
    public function onUpload(CollectionImage $image, \Symfony\Component\HttpFoundation\File\UploadedFile $FILE, $from_create){
        
    }
     /**
     * событие перед изменением
     */
    public function preUpdate(CollectionImage $image, &$params, &$errors){}
     /**
     * событие после изменения CollectionImage
     */
    public function onUpdate(CollectionImage $image){}
    /**
     *
     * @param CollectionImage $image
     */
    public function onDelete(CollectionImage $image){}
}
