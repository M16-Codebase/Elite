<?php
/**
 * Позиции заказа
 *
 * @author olya
 */
namespace Models\CatalogManagement\Positions;
class OrderItem extends \Models\CatalogManagement\Variant{
    /**
     * Свои хелперы
     * @var array
     */
    static protected $dataProviders = array();
    static protected $dataProvidersByFields = array();
    /**
     * урл позици заказа нифига не такой же, как и у позиций каталога
     */
    public function getUrl($segment_id = NULL){
        return $this['url'];
    }
}
