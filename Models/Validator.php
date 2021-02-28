<?php
/**
 * Description of Validator
 *
 * @author olga
 */
namespace Models;
class Validator {
    const ERR_MSG_EMPTY = 'empty';
    const ERR_MSG_INCORRECT = 'incorrect';
    const ERR_MSG_INCORRECT_FORMAT = 'incorrect_format';
    const ERR_MSG_WRONG_COUNT_SYMBOLS = 'count_symbols';
    const ERR_MSG_TOO_BIG = 'too_big';
    const ERR_MSG_TOO_SMALL = 'too_small';
    const ERR_MSG_EXISTS = 'already_exists';
    const ERR_MSG_NOT_FOUND = 'not_found';
    const ERR_MSG_UNIQUE = 'unique';
    /** @var Validator */
    private static $instance = NULL;
    private $request = NULL;
    /**
     * @return Validator
     */
    public static function getInstance(){
        if (empty(self::$instance)){
            self::$instance = new Validator(\App\Builder::getInstance()->getRequest());
        }
        return self::$instance;
    }

    private function __construct($request){
        $this->request = $request;
    }

    protected function checkOptions($options, $allowed){
        if (is_array($options) && is_array($allowed)){
            foreach ($options as $key => $option){
                if (array_search($key, $allowed) === FALSE){
                    throw new \LogicException('Неверная опция при проверке значений: ' . $key);
                }
            }
        }
    }
    /**
     * Проверяет массив результатов валидации на отсутствие ошибок и вычищает из него пустые значения
     * @param array $errors 
     * @return bool true при отсутствии ошибок
     */
    public static function isErrorsEmpty(array &$errors){
        foreach($errors as $field=>$value){
            if (empty($value)){
                unset($errors[$field]);
            }
        }
        return empty($errors);
    }
    public function checkValue(&$value, $type, &$error, $options = NULL){
        $value = trim($value);
        if (!method_exists($this, $type)){
            throw new \LogicException('Задан неверный метод проверки значений: ' . $type);
        }
        $error = $this->$type($value, $options);
        if (!empty($error)){
            return NULL;
        }
        return $value;
    }
    /**
     *
     * @param array $params массив вида:
     * <ul>
     *  <li>Field name = >array(
     *      <ul>
     *          <li>'type' => type name</li>
     *          <li>'options' => array(
     *              <ul>
     *                  <li>option name => option value</li>
     *              </ul>
     *              )
     *          </li>
     *      </ul>
     *      )
     * </li>
     * </ul>
     *
     * @param array $errors
     * @return array
     */
    public function checkFewResponseValues($params, &$errors){
        foreach ($params as $field => $param){
            $result[$field] = $this->checkResponseValue($field, $param['type'], $errors[$field], !empty($param['options']) ? $param['options'] : NULL);
        }
        self::isErrorsEmpty($errors); // вычищаем пустые ошибки
        return $result;
    }
    public function checkResponseValue($value_name, $type, &$error, $options = NULL){
        $value = $this->request->request->get($value_name, isset($options['def']) ? $options['def'] : NULL, TRUE);
        unset($options['def']);
        return $this->checkValue($value, $type, $error, $options);
    }
    protected function checkEmail($param, $options){
        $this->checkOptions($options, array('empty', 'uniq', 'no_deleted'));
        $error = $this->checkEmpty($param, NULL);//пустое ли значение
        //если пустое, а не должно быть, возвращаем ошибку
        if (!empty($error) && empty($options['empty'])){
            return $error;
        }elseif (!empty($error)){//если значение пустое, и может таким быть, то ошибки нет.
            return NULL;
        }
        if(!preg_match("/^([a-z0-9_\-.])+@([a-z0-9_-]+\.)+[a-z0-9]{2,4}$/i", $param)) {
    		return self::ERR_MSG_INCORRECT_FORMAT;
    	}
        if (!empty($options['uniq'])){
            $db = \App\Builder::getInstance()->getDB();
            $result = $db->query('SELECT `id` FROM `users` WHERE `email`=?{ AND `status` != ?s}', $param, !empty($options['no_deleted']) ? 'deleted' : $db->skipIt());
            if ($result->getCell()){
                return self::ERR_MSG_EXISTS;
            }
        }
    }
    protected function checkEmpty($param, $options = NULL){
        $this->checkOptions($options, NULL);
        return empty($param) ? self::ERR_MSG_EMPTY : NULL;
    }
    protected function checkInt($param, array $options=NULL){
        $this->checkOptions($options, array('count', 'empty', 'min', 'max'));
        $error = $this->checkEmpty($param, NULL);//пустое ли значение
        //если пустое, а не должно быть, возвращаем ошибку
        if (!empty($error) && empty($options['empty']) && $param !== 0 && $param !== "0"){
            return $error;
        }elseif (!empty($error)){//если значение пустое, и может таким быть, то ошибки нет.
            return NULL;
        }
        if (!empty($param) && preg_match('~[^0-9]~', $param)){
            return self::ERR_MSG_INCORRECT_FORMAT;
        }
        if (!empty($options['min']) && $param > $options['min']){
            return self::ERR_MSG_TOO_SMALL;
        }
        if (!empty($options['max']) && $param > $options['max']){
            return self::ERR_MSG_TOO_BIG;
        }
        if (!empty($options['count']) && !preg_match('~^[0-9]{'.$options['count'].'}$~', $param)){
            return self::ERR_MSG_WRONG_COUNT_SYMBOLS;
        }
    }
    protected function checkString($param, $options = NULL){
        $this->checkOptions($options, array('empty', 'count', 'count_min', 'count_max'));
        $error = $this->checkEmpty($param, NULL);//пустое ли значение
        //если пустое, а не должно быть, возвращаем ошибку
        if (!empty($error) && empty($options['empty'])){
            return $error;
        }elseif (!empty($error)){//если значение пустое, и может таким быть, то ошибки нет.
            return NULL;
        }
        $param_length = mb_strlen($param, \LPS\Config::getParametr('site', 'codepage'));
        if (!empty($options['count']) && $param_length != $options['count']){
            return self::ERR_MSG_WRONG_COUNT_SYMBOLS;
        }
        if (!empty($options['count_min']) && $param_length < $options['count_min']){
            return self::ERR_MSG_WRONG_COUNT_SYMBOLS;
        }
        if (!empty($options['count_max']) && $param_length > $options['count_max']){
            return self::ERR_MSG_WRONG_COUNT_SYMBOLS;
        }
    }
    protected function checkPhone($param, $options = NULL){
        $this->checkOptions($options, array('empty'));
        $error = $this->checkEmpty($param, NULL);//пустое ли значение
        //если пустое, а не должно быть, возвращаем ошибку
        if (!empty($error) && empty($options['empty'])){
            return $error;
        }elseif (!empty($error)){//если значение пустое, и может таким быть, то ошибки нет.
            return NULL;
        }
        if (preg_match('~[^0-9 \-\+\(\)]~', $param)){
            return self::ERR_MSG_INCORRECT_FORMAT;
        }
    }
    protected function checkICQ($param, $options = NULL){
        $this->checkOptions($options, array('empty'));
        $error = $this->checkEmpty($param, NULL);//пустое ли значение
        //если пустое, а не должно быть, возвращаем ошибку
        if (!empty($error) && empty($options['empty'])){
            return $error;
        }elseif (!empty($error)){//если значение пустое, и может таким быть, то ошибки нет.
            return NULL;
        }
        if (preg_match('~[^0-9\s\-]~', $param)){
            return self::ERR_MSG_INCORRECT_FORMAT;
        }
    }
	protected function checkKey($param, $options){
		$this->checkOptions($options, array('empty'));
		$error = $this->checkEmpty($param, NULL);//пустое ли значение
        //если пустое, а не должно быть, возвращаем ошибку
        if (!empty($error) && empty($options['empty'])){
            return $error;
        }elseif (!empty($error)){//если значение пустое, и может таким быть, то ошибки нет.
            return NULL;
        }
		if (preg_match('~[^0-9a-zA-Z\-_]~', $param)){
			return self::ERR_MSG_INCORRECT_FORMAT;
		}
	}
    protected function checkUrl(&$param, $options){
        $this->checkOptions($options, array('empty'));
		$error = $this->checkEmpty($param, NULL);//пустое ли значение
        //если пустое, а не должно быть, возвращаем ошибку
        if (!empty($error) && empty($options['empty'])){
            return $error;
        }elseif (!empty($error)){//если значение пустое, и может таким быть, то ошибки нет.
            return NULL;
        }
        $param = preg_replace('~^(http:\/\/)?(www\.)?~', '', $param);
        return NULL;
    }
    protected function checkDate(&$param, $options){
        $this->checkOptions($options, array('empty'));
		$error = $this->checkEmpty($param, NULL);//пустое ли значение
        //если пустое, а не должно быть, возвращаем ошибку
        if (!empty($error) && empty($options['empty'])){
            return $error;
        }elseif (!empty($error)){//если значение пустое, и может таким быть, то ошибки нет.
            return NULL;
        }
        $param = preg_replace('~^\d{2}\.\d{2}\.\d{4}$~', '', $param);
        return NULL;
    }
    protected function checkTime(&$param, $options){
        $this->checkOptions($options, array('empty'));
		$error = $this->checkEmpty($param, NULL);//пустое ли значение
        //если пустое, а не должно быть, возвращаем ошибку
        if (!empty($error) && empty($options['empty'])){
            return $error;
        }elseif (!empty($error)){//если значение пустое, и может таким быть, то ошибки нет.
            return NULL;
        }
        $param = preg_replace('~^\d{2}:\d{2}(:\d{2})?$~', '', $param);
        return NULL;
    }
    protected function checkNumber(&$param, $options){
        $this->checkOptions($options, array('empty'));
		$error = $this->checkEmpty($param, NULL);//пустое ли значение
        //если пустое, а не должно быть, возвращаем ошибку
        if (!empty($error) && empty($options['empty'])){
            return $error;
        }elseif (!empty($error)){//если значение пустое, и может таким быть, то ошибки нет.
            return NULL;
        }
        if (!preg_match('~^\d+$~', $param)) {
            return self::ERR_MSG_INCORRECT_FORMAT;
        }
    }
    protected function checkFlag(&$param, $options){
        $this->checkOptions($options, NULL);
        if (empty($param)){//у флага два положения, либо 0 либо 1
            $param = 0;
        }else{
            $param = 1;
        }
        return NULL;
    }
    public function getRelativeUrl($url){
        $self = false;
        if (strpos($url, $_SERVER['SERVER_NAME']) !== false || strpos($url, '.') === false){
            $self = true;
        }
		$url = trim(preg_replace('~^(http://)?(www.)?('.$_SERVER['SERVER_NAME'].')?~', '', $url));
        if ($self){
            if (strpos($url, '/') !== 0){
                $url = '/' . $url;
            }
            $last_char_num = strlen($url)-1;
            if (strpos($url, '?') === FALSE && strrpos($url, '/') != $last_char_num && strpos($url, '*') != $last_char_num){
                $url .= '/';
            }
        }
        return $url;
	}
}

?>
