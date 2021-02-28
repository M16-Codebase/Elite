<?php
namespace Models\CatalogManagement\CatalogHelpers\Interfaces;
use Models\CatalogManagement\Type;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of iTypeDataProvider
 *
 * @author olga
 */
interface iTypeDataProvider{
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList();
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Type $type, $field, $segment_id = NULL);
    /**
     * предупреждение, что данные для указанных Types попали в кеш данных
     */
    public function onLoad(Type $type);
	/**
	 * Событие до создания
	 * @param array $params
	 * @param array $errors
	 */
	public function preCreate(&$params, &$errors);
    /**
     * событие на создание нового Type
     */
    public function onCreate(Type $type, $params);
     /**
     * событие после изменения Type
     */
    public function onUpdate(Type $type);
     /**
     * событие перед изменением
     */
    public function preUpdate(Type $type, &$params, &$errors);
    /**
     *
     * @param Type $type
     */
    public function onDelete(Type $type);
}

?>