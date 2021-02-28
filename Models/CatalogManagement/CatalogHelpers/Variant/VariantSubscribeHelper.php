<?php
namespace Models\CatalogManagement\CatalogHelpers\Variant;
/**
 * Description of VariantSubscribeHelper
 *
 * @author mac-proger
 */
use Models\CatalogManagement\Variant;
use App\Configs\CatalogConfig;
use Models\Subscribe;
class VariantSubscribeHelper extends VariantHelper {
    protected static $i = NULL;
    
    public function onUpdate($updateKey, Variant $variant, $segment_id, $updatedProperties){
        // Задача крона на появление товара в наличии
        if (!isset($old_data['properties'][CatalogConfig::KEY_VARIANT_COUNT])) {
            return;
        }
        if ($old_data['properties'][CatalogConfig::KEY_VARIANT_COUNT]['value'] == 0 && $old_data['properties'][CatalogConfig::KEY_VARIANT_COUNT_WAIT]['value'] == 0 && 
                ($variant[CatalogConfig::KEY_VARIANT_COUNT] > 0 || $variant[CatalogConfig::KEY_VARIANT_COUNT_WAIT] > 0)){
            Subscribe::addTask(Subscribe::SUBSCRIBE_VARIANT_AVAILABLE, $variant['id']);
        }
        // Задача крона на увеличение количества товара
        if ($variant[CatalogConfig::KEY_VARIANT_COUNT] > $old_data['properties'][CatalogConfig::KEY_VARIANT_COUNT]['value'] 
                || $variant[CatalogConfig::KEY_VARIANT_COUNT_WAIT] > $old_data['properties'][CatalogConfig::KEY_VARIANT_COUNT_WAIT]['value']){
            
            Subscribe::addTask(Subscribe::SUBSCRIBE_VARIANT_COUNT, $variant['id']);
        }
        // Задача на уменьшение цены
        $price_change = array();
        foreach(array(CatalogConfig::KEY_VARIANT_PRICE, CatalogConfig::KEY_VARIANT_PRICE_OPT, CatalogConfig::KEY_VARIANT_PRICE_BIG_OPT) as $price_key){
            $price_change[$price_key] = ($variant[$price_key] < $old_data['properties'][$price_key]['value']) ? 1 : 0;
        }
        if ($price_change[CatalogConfig::KEY_VARIANT_PRICE] || $price_change[CatalogConfig::KEY_VARIANT_PRICE_OPT] || $price_change[CatalogConfig::KEY_VARIANT_PRICE_BIG_OPT]){
            Subscribe::addTask(Subscribe::SUBSCRIBE_VARIANT_PRICE, $variant['id'], $price_change);
        }
    }
}
