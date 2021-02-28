<?php
namespace Models\CatalogManagement\CatalogHelpers\Item;
use Models\CatalogManagement\Item AS ItemEntity;
use Models\CatalogManagement\Properties\Factory AS PropertyFactory;
use App\Configs\CatalogConfig;
use Models\CatalogManagement\Properties;

/**
 * Свойства вариантов у товара. 
 * Смысл такой: если взять по ключу свойство варианта у товара из данного хелпера, 
 * то отдабтся либо мин макс, либо через запятую от всех вариантов.
 */
class VariantProperties extends ItemHelper{
	const SEPARATOR_RANGE = '—';
	const SEPARATOR_ENUM = ', ';
    protected static $i = NULL;
    protected $dataCache = array();
	protected static $allowDataTypes = array(
        Properties\Enum::TYPE_NAME,
		Properties\String::TYPE_NAME,
		Properties\Text::TYPE_NAME,
		Properties\View::TYPE_NAME,
		Properties\Int::TYPE_NAME,
		Properties\Float::TYPE_NAME
	);
    protected static $fieldsList = array('variant_properties');
    /**
     * возвращает значение дополнительного поля
     */
    public function get(ItemEntity $item, $field){
        if (!in_array($field, $this->fieldsList())){
            throw new \InvalidArgumentException('Неверное название дополнительного поля');
        }
        $i_id = $item['id'];
        if (!isset($this->dataCache[$i_id])){
			$variant_properties = PropertyFactory::search($item['type_id'], PropertyFactory::P_VARIANTS, 'key', 'type_group', 'parents');
			$account = \App\Builder::getInstance()->getAccount();
			$variant_status = array(\Models\CatalogManagement\Variant::S_PUBLIC);
			if ($account->isPermission('catalog-item', 'edit')){
				$variant_status[] = \Models\CatalogManagement\Variant::S_HIDE;
			}
			$variants = $item->getVariants($variant_status);
			foreach ($variant_properties as $p){//для каждого свойства варианта вытаскиваем свойства мин макс для Int или Float, строка через запятую для остальных
                /**
                 * @TODO не продумано для множественных свойств
                 */
                if ($p['set'] == 1){
                    continue;
                }
				$variant_values = array();
				$value_min = NULL;
				$value_max = NULL;
				//только для свойств с определенными типами:
				if (!in_array($p['data_type'], self::$allowDataTypes)){
					continue;
				}
//				if ($p['key'] != CatalogConfig::KEY_VARIANT_PRICE){
//					continue;
//				}
				if ($p['data_type'] == Properties\Int::TYPE_NAME || $p['data_type'] == Properties\Float::TYPE_NAME){//для этих - диапазон
					foreach ($variants as $v){
						if (isset($v['properties'][$p['key']])){
							$var_value = /*$p['key'] == CatalogConfig::KEY_VARIANT_PRICE ? $v[$p['key']] : */$v['properties'][$p['key']]['value'];
							if (!empty($var_value) && (empty($value_min) || $value_min > $var_value)){
								$value_min = $var_value;
							}
							if (!empty($var_value) && (empty($value_max) || $value_max < $var_value)){
								$value_max = $var_value;
							}
						}
					}
					if ($value_max == $value_min){
						$value_max = NULL;
					}
					if (empty($value_min)){
						$value_min = NULL;
					}
//                    if ($p['group_key'] == CatalogConfig::GROUP_KEY_PRICES){
//                        if (isset($value_min)){
//                            $value_min = \FormatNumber::getPrice($value_min, ',');
//                        }
//                        if (isset($value_max)){
//                            $value_max = \FormatNumber::getPrice($value_max, ',');
//                        }
//                    }
					$return_value = isset($value_min)
						? ($value_min . (isset($value_max) ? (self::SEPARATOR_RANGE . $value_max) : '')) 
						: NULL;
				}else{
					foreach ($variants as $v){
						if (isset($v['properties'][$p['key']])){
							$v_val = $v['properties'][$p['key']]['real_value'];
							if (is_array($v_val)){
								$variant_values = array_combine($v_val, $v_val) + $variant_values;
							}else{
								$variant_values[$v_val] = $v_val;
							}
						}
					}
					sort($variant_values);
					$return_value = !empty($variant_values) ? implode(self::SEPARATOR_ENUM, $variant_values) : NULL;
					if (empty($return_value)){
						$return_value = NULL;
					}
				}
				$this->dataCache[$i_id][$p['key']] = isset($return_value) && !empty($p['mask']) ? $p->markerReplace($return_value, $p['mask']) : NULL;
			}
        }
        return $this->dataCache[$i_id];
    }
    
    public function clearCache($i_id = NULL){
        if (empty($i_id)){
            $this->dataCache = array();
        }else{
            if (!is_array($i_id)){
                $i_id = array($i_id);
            }
            foreach ($i_id as $i){
                if (!empty($this->dataCache[$i])){
                    unset($this->dataCache[$i]);
                }
            }
        }
    }
}