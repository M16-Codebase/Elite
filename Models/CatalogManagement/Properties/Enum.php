<?php
/**
 * Тип свойства "Перечисление" (Enum)
 *
 * @author olga
 */
namespace Models\CatalogManagement\Properties;
use Models\CatalogManagement\Variant as VariantEntity;
use Models\CatalogManagement\Item as ItemEntity;
class Enum extends Property{
    const TYPE_NAME = 'enum';
    /*     * таблица enum значений свойств наименований */
    const TABLE_PROP_ENUM = 'enum_properties';
    protected static function getDataTypeTable($multiple){
        return $multiple ? VariantEntity::TABLE_PROP_INT : ItemEntity::TABLE_PROP_INT;
    }
    protected static function checkData($id, &$data, &$e){
        if(isset($data['values']) && empty($data['values'])){//если нет значений
            $e['values'] = 'Для типа свойства "Перечисление" должно быть заполнено хотя бы одно "Значение"';
			return array();
        }
        $return_data = $data['values'];
		unset($data['values']);
        return $return_data;
    }

    /**
     * ПОСЛЕ сохранения общих параметров, сохраняем дополнительные
     * @param array $data дополнительные данные. ДОЛЖНЫ БЫТЬ УЖЕ ПРОВЕРЕНЫ!!! (self::checkData())
     * @throws \LogicException
     * @return boolean
     */
    protected function saveAdditionalData($data){
        if (!empty($data['values'])) {
			//редактируем\добавляем
            $position = 1;
            foreach ($this->data['values'] as $enum_id => $val){
                if (empty($data['values'][$enum_id])){
                    $this->deleteEnumValue($enum_id);
                }
            }
            foreach ($data['values'] as $e_id => $val){
                if ($val == ''){
                    continue;
                }
                if (isset($this['values'][$e_id])){
                    $this->editEnumValue($e_id, $val, $position, !empty($data['keys'][$e_id]) ? $data['keys'][$e_id] : NULL);
                }else{
                    $e = NULL;
                    $this->addEnumValue($val, $position, $this['data_type'], $e, !empty($data['keys'][$e_id]) ? $data['keys'][$e_id] : NULL);
                }
                $position++;
            }
            return TRUE;
        }elseif(isset($data['values'])){//если нет значений
            throw new \LogicException('Для типа свойства "Перечисление" должно быть заполнено хотя бы одно "Значение"');
        }
    }
    protected function save(array $data){
        $result = parent::save($data);
        //енум значения надо обновить, чтобы информация была актуальной
        $enumByProps = Factory::loadEnumsData(array($this->id));
        $this->data['values'] = !empty($enumByProps[$this->id]) ? $enumByProps[$this->id] : array();
        return $result;
    }
    public function explicitType($v){
		if ($v == ""){
			return NULL;
		}
        return (int) $v;
    }
    protected function unpack($data){
        $enumCache = Factory::getEnumValuesCache();
        if (!empty($enumCache[$this->id])) {
            $data['values'] = $enumCache[$this->id];
        } elseif (!empty($this->data['values'])) {
            $data['values'] = $this->data['values'];
		}
        return parent::unpack($data);
    }
    public function getFinalValue($v, $segment_id = NULL){
        if (!empty($this->data['values'][$v])) {
            if (!empty($this->data['values'][$v]['value'])){
                return $this->data['values'][$v]['value'];
            }else{
                return NULL;
            }
        } else {
            return NULL;
        }
    }
    /**
     * Возвращает ids enum по значениям
     * @param string|array $value
     * @return array
     */
    public function getEnumIds($value){
        $result = array();
        if (!is_array($value)){
            $value = array($value);
        }
        foreach ($value as &$val){
            foreach ($this['values'] as $enum_id => $enum_val){
                if ($enum_val['value'] == $val){
                    $result[$val] = $enum_id;
                    break;
                }
            }
        }
        return $result;
    }
    /**
     * Найти id enum по его ключу
     * @param string $key
     */
    public function getEnumIdByKey($key){
        foreach ($this['values'] as $enum_id => $enum_val){
            if ($enum_val['key'] == $key){
                return $enum_id;
            }
        }
    }

    /**
     * Добавить enum значение к св-ву
     * @param string $value
     * @param int $position
	 * @param string $data_type
	 * @param string $e
     * @throws \Exception
     * @return int
     */
    public function addEnumValue($value, $position = 0, $data_type = NULL, &$e = NULL, $key = NULL) {
        //Объект может быть ещё не сохранен, и тип у него может быть другой
        if ($this->data['data_type'] != 'enum' && (empty($data_type) || $data_type != 'enum')) {
            throw new \Exception('Error adding enum value: wrong data type');
        }
        if (!empty($this->data['values'])){
            foreach ($this->data['values'] as $enum_id => $enum_val){
                if ($enum_val['value'] == $value){
                    if (!is_null($e)){
                        //если передаем переменную ошибки, значит хотим эту ошибку видеть
                        $e['values'] = 'Значение "' . $value . '" уже существует';
                    }
                    return $enum_id;
                }
            }
        }
        $db = \App\Builder::getInstance()->getDB();
        $newPosition = empty($position) ? $db->query('SELECT MAX(`position`) FROM ?# WHERE `property_id` = ?', self::TABLE_PROP_ENUM, $this->id)->getCell() + 1 : $position;
        $enum_id = $db->query('INSERT INTO ?# SET `property_id` = ?, `position` = ?d, `value` = ?, `key` = ?s', self::TABLE_PROP_ENUM, $this->id, $newPosition, $value, $key);
        //изменение св-ва текущего объекта
        $this->data['values'][$enum_id] = array('value' => $value, 'position' => $newPosition);
        foreach (static::$dataProviders as $p) {
            $p->onEnumAdd($this, $enum_id);
        }
        return $enum_id;
    }

    /**
     * Редактировать enum значение св-ва
     * @param int $id
     * @param string $value
     * @param int $position
     * @throws \Exception
     * @internal param $enum_id
     * @return bool
     */
    public function editEnumValue($id, $value = null, $position = 0, $key = NULL) {
        if (!isset($this->data['values'][$id])){
            throw new \Exception('Error: nonexistent enum id');
        }
		if ($this->data['values'][$id]['value'] == $value
                && $this->data['values'][$id]['position'] ==  $position
                && $this->data['values'][$id]['key'] == $key){
			return TRUE;
		}
        $old_data = $this->data['values'][$id];
        $save_data = array();
        if (!empty($value) && $value != $old_data['value']){
            $save_data['value'] = $value;
        }
        if (!empty($position) && $position != $old_data['position']){
            $save_data['position'] = $position;
        }
		if (!array_key_exists('key', $old_data)){
			throw new \Exception('Не найден key в старом значении enum ' . json_encode($old_data, JSON_UNESCAPED_UNICODE));
		}
        if ($key != $old_data['key']){
            $save_data['key'] = $key;
        }
        if (empty($save_data)){
            return TRUE;
        }
        $db = \App\Builder::getInstance()->getDB();
        $result = $db->query('
            UPDATE ?# SET ?a WHERE `id` = ?', self::TABLE_PROP_ENUM, $save_data, $id);
        //изменение св-ва текущего объекта
        $this->data['values'][$id] = array(
            'value' => !empty($value) ? $value : $this->data['values'][$id]['value'],
            'position' => !empty($position) ? $position : $this->data['values'][$id]['position']);
        foreach (static::$dataProviders as $p) {
            $p->onEnumEdit($this, $id, $old_data);
        }
        return (bool) $result;
    }

    /**
     * Удалить enum значение св-ва
     * @param int $id
     * @throws \Exception
     * @return bool
     * @internal param $enum_id
     */
    public function deleteEnumValue($id) {
        $db = \App\Builder::getInstance()->getDB();
        $result = $db->query('DELETE FROM ?# WHERE `id` = ?', self::TABLE_PROP_ENUM, $id);
		$db->query('DELETE FROM ?# WHERE `property_id` = ?d AND `value` = ?d', $this['table'], $this['id'], $id);
		if ($this['multiple'] == 1){
			VariantEntity::clearCache();
		}else{
			ItemEntity::clearCache();
		}
        $enum_data = $this->data['values'][$id];
        //удаление св-ва текущего объекта
        unset($this->data['values'][$id]);
        foreach (static::$dataProviders as $p) {
            $p->onEnumDelete($this, $enum_data);
        }
        return (bool) $result;
    }
    
    public function checkEnumId($id) {
		if (is_null($id)){
			return NULL;
		}
        return !empty($this['values'][$id]);
    }
    
    public static function prepareValues($data, &$errors) {
        $data = parent::prepareValues($data, $errors);
        return $data;
    }
    public function isValueFormatCorrect($val) {
        if(!$this->checkEnumId($val) && $val != ''){
            return FALSE;
        }
        return TRUE;
    }
}
