<?php
/**
 * Description of iPropertyDataProvider
 *
 * @author olga
 */
namespace Models\CatalogManagement\CatalogHelpers\Interfaces;
use Models\CatalogManagement\Properties\Property;
interface iPropertyDataProvider {
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList();
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Property $property, $field);
    /**
     * создание
     */
    public function preCreate(&$params, &$errors, $create_key);
    /**
     * создание
     */
    public function onCreate($id, $create_key);
    /**
     * реакция на создание объекта (для каждого)
     */
    public function onLoad(Property $property, &$data);
     /**
     * событие после изменения Property
     */
    public function onUpdate(Property $property);
     /**
     * событие перед изменением
     */
    public function preUpdate(Property $property, &$params, &$errors);

    public function preDelete(Property $property, &$error);
    public function onDelete($id);
    public function onEnumAdd(Property $property, $enum_id);
    public function onEnumEdit(Property $property, $enum_id, $enum_data);
    public function onEnumDelete(Property $property, $enum_data);
    public function asArray(Property $property, array &$data);
}
?>