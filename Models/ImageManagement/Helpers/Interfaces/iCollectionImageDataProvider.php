<?php
namespace Models\ImageManagement\Helpers\Interfaces;

use Models\ImageManagement\CollectionImage;
/**
 * Description of iImageDataProvider
 *
 * @author olga
 */
interface iCollectionImageDataProvider{
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList();
    /**
     * возвращает значение дополнительного поля
     */
    public function get(CollectionImage $image, $field, $segment_id = NULL);
    /**
     * предупреждение, что данные для указанных Images попали в кеш данных
     */
    public function onLoad(CollectionImage $image);
	/**
	 * Событие до создания
	 * @param array $params
	 * @param array $errors
	 */
	public function preCreate($file, &$params, &$errors);
    /**
     * событие на создание нового CollectionImage
     */
    public function onCreate(CollectionImage $image, $params);
	/**
     * Событие после загрузки картинки
     * @param CollectionImage $image
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $FILE
     */
    public function onUpload(CollectionImage $image, \Symfony\Component\HttpFoundation\File\UploadedFile $FILE, $from_create);
     /**
     * событие перед изменением
     * @param CollectionImage $image
     * @param array $params
     * @param array $errors
     */
    public function preUpdate(CollectionImage $image, &$params, &$errors);
     /**
     * событие после изменения CollectionImage
     * @param CollectionImage $image
     */
    public function onUpdate(CollectionImage $image);
    /**
     *
     * @param CollectionImage $image
     */
    public function onDelete(CollectionImage $image);
}
