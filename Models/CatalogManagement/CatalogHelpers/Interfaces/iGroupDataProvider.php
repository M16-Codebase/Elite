<?php
namespace Models\CatalogManagement\CatalogHelpers\Interfaces;
use Models\CatalogManagement\Group;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of iGroupDataProvider
 *
 * @author olga
 */
interface iGroupDataProvider{
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList();
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Group $group, $field);
    /**
     * предупреждение, что данные для указанных Groups попали в кеш данных
     */
    public function onLoad(Group $group);
	/**
	 * Событие до создания
	 * @param array $params
	 * @param array $errors
	 */
	public function preCreate(&$params, &$errors);
    /**
     * событие на создание нового Group
     */
    public function onCreate($id, $params);
     /**
     * событие после изменения Group
     */
    public function onUpdate(Group $group);
     /**
     * событие перед изменением
     */
    public function preUpdate(Group $group, &$params, &$errors);

    public function onDelete($id);

    public function asArray(Group $group);
}
