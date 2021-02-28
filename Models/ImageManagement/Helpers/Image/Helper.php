<?php
namespace Models\ImageManagement\Helpers\Image;

use Models\ImageManagement\Image;
/**
 * Description of Helper
 *
 * @author olya
 */
abstract class Helper implements \Models\ImageManagement\Helpers\Interfaces\iImageDataProvider{
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
        Image::addDataProvider($this);
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
    public function get(Image $image, $field, $segment_id = NULL){}
    /**
     * предупреждение, что данные для указанных Images попали в кеш данных
     */
    public function onLoad(Image $image){}
	/**
	 * Событие до создания
	 * @param array $params
	 * @param array $errors
	 */
    public function preCreate($file, &$params, &$errors){}
    /**
     * событие на создание нового Image
     */
    public function onCreate(Image $image, $params){}
    /**
     * Событие после загрузки картинки
     * @param Image $image
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $FILE
     */
    public function onUpload(Image $image, \Symfony\Component\HttpFoundation\File\UploadedFile $FILE, $from_create){
        
    }
     /**
     * событие перед изменением
     */
    public function preUpdate(Image $image, &$params, &$errors){}
     /**
     * событие после изменения Image
     */
    public function onUpdate(Image $image){}
    /**
     *
     * @param Image $image
     */
    public function onDelete(Image $image){}
}
