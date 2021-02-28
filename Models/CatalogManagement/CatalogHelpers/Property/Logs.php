<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 19.09.14
 * Time: 18:31
 */

namespace Models\CatalogManagement\CatalogHelpers\Property;

use Models\CatalogManagement\Properties;
use Models\CatalogManagement\Properties\Property;
use Models\Logger AS MainLogger;
class Logs extends PropertyHelper{
    const LOG_ENTITY_TYPE = 'property';
    protected static $i = NULL;
    private $old_data = array();
    private $delete_data_log = array();
    public function onCreate($id, $create_key){
        $property = \Models\CatalogManagement\Properties\Factory::getById($id);
        $type = $property->getType();
        $catalog = $type->getCatalog();
        $data = array(
            'type' => MainLogger::LOG_TYPE_CREATE,
            'entity_type' => self::LOG_ENTITY_TYPE,
            'entity_id' => $id,
            'additional_data' => array(
                't' => $property['title'],
                'dt' => $property['data_type'],
                't_t' => $type['title'],
                't_is_c' => $type->isCatalog(),
                't_c' => $catalog['title']
            )
        );
        MainLogger::add($data);
    }
    public function preUpdate(Property $property, &$params, &$errors) {
        $this->old_data[$property['id']] = $property->asArray();
    }

    public function onUpdate(Property $property){
        $new_data = $property->asArray();
        if (empty($this->old_data[$property['id']])){
            return;
        }
        $logged_fields = \App\Configs\CatalogConfig::getFields('property');
        foreach ($new_data as $f => $nd){
            if (!array_key_exists($f, $logged_fields)){
                continue;
            }
            if ($nd != $this->old_data[$property['id']][$f]){
                $diff[$f] = $nd;
            }
        }
        if (empty($diff)){
            return;
        }
        $type = $property->getType();
        $catalog = $type->getCatalog();
        //в некоторых случая, values чисто технический параметр, который не обязательно записывать в логи
        if (($property instanceof \Models\CatalogManagement\Properties\CatalogPosition || $property instanceof \Models\CatalogManagement\Properties\Enum) && isset($diff['values'])){
            unset($diff['values']);
        }
        foreach ($diff as $f => $v){
            if ($f == 'data_type' && $property instanceof Properties\CatalogPosition){
                $v = $property['data_type'] . \App\Configs\CatalogConfig::PROP_DATA_TYPE_SEPARATOR . $property['values']['catalog_id'];
            }
            if ($property instanceof Properties\Flag && $f == 'default_value'){
                $k = $v == 0 ? 'no' : 'yes';
                $v = isset($property['values'][$k]) ? $property['values'][$k] : NULL;
            }
            $data = array(
                'type' => MainLogger::LOG_TYPE_EDIT,
                'entity_type' => self::LOG_ENTITY_TYPE,
                'entity_id' => $property['id'],
                'attr_id' => $f,
                'additional_data' => array(
                    't' => $property['title'],
                    'dt' => $property['data_type'],
                    't_t' => $type['title'],
                    't_is_c' => $type->isCatalog(),
                    't_c' => $catalog['title'],
                    'v' => $v
                )
            );
            MainLogger::add($data);
        }
    }
    public function preDelete(Property $property, &$error){
        $type = $property->getType();
        $catalog = $type->getCatalog();
        $this->delete_data_log[$property['id']] = array(
            'type' => MainLogger::LOG_TYPE_DEL,
            'entity_type' => self::LOG_ENTITY_TYPE,
            'entity_id' => $property['id'],
            'additional_data' => array(
                't' => $property['title'],
                'dt' => $property['data_type'],
                't_t' => $type['title'],
                't_is_c' => $type->isCatalog(),
                't_c' => $catalog['title']
            )
        );
        return TRUE;
    }

    public function onDelete($id){
        if (isset($this->delete_data_log[$id])){
            MainLogger::add($this->delete_data_log[$id]);
            unset($this->delete_data_log[$id]);
        }
    }
    public function onEnumAdd(Property $property, $enum_id) {
        $type = $property->getType();
        $catalog = $type->getCatalog();
        $data = array(
            'type' => MainLogger::LOG_TYPE_EDIT,
            'entity_type' => 'property',
            'entity_id' => $property['id'],
            'attr_id' => 'values',
            'comment' => 'add',
            'additional_data' => array(
                't' => $property['title'],
                'dt' => $property['data_type'],
                't_t' => $type['title'],
                't_is_c' => $type->isCatalog(),
                't_c' => $catalog['title'],
                'e_id' => $enum_id,
                'v' => $property['values'][$enum_id]['value']
            )
        );
        MainLogger::add($data);
    }
    public function onEnumEdit(Property $property, $enum_id, $enum_data) {
            if ($property['values'][$enum_id]['value'] == $enum_data['value']){
                return;
            }
            $type = $property->getType();
            $catalog = $type->getCatalog();
            $data = array(
                'type' => MainLogger::LOG_TYPE_EDIT,
                'entity_type' => 'property',
                'entity_id' => $property['id'],
                'attr_id' => 'values',
                'comment' => 'edit',
                'additional_data' =>  array(
                    't' => $property['title'],
                    'dt' => $property['data_type'],
                    't_t' => $type['title'],
                    't_is_c' => $type->isCatalog(),
                    't_c' => $catalog['title'],
                    'e_id' => $enum_id,
                    'v' => $property['values'][$enum_id]['value'],
                    'o_v' => $enum_data['value']
                )
            );
            MainLogger::add($data);
    }
    public function onEnumDelete(Property $property, $enum_data) {
        $type = $property->getType();
        $catalog = $type->getCatalog();
        $data = array(
            'type' => MainLogger::LOG_TYPE_EDIT,
            'entity_type' => 'property',
            'entity_id' => $property['id'],
            'attr_id' => 'values',
            'comment' => 'delete',
            'additional_data' => array(
                't' => $property['title'],
                'dt' => $property['data_type'],
                't_t' => $type['title'],
                't_is_c' => $type->isCatalog(),
                't_c' => $catalog['title'],
                'e_id' => $enum_data['id'],
                'v' => $enum_data['value']
            )
        );
        MainLogger::add($data);
    }
} 