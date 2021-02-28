<?php
namespace Models\ImageManagement\Helpers\Interfaces;

use Models\ImageManagement\Collection;
/**
 * Description of iCollectionDataProvider
 *
 * @author olga
 */
interface iCollectionDataProvider{
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList();
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Collection $collection, $field, $segment_id = NULL);
    /**
     * предупреждение, что данные для указанных Collection попали в кеш данных
     */
    public function onLoad(Collection $collection);
	/**
	 * Событие до создания
	 * @param array $params
	 * @param array $errors
	 */
	public function preCreate($type, &$params, $position, &$errorss);
    /**
     * событие на создание нового Collection
     */
    public function onCreate($collection_id, $type, $params, $positions, $errors);
     /**
     * событие перед изменением
     */
    public function preUpdate(Collection $collection, &$params, &$errors);
     /**
     * событие после изменения Collection
     */
    public function onUpdate(Collection $collection);
    /**
     *
     * @param Collection $collection
     */
    public function onDelete(Collection $collection);
}
