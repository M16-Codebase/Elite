<?php
namespace Models\CatalogManagement\CatalogHelpers\Interfaces;
use Models\CatalogManagement\CatalogPosition;
interface iCatalogPositionDataProvider{
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList();
    /**
     * возвращает значение дополнительного поля
     */
    public function get(CatalogPosition $c, $field);
    /**
     * предупреждение, что данные для указанных CatalogPositions попали в кеш данных чтобы можно было подготовить доп. данные
     */
    public function onLoad(CatalogPosition $c, $propertiesBySegments = NULL);
    public function onPropertyLoad(CatalogPosition $c, &$propValues);
    /**
     * событие на создание нового CatalogPosition
     */
    public function onCreate($catalog_position_id, $entity_type, $segment_id);
    /**
     * Событие до изменения CatalogPosition
     */
    public function preUpdate($updateKey, CatalogPosition $entity, &$params, &$properties, $segment_id, &$errors);
    /**
     * событие на изменение CatalogPosition
     */
    public function onUpdate($updateKey, CatalogPosition $entity, $segment_id, $updatedProperties);
	/**
	 * Событие на изменение значения
	 * @param type $additional_data
	 */
	public function onValueChange($action, CatalogPosition $entity, \Models\CatalogManagement\Properties\Property $property, $v_id, $old_values, $additional_data, $segment_id);
    /**
     * событие на удаление CatalogPosition
     */
    public function onDelete($catalog_position_id, $entity_type, $entity, $remove_from_db);
    public function onCleanup();
    public function onClearCache($item_id = NULL);
}
