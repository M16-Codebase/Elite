<?php
/**
 * Дополнительные данные о заказе, которые меняются от проекта к проекту
 *
 * @author olga
 */
namespace Models\CatalogManagement\CatalogHelpers\Variant;

use Models\CatalogManagement\Variant;
use App\Configs\OrderConfig;
use App\Configs\CatalogConfig;
/**
 * Данные для заказа
 */
class OrderData extends VariantHelper{
    protected static $i = NULL;
    protected static $fieldsList = array('order_data');
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Variant $v, $field){
        if (!in_array($field, static::$fieldsList)){
            throw new \Exception('Поле ' . $field . ' не существует');
        }
//        $variant_cover = $v['cover_view'];
        $item = $v->getItem();
        $order_catalog = \Models\CatalogManagement\Type::getByKey(CatalogConfig::ORDERS_KEY);
        $prop_entity_type = \Models\CatalogManagement\Properties\Factory::getSingleByKey(OrderConfig::KEY_POSITION_ENTITY_TYPE, $order_catalog['id']);
        $enums_entity_type = $prop_entity_type->getEnumIds(array('variant', 'Вариант'));//@TODO временно оба, надо свести к одному
        $data = array(
            OrderConfig::KEY_POSITION_ENTITY => $v['id'],
            OrderConfig::KEY_POSITION_ENTITY_TYPE => !empty($enums_entity_type['variant']) ? $enums_entity_type['variant'] : $enums_entity_type['Вариант'],
            OrderConfig::KEY_POSITION_TITLE => $item[CatalogConfig::KEY_ITEM_TITLE] . ' ' . $v[CatalogConfig::KEY_VARIANT_TITLE],
            OrderConfig::KEY_POSITION_PRICE => !empty($v[CatalogConfig::KEY_VARIANT_PRICE]) ? $v[CatalogConfig::KEY_VARIANT_PRICE] : 0,
            OrderConfig::KEY_POSITION_URL => $v->getUrl(),
            OrderConfig::KEY_POSITION_IMAGE => !empty($variant_cover) ? $variant_cover['id'] : NULL
        );
        return $data;
    }
}
