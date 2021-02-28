<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Float
 *
 * @author olga
 */
namespace Models\CatalogManagement\Properties;
use Models\CatalogManagement\Variant as VariantEntity;
use Models\CatalogManagement\Item as ItemEntity;
class Float extends Property{
    const TYPE_NAME = 'float';
    /**
     * Диапазон значений, принимаемый MySQL
     * Если не попадаем в диапазон, считаем значение некорректным
     * @TODO изменить при смене структуры БД
     */
    const MIN_VALUE = -999999999.99999;
    const MAX_VALUE = 999999999.99999;

    protected static $values_fields = array('min', 'max', 'step');
    protected static function getDataTypeTable($multiple){
        return $multiple ? VariantEntity::TABLE_PROP_FLOAT : ItemEntity::TABLE_PROP_FLOAT;
    }
    public function explicitType($v){
		if ($v == ""){
			return NULL;
		}
		$v = str_replace(',', '.', $v);
        return str_replace(',', '.', (float) $v);
    }
    protected function pack($data){
        if ((is_numeric($data['values']['min']) && is_numeric($data['values']['max']) && is_numeric($data['values']['step']))
            || is_numeric($data['values']['step'])
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
            $data['values']['min'] = $out[2];
            $data['values']['max'] = $out[3];
            $data['values']['step'] = $out[4];
        }
        return parent::unpack($data);
    }
    public function getFinalValue($v, $segment_id = NULL){
        return floatval($v);
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
    protected function getValueToSave($val) {
        $val = parent::getValueToSave($val);
        return !is_null($val) ? str_replace(',', '.', $val) : NULL;
    }
    public function isValueFormatCorrect($val) {
        if (!parent::isValueFormatCorrect($val)){
            return FALSE;
        }
        if (!is_numeric($val)){
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
