<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 05.08.15
 * Time: 11:52
 */

namespace Models\CatalogManagement\Properties;


class Time extends Property
{
    const TYPE_NAME = 'time';
    const VALUES_TYPE_ARRAY = FALSE;

    /**
     * Проверка значения на соответствие типу данных
     * @param $val
     * @return boolean
     */
    public function isValueFormatCorrect($val){
        if (parent::isValueFormatCorrect($val)){
            if (preg_match('~^(\d{1,2}):(\d{2})(:(\d{2}))?$~i', $val, $matches)) {
                if ($matches[1] < 24
                    && $matches[2] < 60
                    && (empty($matches[3]) || $matches[4] < 60)){
                    return TRUE;
                }
            }
        }
        return FALSE;
    }

    /**
     * Подставляем секунды, если не указаны
     * @param \Models\CatalogManagement\Type $v
     * @return mixed|string
     */
    public function getCompleteValue($v, $segment_id = NULL){
        return empty($v['value']) || strlen($v['value']) >= 7 ? $v['value'] : $v['value'] . ':00';
    }
}