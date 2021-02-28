<?php
namespace Models\CatalogManagement;

/**
 * Интерфейс для определения сущности, которую можно положить в заказ
 *
 * @author olga
 */
interface iOrderData {
    /**
     * Возвращает данные о заказанном товаре
	 * !!!ВАЖНО: Возвращаемый массив обязан содержать элементы, указанные в
     * Positions\OrderItem::getRequiredFields()
	 *
     */
    public function getDataForOrder();
    /**
     * Подготавливает объекты для загрузки
     */
    public static function prepare($ids, $clear = FALSE);
    /**
     * взять объект по id
     */
    public static function getById($ids);
}
