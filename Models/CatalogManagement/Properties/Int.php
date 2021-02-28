<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Int
 *
 * @author olga
 */
namespace Models\CatalogManagement\Properties;
use Models\CatalogManagement\Variant as VariantEntity;
use Models\CatalogManagement\Item as ItemEntity;
class Int extends Property{
    const TYPE_NAME = 'int';
    /**
     * Диапазон значений, принимаемый MySQL
     * Если не попадаем в диапазон, считаем значение некорректным
     * @TODO изменить при смене структуры БД
     */
    const MIN_VALUE = -2147483648;
    const MAX_VALUE = 2147483647;

    protected static $values_fields = array('min', 'max', 'step');
    protected static function getDataTypeTable($multiple){
        return $multiple ? VariantEntity::TABLE_PROP_INT : ItemEntity::TABLE_PROP_INT;
    }
    public function explicitType($v){
		if ($v == ""){
			return NULL;
		}
        return (int) $v;
    }
    protected function pack($data){
        if ((is_numeric($data['values']['min'])&& is_numeric($data['values']['max']) && is_numeric($data['values']['step'])) || is_numeric($data['values']['step'])
        ) {
            $data['values'] =
                    ($data['values']['min'] != '' ? 'min:' . str_replace(',', '.', $data['values']['min']) . ',' : '')
                    . ($data['values']['max'] != '' ? 'max:' . str_replace(',', '.', $data['values']['max']) . ',' : '')
                    . 'step:' . str_replace(',', '.', $data['values']['step']);
        } else {
            $data['values'] = '';
        }
        return parent::pack($data);
    }
    protected function unpack($data){
        if (!empty($data['values']) && $data['values'] != '') {
            preg_match('~(min:([\d\.]*),max:([\d\.]*),)?step:([\d\.]*)~', $data['values'], $out);
            $data['values'] = array();
            $data['values']['min'] = !empty($out[2]) ? $out[2] : NULL;
            $data['values']['max'] = !empty($out[3]) ? $out[3] : NULL;
            $data['values']['step'] = !empty($out[4]) ? $out[4] : NULL;
        }
        return parent::unpack($data);
    }
    public function getFinalValue($v, $segment_id = NULL){
        return intval($v);
    }

    public static function prepareValues($data, &$errors){
        $data = parent::prepareValues($data, $errors);
        foreach(static::$values_fields as $field){
            if (!isset($data['values'][$field])){
                $data['values'][$field] = '';
            }
        }
        return $data;
    }
    public function isValueFormatCorrect($val) {
        if (!parent::isValueFormatCorrect($val)){
            return FALSE;
        }
        if (!is_numeric($val) || intval($val) != $val){
            return FALSE;
        }
        // Не пропускаем значения, не проходящие в диапазон значений MySQL
        if ($val > static::MAX_VALUE || $val < static::MIN_VALUE){
            return FALSE;
        }
        return TRUE;
    }
}

?>
