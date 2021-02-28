<?php
namespace Models\CatalogManagement\Properties;

/**
 * Диапазон значений. Названия всех наследуемых классов должны либо начинаться с названия данного класса, либо переопределять метод preDataTypeChange
 *
 * @author olya
 */
abstract class Diapason extends View{
    const ADDITIONAL_PROPS_TYPE = 'UNDECLARE';
    const ADDITIONAL_PROPS_SUFFIX_MIN = 'min';
    const ADDITIONAL_PROPS_SUFFIX_MAX = 'max';
    const VALUES_TYPE_ARRAY = TRUE;
    const DEFAULT_MASK_MIN_MAX = '{min}—{max}';
    const DEFAULT_MASK_MIN = 'от {min}';
    const DEFAULT_MASK_MAX = 'до {max}';
    private static $deleted_props = array();
    private static function getAdditionalProperty($type, $prop_key, $prop_type_id){
        if (!in_array($type, array(self::ADDITIONAL_PROPS_SUFFIX_MIN, self::ADDITIONAL_PROPS_SUFFIX_MAX))){
            throw new \Exception('Неверный тип дополнительного свойства: ' . $type);
        }
        return Factory::getSingleByKey(self::getAdditionalKey($prop_key, $type), $prop_type_id);
    }
    /**
     * @param int $id
     * @param array $data
     * @param array $e
     * @return boolean
     */
    protected static function checkData($id, &$data, &$e){
        if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE) {
            // При включенных языках свойство «диапазон» должно быть сегментированным
            $data['segment'] = 1;
        }
        if (!empty($data['default_prop'])){
            // Не проверяем для свойства по умолчанию
            return FALSE;
        }
        if (!empty($id)){
            $property = Factory::getById($id);
        }
        if (isset($data['values'])){
            if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE){
                $values = $data['values'];
            }else{
                $values = array($data['values']);
            }
            foreach ($values as $val){
                if (!isset($val['min_max']) || !preg_match('~\{min\}[^\{]*\{max\}~', $val['min_max'])){
                    $e['values']['min_max'] = 'Неверно заполнен шаблон отображения для min и max';
                }
                if (!isset($val['min']) || !preg_match('~\{min\}~', $val['min'])){
                    $e['values']['min'] = 'Неверно заполнен шаблон отображения для min';
                }
                if (!isset($val['max']) || !preg_match('~\{max\}~', $val['max'])){
                    $e['values']['max'] = 'Неверно заполнен шаблон отображения для max';
                }
            }
        }
        if (array_key_exists('key', $data)){
            $key_min = self::getAdditionalKey($data['key'], self::ADDITIONAL_PROPS_SUFFIX_MIN);
            $key_max = self::getAdditionalKey($data['key'], self::ADDITIONAL_PROPS_SUFFIX_MAX);
            if (empty($id) || $property['key'] != $data['key']){
                //надо проверить ключи новых свойств на уникальность
                if (Factory::isPropertyKeyExist($key_min, $data['type_id'], NULL)
                    || Factory::isPropertyKeyExist($key_max, $data['type_id'], NULL)
                ){
                    $e[$data['key']] = \Models\Validator::ERR_MSG_EXISTS;
                }
            }
        }
        $additional_data = array();
        if (!empty($id)){
            if (array_key_exists('key', $data) && $property['key'] != $data['key']){
                $additional_data['old_key'] = $property['key'];
            }
            if (array_key_exists('data_type', $data) && $property['data_type'] != $data['data_type']){
                $additional_data['old_data_type'] = $property['data_type'];
            }
        }
        return $additional_data;
    }
    private static function getAdditionalKey($key, $a_key){
        return $key . '_' . $a_key;
    }

    /**
     * при создании надо создать ещё доп свойства
     * @param int $id
     * @return bool|void
     * @throws \ErrorException
     */
    public static function onCreate($id){
        $property = Factory::getById($id);
        if ($property['default_prop']) {
            // Не создаем для свойства по умолчанию
            return FALSE;
        }
        $params = array(
            'type_id' => $property['type_id'],
            'title' => 'Min для диапазона ' . $property['key'],
            'key' => self::getAdditionalKey($property['key'], self::ADDITIONAL_PROPS_SUFFIX_MIN),
            'data_type' => static::ADDITIONAL_PROPS_TYPE,
            'multiple' => $property['multiple'],
            'fixed' => self::FIXED_HIDE,
            'visible' => NULL
        );
        $id = Property::create($params, $errors);
        $prop = Factory::getById($id);
        if (!empty($prop)) {
            $prop->update($params, $errors);
        }
        if (!empty($errors) && (empty($errors['key']) || $errors['key'] != \Models\Validator::ERR_MSG_EXISTS)) {
            throw new \ErrorException('Не удалось создать свойство min для диапазона, errors: ' . var_export($errors, true));
        }
        $params['key'] = self::getAdditionalKey($property['key'], self::ADDITIONAL_PROPS_SUFFIX_MAX);
        $params['title'] = 'Max для диапазона ' . $property['key'];
        $id = Property::create($params, $errors);
        $prop = Factory::getById($id);
        if (!empty($prop)) {
            $prop->update($params, $errors);
        }
        if (!empty($errors) && (empty($errors['key']) || $errors['key'] != \Models\Validator::ERR_MSG_EXISTS)) {
            throw new \ErrorException('Не удалось создать свойство max для диапазона, errors: ' . var_export($errors, true));
        }
    }

    /**
     * @return Property[]
     * @throws \Exception
     */
	public function getAddProperties(){
		$property_min = self::getAdditionalProperty(self::ADDITIONAL_PROPS_SUFFIX_MIN, $this['key'], $this['type_id']);
		$property_max = self::getAdditionalProperty(self::ADDITIONAL_PROPS_SUFFIX_MAX, $this['key'], $this['type_id']);
		return array('min' => $property_min, 'max' => $property_max);
	}
    /**
     * 
     * @param array $data дополнительные данные. ДОЛЖНЫ БЫТЬ УЖЕ ПРОВЕРЕНЫ
     * @return boolean
     */
    protected function saveAdditionalData($data) {
        if (!empty($data['old_key'])){//если изменили ключ, надо поменять ключи доп свойств
            $this->onKeyChange($data['old_key']);
        }
        if (!empty($data['old_data_type'])){
            $this->onDataTypeChange($data['old_data_type']);
        }
        return TRUE;
    }
    protected static function onDelete($id){
        if (!empty(self::$deleted_props[$id])){
            $property_min = self::getAdditionalProperty(self::ADDITIONAL_PROPS_SUFFIX_MIN, self::$deleted_props[$id]['key'], self::$deleted_props[$id]['type_id']);
            $property_max = self::getAdditionalProperty(self::ADDITIONAL_PROPS_SUFFIX_MAX, self::$deleted_props[$id]['key'], self::$deleted_props[$id]['type_id']);
            Property::delete($property_min['id'], $error_min);
            Property::delete($property_max['id'], $error_max);
            if (!empty($error_min) || !empty($error_max)){
                throw new \Exception('Ошибки при удалении свойства: '
                    . (!empty($error_min) ? json_encode($error_min, JSON_UNESCAPED_UNICODE) : '')
                    . (!empty($error_max) ? json_encode($error_max, JSON_UNESCAPED_UNICODE) : ''));
            }
            unset(self::$deleted_props[$id]);
        }
    }
    protected function preDataTypeChange($new_data_type){
        //если сменили на класс диапазона, то ничего не делаем, иначе удаляем доп свойства
        if (strpos($new_data_type, strtolower(substr(__CLASS__, strrpos(__CLASS__, '\\')+1))) !== 0){
            $this->preDelete($error);
            self::onDelete($this['id']);
        }
    }
    private function onKeyChange($old_key){
        $property_min = self::getAdditionalProperty(self::ADDITIONAL_PROPS_SUFFIX_MIN, $old_key, $this['type_id']);
        $property_min->update(array('key' => self::getAdditionalKey($this['key'], self::ADDITIONAL_PROPS_SUFFIX_MIN)), $e);
        if (!empty($e)){
            throw new \Exception(json_encode($e, JSON_UNESCAPED_UNICODE));
        }
        $property_max = self::getAdditionalProperty(self::ADDITIONAL_PROPS_SUFFIX_MAX, $old_key, $this['type_id']);
        $property_max->update(array('key' => self::getAdditionalKey($this['key'], self::ADDITIONAL_PROPS_SUFFIX_MAX)), $e);
        if (!empty($e)){
            throw new \Exception(json_encode($e, JSON_UNESCAPED_UNICODE));
        }
    }
    private function onDataTypeChange($old_data_type){
        //если был класс диапазона, то достаточно сменить тип данных доп свойств
        if (strpos($old_data_type, strtolower(substr(__CLASS__, strrpos(__CLASS__, '\\')+1))) === 0){
            $property_min = self::getAdditionalProperty(self::ADDITIONAL_PROPS_SUFFIX_MIN, $this['key'], $this['type_id']);
            $property_min->update(array('data_type' => static::ADDITIONAL_PROPS_TYPE), $e);
            if (!empty($e)){
                throw new \Exception(json_encode($e, JSON_UNESCAPED_UNICODE));
            }
            $property_max = self::getAdditionalProperty(self::ADDITIONAL_PROPS_SUFFIX_MAX, $this['key'], $this['type_id']);
            $property_max->update(array('data_type' => static::ADDITIONAL_PROPS_TYPE), $e);
            if (!empty($e)){
                throw new \Exception(json_encode($e, JSON_UNESCAPED_UNICODE));
            }
        }else{
            self::onCreate($this['id']);
        }
    }
    public function composeValue($values, $propList, $segment_id = 0, $item_id = NULL, $used_views = array()) {
        if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE && !empty($segment_id) && isset($this['values'][$segment_id]['min_max'])){
            $min_max_mask = $this['values'][$segment_id]['min_max'];
            $min_mask = $this['values'][$segment_id]['min'];
            $max_mask = $this['values'][$segment_id]['max'];
        }elseif (isset($this['values']['min_max'])){
            $min_max_mask = $this['values']['min_max'];
            $min_mask = $this['values']['min'];
            $max_mask = $this['values']['max'];
        }else{
            $min_max_mask = '';
            $min_mask = '';
            $max_mask = '';
        }
        $min_key = self::getAdditionalKey($this['key'], self::ADDITIONAL_PROPS_SUFFIX_MIN);
        $min_value = isset($values[$min_key]) ? $values[$min_key]['value'] : NULL;
        $max_key = self::getAdditionalKey($this['key'], self::ADDITIONAL_PROPS_SUFFIX_MAX);
        $max_value = isset($values[$max_key]) ? $values[$max_key]['value'] : NULL;
		$pValue = '';
		if (isset($min_value) && isset($max_value)){
            if ($min_value == $max_value){
                $pValue = $min_value;
            }else{
                $pValue = str_replace(array('{min}', '{max}'), array($min_value, $max_value), $min_max_mask);
            }
		}elseif (isset($min_value)){
			$pValue = str_replace('{min}', $min_value, $min_mask);
		}elseif (isset($max_value)){
			$pValue = str_replace('{max}', $max_value, $max_mask);
		}
        return $pValue;
    }
    protected function preDelete(&$error = NULL){
        //ставим маркер о том, что надо удалить доп свойства
        self::$deleted_props[$this['id']] = array(
            'key' => $this['key'],
            'type_id' => $this['type_id']
        );
    }
}
