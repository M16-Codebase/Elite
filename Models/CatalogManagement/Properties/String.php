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
class String extends Property{
    const TYPE_NAME = 'string';
    const VALUES_TYPE_ARRAY = FALSE;

    protected static function checkData($id, &$data, &$e)
    {
        if (isset($data['validation']) && empty($data['validation'])) {
            $vp = $data['validation'];
            if (empty($vp['mode'])) {
                switch($vp['mode']){
                    case 'off':
                        break;
                    case 'preset':
                        if (empty($vp['preset'])){
                            $e['validation[preset]'] = \Models\Validator::ERR_MSG_EMPTY;
                        }
                        break;

                    case 'sel_opts':
                        if (empty($vp['sel_opts'])){
                            $e['validation[sel_opts]'] = \Models\Validator::ERR_MSG_EMPTY;
                        }
                        break;

                    case 'regex':
                        if (empty($vp['regex'])){
                            $e['validation[regex]'] = \Models\Validator::ERR_MSG_EMPTY;
                        }
                        break;
                    default:
                        $e['validation[mode]'] = \Models\Validator::ERR_MSG_INCORRECT_FORMAT;
                        break;
                }
            }
//            if (empty($e)){
//                $data['validation'] = json_encode($vp);
//            }
        }
        return array();
    }
    protected function pack($data){
        if (!empty($data['validation']) && is_array($data['validation'])) {
            $data['validation'] = json_encode($data['validation']);
        } else {
            $data['validation'] = '';
        }
        return parent::pack($data);
    }
    protected function unpack($data){
        if (!empty($data['validation'])){
            $params = is_array($data['validation']) ? $data['validation'] : json_decode($data['validation'], true);
            if (!empty($params['mode']) && $params['mode'] == 'sel_opts'){
                // Список допустимых символов для проверки регуляркой (режим валидации "допустимые символы", не regex)
                $params['allow_symbols'] = (!empty($params['sel_opts']['digits']) ? '\d' : '')
                    . (!empty($params['sel_opts']['cyrillic']) ? 'А-Яа-яЁё' : '')
                    . (!empty($params['sel_opts']['english']) ? 'A-Za-z' : '')
                    . (!empty($params['sel_opts']['symbols']) ? $params['sel_opts']['symbols'] : '');
            }
            $data['validation'] = $params;
        } else {
            $data['validation'] = NULL;
        }
        return parent::unpack($data);
    }

    /**
     * Проверка значения на соответствие типу данных
     * @param $val
     * @return boolean
     */
    public function isValueFormatCorrect($val){
        if (parent::isValueFormatCorrect($val)){
            $vp = $this['validation'];
            if (empty($vp) || empty($vp['mode'])){
                return TRUE;
            } else {
                switch($vp['mode']) {
                    case 'preset':
                        \Models\Validator::getInstance()->checkValue($val, \App\Configs\CatalogConfig::getPropertyFieldsData('string_prop_validation', $vp['preset'])['validatorMethod'], $error);
                        return empty($error);
                        break;

                    case 'sel_opts':
                        if (preg_match('~[^'.$vp['allow_symbols'].']~u', $val, $matches)){
                            return FALSE;
                        }
                        break;

                    case 'regex':
                        if (!preg_match($vp['regex'], $val)){
                            return FALSE;
                        }
                        break;
                    default:
                        break;
                }
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
