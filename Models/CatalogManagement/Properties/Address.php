<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 25.08.15
 * Time: 14:02
 */

namespace Models\CatalogManagement\Properties;

/**
 * @TODO удалять координаты надо после удаления адреса, функция onDelete статичная, удалять надо там.
 * в preDelete можно подготовить все нужные данные
 * @TODO вместо preUpdate надо использовать статичный checkData, он для этого и предназначен, он ссылается на новый тип данных. т.к. если есть ошибки, то и тип данных менять не надо.
 * @TODO вместо onUpdate и onDataTypeChange надо использовать saveAdditionalData - этот метод как раз для этого, тут все дополнительные манипуляции можно провести
 */
class Address extends Property
{
    const TYPE_NAME = 'address';
    const VALUES_TYPE_ARRAY = FALSE;
    
    const ALLOW_SET = FALSE;

    const COORDS_PROP_SUFFIX = '_coords';

    private $old_key = null;

    protected function createCoordsProp(){
        if ($this['default_prop']) {
            // Не создаем координаты для свойства по умолчанию
            return FALSE;
        }
        /**
         * @TODO проверка ключа на уникальность. self::checkData
         */
        $params = array(
            'type_id' => $this['type_id'],
            'title' => 'Координаты для свойства «' . $this['title'] . '»',
            'key' => $this['key'] . self::COORDS_PROP_SUFFIX,
            'data_type' => Coords::TYPE_NAME,
            'multiple' => $this['multiple'],
            'fixed' => self::FIXED_HIDE
        );
        if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE) {
            $title = 'Координаты для свойства «' . $this['title'] . '»';
            $segments = \App\Segment::getInstance()->getAll();
            $params['title'] = array();
            foreach($segments as $s) {
                $params['title'][$s['id']] = $title;
            }
        }
        $id = Property::create($params, $errors);
        $prop = Factory::getById($id);
        if (!empty($prop)) {
            $prop->update($params, $errors);
        }
        if (!empty($errors)) {
            throw new \ErrorException('Не удалось создать свойство координат, errors: ' . var_export($errors, true));
        }
    }

    /**
     * @param string|null $old_key
     * @return false|Property
     * @throws \Exception
     */
    private function getCoordsProp($old_key = null){
        $props = Factory::search($this['type_id'], Factory::P_ALL, 'id', 'type_group', 'self', array('key' => (!empty($old_key) ? $old_key : $this['key']) . self::COORDS_PROP_SUFFIX));
        return reset($props);
    }

    protected static function onCreate($id) {
        /** @var Address $property */
        $property = Factory::getById($id);
        $property->createCoordsProp();
    }

    protected function preDataTypeChange($new_data_type){
        $this->preDelete();
    }

    protected function onDataTypeChange(){
        $this->createCoordsProp();
    }

    protected function preUpdate($params, &$errors) {
        if (!empty($params['key']) && $params['key'] != $this['key']) {
            $this->old_key = $this['key'];
        }
    }

    protected function onUpdate() {
        if (!empty($this->old_key)) {
            $prop = $this->getCoordsProp($this->old_key);
            if (!empty($prop)) {
                $upd_data = $prop->asArray();
                unset($upd_data['id']);
                $upd_data['key'] = $this['key'] . self::COORDS_PROP_SUFFIX;
                $prop->update($upd_data, $e);
                if (!empty($e)) {
                    throw new \ErrorException("Не удалось сменить ключ свойства координат, errors: " . var_export($e, true));
                }
            } else {
                $this->createCoordsProp();
            }
            $this->old_key = null;
        }
    }

    /**
     * Удаляем свойство координат
     * @param $err
     * @throws \ErrorException
     */
    protected function preDelete(&$err = null){
        $coords_prop = $this->getCoordsProp();
        if (!empty($coords_prop)) {
            Property::delete($coords_prop['id'], $e);
            if (!empty($e)) {
                throw new \ErrorException("Не удалось удалить свойство координат адреса, id:${coords_prop['id']}");
            }
        }
    }
}