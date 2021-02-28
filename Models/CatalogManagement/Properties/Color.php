<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of String
 *
 * @author olga
 */
namespace Models\CatalogManagement\Properties;
class Color extends Property{
    const TYPE_NAME = 'color';
	const ALLOW_VALUES = FALSE;
	const ALLOW_MASK = FALSE;
    const VALUES_TYPE_ARRAY = FALSE;

    /**
     * Проверка значения на соответствие типу данных
     * @param $val
     * @return boolean
     */
    public function isValueFormatCorrect($val){
        if (!preg_match('~^\#[\dA-Fa-f]{3}([\dA-Fa-f]{3})?$~u', $val)){
            return FALSE;
        }
        return TRUE;
    }
}
