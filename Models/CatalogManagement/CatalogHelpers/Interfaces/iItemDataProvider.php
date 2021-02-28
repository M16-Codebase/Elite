<?php
namespace Models\CatalogManagement\CatalogHelpers\Interfaces;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Variant;
use Models\CatalogManagement\Properties\Property;
/**
 * Description of iItemDataProvider
 *
 * @author olga
 * @TODO разделить на iItemListener и iItemDataProvider как в вариантах
 */
interface iItemDataProvider{
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList();
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Item $item, $field);
    /**
     * предупреждение, что данные для указанных Items попали в кеш данных
     */
    public function onLoad(Item $item);
    /**
     * событие перед созданием нового Item
     */
    public function preCreate($type_id, $propValues, &$errors, $segment_id);
    /**
     * событие на создание нового Item
     */
    public function onCreate($id, $segment_id);
     /**
     * событие после изменения Item
     * @param \Models\CatalogManagement\Item $item
     * @param array $old_data
     * @param array $segment_id
     * @param $updatedProperties свойства, которые реально поменялись
     */
    public function onUpdate($updateKey, Item $item, $segment_id, $updatedProperties);
     /**
      * событие перед изменением
      * @param Item $item
      * @param array $params
      * @param array $properties
      * @param int|null $segment_id
      * @param array $errors
      */
    public function preUpdate($updateKey, Item $item, &$params, &$properties, $segment_id, &$errors);
    /**
     * При загрузке пропертей в объект
     * @param Item $item
     * @param array $propValues
     */
    public function onPropertyLoad(Item $item, &$propValues);
	/**
     * Событие на изменение значения
     * @param string $action
     * @param Item $item
     * @param Property $property
     * @param int $v_id
     * @param array $old_values
     * @param array $additional_data
     * @param int|null $segment_id
     */
	public function onValueChange($action, Item $item, Property $property, $v_id, $old_values, $additional_data, $segment_id);
    /**
     * Перед добавлением варианта
     * @param Item $item
     * @param array $data
     * @param mixed $errors
     * @param int $segment_id
     */
    public function preVariantAdd(Item $item, $data, &$errors, $segment_id);
    /**
     * После добавления варианта
     * @param Item $item
     * @param int $variant_id
     */
    public function onVariantAdd(Item $item, $variant_id);
    /**
     * Перед удалением варианта
     * @param Item $item
     * @param Variant $variant
     */
    public function preVariantRemove(Item $item, Variant $variant);
    /**
     * После удаления варианта. В данный момент вариант удален, и почищены все реестры и кэши.
     * @param Item $item
     * @param int $variant_id
     */
    public function onVariantRemove(Item $item, $variant_id);

    /**
     * Событие перед удалением объекта каталога
     * @param Item $item
     * @param array $errors
     * @return mixed
     */
    public function preDelete(Item $item, &$errors);
    /**
     * cобытие на удаление item
     * @param int $item_id
     */
    public function onDelete($item_id, $entity, $remove_from_db);
    /**
     * при чистке хвостов
     */
    public function onCleanup();
    /**
     * при чистке реестра собранных обхектов
     * @param int $item_id
     */
    public function onClearCache($item_id = NULL);
}
