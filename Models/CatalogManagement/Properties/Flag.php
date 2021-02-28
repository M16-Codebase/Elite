<?php
/**
 * Description of Flag
 *
 * @author olga
 */
namespace Models\CatalogManagement\Properties;
use Models\CatalogManagement\Variant as VariantEntity;
use Models\CatalogManagement\Item as ItemEntity;
class Flag extends Property{
    const TYPE_NAME = 'flag';
    const ALLOW_SET = FALSE;
	const ALLOW_MASK = FALSE;
	const ALLOW_SORT = FALSE;
    const VALUES_TYPE_ARRAY = TRUE;
    /*     * для флага по умолчанию */
    const VALUE_YES = 'Есть';
    const VALUE_NO = 'Нет';
    protected static $values_fields = array('yes', 'no');
    protected static function getDataTypeTable($multiple){
        return $multiple ? VariantEntity::TABLE_PROP_INT : ItemEntity::TABLE_PROP_INT;
    }
    public function explicitType($v){
		if ($v == "" && !is_numeric($v)){
			return NULL;
		}
        return (int) $v;
    }
    public function getFinalValue($v, $segment_id = NULL){
        if (is_null($v)) {
            return NULL;
        }
        if (!empty($this->data['values'])) {
            $value = $this->data['values'][$v ? 'yes' : 'no'];
            if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE && is_array($value)) {
                $value = !empty($value[$segment_id]) ? $value[$segment_id] : (!empty($v) ? static::VALUE_YES : static::VALUE_NO);
            }
            return $value;
        } else {
            return !empty($v) ? static::VALUE_YES : static::VALUE_NO;
        }
    }

    public function onPropertyLoad(\Models\CatalogManagement\CatalogPosition $catalogPosition, &$propertiesBySegments) {
        if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE && !empty($propertiesBySegments[0][$this['key']])) {
            $segments = \App\Segment::getInstance()->getAll();
            foreach($segments as $s) {
                $data = $propertiesBySegments[0][$this['key']];
                $prop_data = array();
                if (is_array($data['val_id'])) {
                    foreach($data['val_id'] as $k => $val_id) {
                        $prop_data[$val_id] = array(
                            'obj_id' => $catalogPosition,
                            'segment_id' => 0,
                            'value' => $data['value'][$k],
                            'id' => $val_id,
                            'position' => $data['position'][$k]
                        );
                    }
                } else {
                    $prop_data[$data['val_id']] = array(
                        'obj_id' => $catalogPosition,
                        'segment_id' => 0,
                        'value' => $data['value'],
                        'id' => $data['val_id'],
                        'position' => NULL
                    );
                }
                $propertiesBySegments[$s['id']][$this['key']] = $catalogPosition->makePropValue(
                    $this,
                    $prop_data,
                    $s['id']
                );
            }
            // Удалять несегментированное значение не нужно, ломается редактирование
//            unset($propertiesBySegments[0][$this['key']]);
        }
    }

    public function isValueFormatCorrect($val) {
        if (!parent::isValueFormatCorrect($val)){
            return FALSE;
        }
        if ($val != 1 && $val != 0){
            return FALSE;
        }
        return TRUE;
    }
}
