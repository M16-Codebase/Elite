<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 19.09.14
 * Time: 17:58
 */

namespace Models\CatalogManagement\CatalogHelpers\Type;
use Models\CatalogManagement\Type;
use Models\Logger as MainLogger;

class Logs extends TypeHelper{
    const LOG_ENTITY_TYPE = 'item_type';
    protected static $i = NULL;
    private $old_params = array();

    public function onCreate(Type $type, $params){
        $catalog = $type->getCatalog();
        $data = array(
            'type' => MainLogger::LOG_TYPE_CREATE,
            'entity_type' => self::LOG_ENTITY_TYPE,
            'entity_id' => $type['id'],
            'additional_data' => array(
                't' => $type['title'],
                't_is_c' => $type->isCatalog() ? 1 : 0,
                't_c' => $catalog['title']//,
//                'data' => array_intersect_key($type->asArray(), self::$logged_fields)
            )
        );
        MainLogger::add($data);
    }
    public function preUpdate(Type $type, &$params, &$errors){
        $this->old_params[$type['id']] = $type->asArray();
    }
    public function onUpdate(Type $type){
        $new_params = $type->asArray();
        $logged_fields = \App\Configs\CatalogConfig::getFields('type');
        foreach ($new_params as $f => $nd){
            if (!array_key_exists($f, $logged_fields)){
                continue;
            }
            if ($nd != $this->old_params[$type['id']][$f]){
                $diff[$f] = $nd;
            }
        }
        if (empty($diff)){
            return;
        }
        $catalog = $type->getCatalog();
        foreach ($diff as $f => $v){
            $data = array(
                'type' => MainLogger::LOG_TYPE_EDIT,
                'entity_type' => self::LOG_ENTITY_TYPE,
                'entity_id' => $type['id'],
                'attr_id' => $f,
                'additional_data' => array(
                    't' => $type['title'],
                    't_is_c' => $type->isCatalog(),
                    't_c' => $catalog['title'],
                    'v' => $v
                )
            );
            MainLogger::add($data);
        }
        return;
    }
    /**
     *
     * @param Type $type
     */
    public function onDelete(Type $type){
        $catalog = $type->getCatalog();
        $data = array(
            'type' => MainLogger::LOG_TYPE_DEL,
            'entity_type' => self::LOG_ENTITY_TYPE,
            'entity_id' => $type['id'],
            'additional_data' => array(
                't' => $type['title'],
                't_is_c' => $type->isCatalog(),
                't_c' => $catalog['title'],
            )
        );
        MainLogger::add($data);
    }

} 