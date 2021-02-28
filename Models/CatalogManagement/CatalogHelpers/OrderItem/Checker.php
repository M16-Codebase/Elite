<?php
namespace Models\CatalogManagement\CatalogHelpers\OrderItem;

use Models\CatalogManagement\Variant;
use App\Configs\OrderConfig;
use App\Configs\Settings;
use Models\CatalogManagement\Positions\OrderItem;
/**
 * делаем запрет на то, чтобы положить этот товар в корзину, если он не удовлетворяет условиям
 * каким именно, описываем в данном хелпере
 *
 * @author olya
 */
class Checker extends OrderItemHelper{
    protected static $i = NULL;
    public function preCreate($item_id, &$errors, $propValues, $segment_id) {
        $item = \Models\CatalogManagement\Positions\Order::getById($item_id);
        $position_properties = $item->getVariantProperties('key');
        //при создании, если количество не передано, оно будет равно 1;
        if (!isset($propValues[OrderConfig::KEY_POSITION_COUNT])){
            $propValues[OrderConfig::KEY_POSITION_COUNT][] = array(
                'val_id' => NULL,
                'value' => 1,
                'position' => NULL
            );
        }
        if (!isset($propValues[OrderConfig::KEY_POSITION_ENTITY])){
            throw new \Exception('Должен передаваться id сущности');
        }
        $ent_val = reset($propValues[OrderConfig::KEY_POSITION_ENTITY]);
        $entity_id = $ent_val['value'];
        //@TODO надо хитро вытаскивать сущность из правильного класса
        $entity = Variant::getById($entity_id);
        $this->check($entity, $position_properties, $propValues, $errors);
        // Наличие проверяем только если проверка включена в конфиге
        if (OrderConfig::getParameter(Settings::KEY_AVAILBALE_LOCK)) {
            //наличие надо проверять только при создании
            $available_setting = OrderConfig::getParameter(Settings::KEY_AVAILBALE_CONSIDER);
            $entity_available = $entity['properties'][\App\Configs\CatalogConfig::KEY_VARIANT_AVAILABLE]['value_key'];
            if (in_array($entity_available, $available_setting)){//если в настройках такое наличие не указано
                $errors[\App\Configs\CatalogConfig::KEY_VARIANT_AVAILABLE] = \Models\Validator::ERR_MSG_INCORRECT;
            }
        }
    }
    public function preUpdate($updateKey, Variant $variant, &$params, &$properties, $segment_id, &$errors) {
        $position_properties = $variant['properties'];
        $this->check($variant, $position_properties, $properties, $errors);
    }
    /**
     * Проверка общих параметров при создании и редактировании
     * @param Variant $entity
     * @param \Models\CatalogManagement\Properties\Property[] $position_properties
     * @param array $posValues
     * @param array $errors
     */
    private function check($entity, $position_properties, $posValues, &$errors){
        //проверяем количество
        if (isset($posValues[OrderConfig::KEY_POSITION_COUNT]) && (OrderConfig::getParameter(Settings::KEY_POSITION_COUNT_CONSIDER) 
            || OrderConfig::getParameter(Settings::KEY_POSITION_RESERVE))){
            $old_pos_count = array_key_exists('value', $position_properties[OrderConfig::KEY_POSITION_COUNT]) ? $position_properties[OrderConfig::KEY_POSITION_COUNT]['value'] : 0;
            $new_val = reset($posValues[OrderConfig::KEY_POSITION_COUNT]);
            $new_pos_v = $new_val['value'];
            $count = $new_pos_v - $old_pos_count;
            if ($count > 0 && $entity[\App\Configs\CatalogConfig::KEY_VARIANT_COUNT] < $count){
                $errors[OrderConfig::KEY_POSITION_COUNT] = \Models\Validator::ERR_MSG_TOO_SMALL;
            }
        }
        //проверяем цену
        if (isset($posValues[OrderConfig::KEY_POSITION_PRICE]) && !OrderConfig::getParameter(Settings::KEY_POSITION_PRICE_CONSIDER)){
            $new_val = reset($posValues[OrderConfig::KEY_POSITION_PRICE]);
            $new_pos_v = $new_val['value'];
            if (empty($new_pos_v)){
                $errors[OrderConfig::KEY_POSITION_PRICE] = \Models\Validator::ERR_MSG_EMPTY;
            }
        }
    }
}
