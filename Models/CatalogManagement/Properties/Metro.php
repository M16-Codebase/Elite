<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 31.08.15
 * Time: 16:27
 */

namespace Models\CatalogManagement\Properties;


use App\Configs\CatalogConfig;
use Models\CatalogManagement\Type;

class Metro extends Variant
{
    const TYPE_NAME = 'metro';
        /**
     * можно ли делать свойство сегментированным
     */
    const ALLOW_SEGMENT = FALSE;
    const METRO_STATION_CATALOG_KEY = CatalogConfig::CATALOG_KEY_METRO;
    const ADDITIONAL_PROPS_TYPE = Int::TYPE_NAME;
    const WALK_TIME_SUFFIX = 'walk_time';
    const DRIVE_TIME_SUFFIX = 'drive_time';
    private static $deleted_props = array();

    /**
     * @param string $type
     * @param string $prop_key
     * @param int $prop_type_id
     * @return Property
     * @throws \Exception
     */
    private static function getAdditionalProperty($type, $prop_key, $prop_type_id){
        if (!in_array($type, array(self::WALK_TIME_SUFFIX, self::DRIVE_TIME_SUFFIX))){
            throw new \Exception('Неверный тип дополнительного свойства: ' . $type);
        }
        return Factory::getSingleByKey(self::getAdditionalKey($prop_key, $type), $prop_type_id);
    }

    public static function prepareValues($data, &$errors){
        $catalog = Type::getByKey(self::METRO_STATION_CATALOG_KEY);
        if (empty($catalog)) {
            throw new \ErrorException('Каталог «' . self::METRO_STATION_CATALOG_KEY . '» не найден');
        }
        $data['values']['catalog_id'] = $catalog['id'];
        $data['values']['edit_mode'] = self::SELECT_MODE_LIST;
        $data['set'] = 1;
        $data = parent::prepareValues($data, $errors);
        return $data;
    }
    /**
     * @param int $id
     * @param array $data
     * @param array $e
     * @return boolean
     */
    protected static function checkData($id, &$data, &$e)
    {
        if (!empty($data['default_prop'])) {
            // Не проверяем для свойства по умолчанию
            return FALSE;
        }
        if (!empty($id)){
            $property = Factory::getById($id);
        }
        $key_walk_time = self::getAdditionalKey($data['key'], self::WALK_TIME_SUFFIX);
        $key_drive_time = self::getAdditionalKey($data['key'], self::DRIVE_TIME_SUFFIX);
        if (empty($id) || $property['key'] != $data['key']){
            //надо проверить ключи новых свойств на уникальность
            if (Factory::isPropertyKeyExist($key_walk_time, $data['type_id'], NULL)
                || Factory::isPropertyKeyExist($key_drive_time, $data['type_id'], NULL)
            ){
                $e[$data['key']] = \Models\Validator::ERR_MSG_EXISTS;
            }
        }
        $additional_data = array();
        if (!empty($id)){
            if ($property['key'] != $data['key']){
                $additional_data['old_key'] = $property['key'];
            }
            if ($property['data_type'] != $data['data_type']){
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
     * @return bool
     * @throws \ErrorException
     */
    public static function onCreate($id)
    {
        $property = Factory::getById($id);
        if ($property['default_prop']) {
            // Не создаем для свойства по умолчанию
            return FALSE;
        }
        $params = array(
            'type_id' => $property['type_id'],
            'title' => 'Время до станции пешком ' . $property['key'],
            'key' => self::getAdditionalKey($property['key'], self::WALK_TIME_SUFFIX),
            'data_type' => static::ADDITIONAL_PROPS_TYPE,
            'multiple' => $property['multiple'],
            'set' => 1,
            'fixed' => self::FIXED_HIDE,
            'visible' => NULL
        );
        $id = Property::create($params, $errors);
        $prop = Factory::getById($id);
        if (!empty($prop)) {
            $prop->update($params, $errors);
        }
        if (!empty($errors) && (empty($errors['key']) || $errors['key'] != \Models\Validator::ERR_MSG_EXISTS)) {
            throw new \ErrorException('Не удалось создать свойство Время до станции метро пешком, errors: ' . var_export($errors, true));
        }
        $params['key'] = self::getAdditionalKey($property['key'], self::DRIVE_TIME_SUFFIX);
        $params['title'] = 'Время до станции на машине ' . $property['key'];
        $id = Property::create($params, $errors);
        $prop = Factory::getById($id);
        if (!empty($prop)) {
            $prop->update($params, $errors);
        }
        if (!empty($errors) && (empty($errors['key']) || $errors['key'] != \Models\Validator::ERR_MSG_EXISTS)) {
            throw new \ErrorException('Не удалось создать свойство Время до станции метро на машине, errors: ' . var_export($errors, true));
        }
    }
    public function getAddProperties(){
        $property_walk = self::getAdditionalProperty(self::WALK_TIME_SUFFIX, $this['key'], $this['type_id']);
        $property_drive = self::getAdditionalProperty(self::DRIVE_TIME_SUFFIX, $this['key'], $this['type_id']);
        return array('walk' => $property_walk, 'drive' => $property_drive);
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
            $property_walk = self::getAdditionalProperty(self::WALK_TIME_SUFFIX, self::$deleted_props[$id]['key'], self::$deleted_props[$id]['type_id']);
            $property_drive = self::getAdditionalProperty(self::DRIVE_TIME_SUFFIX, self::$deleted_props[$id]['key'], self::$deleted_props[$id]['type_id']);
            Property::delete($property_walk['id'], $error_walk);
            Property::delete($property_drive['id'], $error_drive);
            if (!empty($error_walk) || !empty($error_drive)){
                throw new \Exception('Ошибки при удалении свойства: '
                    . (!empty($error_walk) ? json_encode($error_walk, JSON_UNESCAPED_UNICODE) : '')
                    . (!empty($error_drive) ? json_encode($error_drive, JSON_UNESCAPED_UNICODE) : ''));
            }
            unset(self::$deleted_props[$id]);
        }
    }
    protected function preDataTypeChange($new_data_type){
        $this->preDelete($error);
        self::onDelete($this['id']);
    }
    private function onKeyChange($old_key){
        $property_walk = self::getAdditionalProperty(self::WALK_TIME_SUFFIX, $old_key, $this['type_id']);
        $upd_props = $property_walk->asArray();
        unset($upd_props['id']);
        $upd_props['key'] = self::getAdditionalKey($this['key'], self::WALK_TIME_SUFFIX);
        $upd_props['title'] = 'Время до станции пешком ' . $this['key'];
        $property_walk->update($upd_props, $e);
        if (!empty($e)){
            throw new \Exception(json_encode($e, JSON_UNESCAPED_UNICODE));
        }
        $property_drive = self::getAdditionalProperty(self::DRIVE_TIME_SUFFIX, $old_key, $this['type_id']);
        $upd_props = $property_drive->asArray();
        unset($upd_props['id']);
        $upd_props['key'] = self::getAdditionalKey($this['key'], self::DRIVE_TIME_SUFFIX);
        $upd_props['title'] = 'Время до станции на машине ' . $this['key'];
        $property_drive->update($upd_props, $e);
        if (!empty($e)){
            throw new \Exception(json_encode($e, JSON_UNESCAPED_UNICODE));
        }
    }
    private function onDataTypeChange($old_data_type){
        self::onCreate($this['id']);
    }
    protected function preDelete(&$error = NULL){
        //ставим маркер о том, что надо удалить доп свойства
        self::$deleted_props[$this['id']] = array(
            'key' => $this['key'],
            'type_id' => $this['type_id']
        );
    }
}