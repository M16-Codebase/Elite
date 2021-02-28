<?php
namespace Models\CatalogManagement\CatalogHelpers\Interfaces;
use Models\CatalogManagement\Variant;
interface iVariantDataProvider{
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList();
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Variant $v, $field);
    /**
     * предупреждение, что данные для указанных Variants попали в кеш данных чтобы можно было подготовить доп. данные
     */
    public function onLoad(Variant $v, $propertiesBySegments = NULL);
    public function onPropertyLoad(Variant $v, &$propValues);
    public function preCreate($item_id, &$errors, $propValues, $segment_id);
    /**
     * событие на создание нового Variant
     */
    public function onCreate($variant_id, $segment_id);
    /**
     * Событие до изменения Variant
     */
    public function preUpdate($updateKey, Variant $variant, &$params, &$properties, $segment_id, &$errors);
    /**
     * событие на изменение Variant
     */
    public function onUpdate($updateKey, Variant $variant, $segment_id, $updatedProperties);
	/**
	 * Событие на изменение значения
	 * @param type $additional_data
	 */
	public function onValueChange($action, Variant $variant, \Models\CatalogManagement\Properties\Property $property, $v_id, $old_values, $additional_data, $segment_id);

    /**
     * Событие перед удалением объекта каталога
     * @param Item $item
     * @param array $errors
     * @return mixed
     */
    public function preDelete(Variant $variant, &$errors);
    /**
     * событие на удаление Variant
     */
    public function onDelete($variant_id, $entity, $remove_from_db);
    public function OnCleanup();
    public function onClearCache($item_id = NULL);
}