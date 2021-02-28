<?php
namespace Models\CatalogManagement\CatalogHelpers\CatalogPosition;
use Models\CatalogManagement\CatalogPosition;
use Models\Logger;
use App\Configs\CatalogConfig;
/**
 * Логирование изменений всех свойств в БД
 * @author Alexander Shulman
 */
class ValuesLogger extends CatalogPositionHelper{
    const COMMENT_COMPLEX_ARRAY = '#complex array#';
    const COMMENT_OBJECT        = '#object#';
//    private $cache = array();
    protected static $i = NULL;
    /**
     *
     * @var \MysqlSimple\Controller
     */
    private $db = NULL;
//    private $def_segment_id = NULL;
    /**
     * @return static
     */
    public static function factory(){
        if (empty (self::$i)){
            self::$i = new ValuesLogger();
        }
        return self::$i;
    }
    protected function __construct(){
        $builder = \App\Builder::getInstance();
        $this->db = $builder->getDB();
//        $def_segment = \App\Segment::getInstance()->getDefault();
//        $this->def_segment_id = $def_segment['id'];
    }
    /**
     * событие на создание нового CatalogPosition
     */
    public function onCreate($entity_id, $entity_type, $segment_id){
        $entity = $entity_type == 'item' ? \Models\CatalogManagement\Item::getById($entity_id, $segment_id) : \Models\CatalogManagement\Variant::getById($entity_id, $entity_type);
        $data = array(
            'type'        => Logger::LOG_TYPE_CREATE,
            'entity_type' => $entity_type,
            'entity_id'   => $entity_id,
            'additional_data' => $this->getAdditionalData($entity)
        );
        Logger::add($data);
    }

    /**
     * событие на удаление CatalogPosition
     */
    public function onDelete($entity_id, $entity_type, $entity, $remove_from_db){
        $data = array(
            'type'        => Logger::LOG_TYPE_DEL,
            'entity_type' => $entity_type,
            'entity_id'   => $entity_id,
            'additional_data' => $this->getAdditionalData($entity)
        );
        Logger::add($data);
    }
    /**
     * Событие до изменения CatalogPosition
     * @TODO основные свойства
     */
    public function preUpdate($updateKey, CatalogPosition $entity, &$params, &$properties, $segment_id, &$errors){
        
//        $propertyValues = $entity->getSegmentProperties($segment_id==0 ? $this->def_segment_id : $segment_id);
//        $checkSums = array();
//        if (empty($properties)){
//            return;
//        }
//        $entity_properties = $entity->getPropertyList('key');//значений может и не быть, а логировать надо все
//        foreach ($entity_properties as $p_key => $property){
//            if (!empty($propertyValues[$p_key])){
//                $value_data = $propertyValues[$p_key];
//                $checkSums[$p_key]['main'] = $this->getCheckSum($value_data);
//                $checkSums[$p_key]['prop_id']   = $value_data['id'];
//                foreach (array_keys($value_data) as $data_key){
//                    $checkSums[$p_key][$data_key] = $this->getCheckSum($value_data[$data_key]);
//                }
//            }else{
//                $checkSums[$p_key]['main'] = array();
//                $checkSums[$p_key]['prop_id']   = $property['id'];
//            }
//        }
//        $this->cache[$this->getCacheKey($entity)] = array(
//            'propertyValues'   => $propertyValues,
//            'check_sum_by_key' => $checkSums,
//            'properties'       => $entity_properties,
//            'last_update'      => $entity['last_update']
//        );
    }

//    private function getCheckSum($data, $depth = 0){
//        if (empty($data)){
//            return '';
//        }
//        if (is_array($data)){
//            $newData = array();
//            foreach ($data as $k=>$v){
//                $newData[] = '('.$k.'=>'.$this->getCheckSum($v, $depth+1).')';
//            }
//            $data = implode($newData, '; ');
//        }
//        if (is_object($data)){
//            $data = '('.get_class($data).')';
//        }
//        return $depth == 0 ? md5($data) : $data;
//    }

//    private function getCacheKey(CatalogPosition $entity){
//        return get_class($entity).'[id:'.$entity['id'].']';
//    }

    /**
     * @TODO основные свойства
     * событие на изменение CatalogPosition
     * Сохранится информация о entity (type & id), атрибуте (attr_id),
     * изменных значениях атрибута (массив additional_data с изменившимися значениями конкретного аттрибута)
     *
     * @param \Models\CatalogManagement\CatalogPosition $entity
     * @param int $segment_id
     * @param array $updatedProperties свойства, которые реально изменились
     * @throws \LogicException
     */
    public function onUpdate($updateKey, CatalogPosition $entity, $segment_id, $updatedProperties){
//        if (!isset($this->cache[$this->getCacheKey($entity)])){
//            throw new \LogicException('Cache break ' . $this->getCacheKey($entity));
//        }
//        $old_data = $this->cache[$this->getCacheKey($entity)];
////        unset($this->cache[$this->getCacheKey($entity)]);
//        if (empty($old_data['properties'])){
//            return;
//        }
//
//        $check_sum_by_key = $old_data['check_sum_by_key'];
//        //TODO Объяснить безобразие с $segment_id==0
//        $propertyValues = $entity->getSegmentProperties($segment_id==0 ? $this->def_segment_id : $segment_id);
//        foreach ($check_sum_by_key as $p_key => $old_property_data){
//            if (!isset($propertyValues[$p_key]) && !isset($old_data['propertyValues'][$p_key])){//если значения небыло и нет
//                continue;
//            }
//            $p_id = $old_property_data['prop_id'];
//            if (!isset($updatedProperties[$p_id])){
//                continue;
//            }
//            if (isset($propertyValues[$p_key]) && isset($old_data['propertyValues'][$p_key]) && $old_data['propertyValues'][$p_key]['value'] == $propertyValues[$p_key]['value']/* || $entity['properties'][$p_key]['data_type'] == \Models\CatalogManagment\Properties\View::TYPE_NAME*/){
//                continue;//если реальное значение не поменялось или это комбинируемое свойство
//            }
//            $old_check_sum = $old_property_data['main'];
//            $new_check_sum = !empty($propertyValues[$p_key]) ? $this->getCheckSum($propertyValues[$p_key]) : '';
//            // пропускаем не изменившиеся значения
//            if ($old_check_sum == $new_check_sum){
//                continue;
//            }
//            $value = isset($propertyValues[$p_key]) ? $propertyValues[$p_key]['real_value'] : '';
//            if (is_array($value)){
//                if (!is_array(reset($value))){
//                    $value = implode($value, '; ');
//                }else{
//                    $value = self::COMMENT_COMPLEX_ARRAY;
//                }
//            }
//            if (is_object($value)){
//                $value = self::COMMENT_OBJECT;
//            }
//
//            $additional_data = array(
//                'at_n' => $old_data['properties'][$p_key]['title'],
//                'at_dt' => $old_data['properties'][$p_key]['data_type'],
//                'v' => $value
//            );
//            if ($old_data['properties'][$p_key] instanceof \Models\CatalogManagement\Properties\CatalogPosition){
//                $additional_data['at_c_id'] = $old_data['properties'][$p_key]['values'];
//            }
//            $this->addEvent($entity, '', Logger::LOG_TYPE_ATTR, $additional_data, $p_id, $segment_id);
//        }
    }

    /**
     * Просто передайте первые два параметра для того чтобы залогировать что угодно происходщие с указанным $entity
     * Учтите, что изменение параметров $entity происходит автоматически @see::onUpdate
     *
     * @param \Models\CatalogManagement\CatalogPosition $entity
     * @param string $comment
     * @param string $type
     * @param array $additional_data
     * @param string attr_id
     * @param int segment_id
     * @throws \InvalidArgumentException
     */
    public function addEvent(CatalogPosition $entity, $comment, $type = Logger::LOG_TYPE_EDIT, $additional_data = \NULL, $attr_id = NULL, $segment_id = NULL, $hidden = NULL){
        $upd_data = array(
            'type'        => $type,
            'entity_type' => $entity instanceof \Models\CatalogManagement\Item ? 'item' : 'variant',
            'entity_id' => $entity['id'],
            'attr_id' => $attr_id,
            'segment_id' => $segment_id,
            'comment'   => $comment,
            'additional_data' => $this->getAdditionalData($entity, $additional_data),
            'hidden' => $hidden
        );
        Logger::add($upd_data);
    }
    /**
     * общая для всех позиции информация для записи в логи
     * @param \Models\CatalogManagement\Item $entity
     * @param array $additional_data
     * @return array
     */
    private function getAdditionalData($entity, $additional_data = array()){
        $typeEntity = $entity->getType();
        $catalog = $typeEntity->getCatalog();
        $additional_data['t_t'] = $typeEntity['title'];
        $additional_data['t_is_c'] = $typeEntity->isCatalog();
        $additional_data['t_c'] = $catalog['title'];
        $additional_data['c_id'] = $catalog['id'];
        if ($catalog['nested_in'] && $entity instanceof \Models\CatalogManagement\Item){
            $parents = $entity->getParentsByTypes();
            $types = \Models\CatalogManagement\Type::factory(array_keys($parents));
            foreach ($parents as $p){
                $additional_data['p'][$types[$p['type_id']]['title']] = $p[$p::ENTITY_TITLE_KEY];
            }
        }
        if ($entity instanceof \Models\CatalogManagement\Item){
            $entity_title = !empty($entity[CatalogConfig::KEY_ITEM_TITLE]) ? $entity[CatalogConfig::KEY_ITEM_TITLE] : $entity['id'];
        }else{
            $entity_title = !empty($entity[CatalogConfig::KEY_VARIANT_TITLE]) ? $entity[CatalogConfig::KEY_VARIANT_TITLE] : $entity['id'];
            $item = $entity->getItem();
            $additional_data['i_t'] = $item[\App\Configs\CatalogConfig::KEY_ITEM_TITLE];
        }
        $additional_data['t'] = $entity_title;
        return $additional_data;
    }
    /**
     * 
     * @param string $action
     * @param CatalogPosition $entity
     * @param \Models\CatalogManagement\Properties\Property $property
     * @param int $v_id
     * @param array $old_values
     * @param array $additional_data
     * @param int $segment_id
     * @return type
     */
    public function onValueChange($action, CatalogPosition $entity, \Models\CatalogManagement\Properties\Property $property, $v_id, $old_values, $additional_data, $segment_id){
		$properties = $entity->getSegmentProperties($segment_id);
        $new_values = !empty($properties[$property['key']]) ? $properties[$property['key']] : array();
        $add_data = array(
            'at_n' => $property['title'],
            'at_dt' => $property['data_type'],
            'at_is_ent' => $property instanceof \Models\CatalogManagement\Properties\Entity,
            'at_s' => $property['set']
        );
        //если не поменялось значение, значит поменялась только позиция
        if ($this->isValueSame($action, $property, $entity, $v_id, $old_values, $segment_id)){
            //скорее всего так и оставим, так как смена позиции не информативна, либо придется задействовать onUpdate
            return;
//            $add_data = array(
//                'p' => $new_values['position'][$v_id],
//                'o_p' => $old_values['position'][$v_id]
//            );
        }
        $add_data += array(
            'o_v' => $action != 'create' ? ($property['set'] ? $old_values['value'][$v_id] : $old_values['value']) : NULL,
            'o_v_r' => $action != 'create' ? ($property['set'] ? $old_values['real_value'][$v_id] : $old_values['real_value']) : NULL,
            'v' => $action != 'delete' ? ($property['set'] ? $new_values['value'][$v_id] : $new_values['value']) : NULL,
            'v_r' => $action != 'delete' ? ($property['set'] ? $new_values['real_value'][$v_id] : $new_values['real_value']) : NULL
        );
        if ($property instanceof \Models\CatalogManagement\Properties\CatalogPosition){
            $add_data['at_c_id'] = $property['values'];
        }
        $this->addEvent($entity, '', Logger::LOG_TYPE_ATTR, $add_data, $property['id'], $segment_id, !empty($property['fixed']) && is_numeric($property['fixed']) ? $property['fixed'] : (!empty($property['fixed']) ? 0 : NULL));
    }
    /**
     * Изменения свйоств-объектов отдельно
     * @param CatalogPosition $entity
     * @param \Models\CatalogManagement\Properties\Property $property
     * @param int $object_id
     * @param string $object_title
     * @param int $segment_id
     */
    public function onPropertyEntityUpdate(CatalogPosition $entity, \Models\CatalogManagement\Properties\Property $property, $object_id, $object_title, $segment_id){
        $add_data = array(
            'at_n' => $property['title'],
            'at_dt' => $property['data_type'],
            'at_is_ent' => $property instanceof \Models\CatalogManagement\Properties\Entity,
			'at_s' => $property['set'],
            'at_t' => $object_title,
            'v' => $object_id
        );
        if ($property instanceof \Models\CatalogManagement\Properties\CatalogPosition){
            $add_data['at_c_id'] = $property['values'];
        }
        $this->addEvent($entity, '', Logger::LOG_TYPE_ATTR, $add_data, $property['id'], $segment_id, !empty($property['fixed']) && is_numeric($property['fixed']) ? $property['fixed'] : (!empty($property['fixed']) ? 0 : NULL));
    }
}
