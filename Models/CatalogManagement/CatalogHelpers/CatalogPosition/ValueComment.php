<?php
/**
 * Комментарии к значения свойств позиций каталога
 * конфликтов между товарами и вариантами быть не должно, 
 * т.к. в кэше хранится массив значений разбитый по таблицам, 
 * а id товаров и вариантов тут роли не играют
 * 
 * Хранение данных:
 * self::$cache = array(
 *	'table(таблица значений)' => array(
 *		'val_id(id в таблице)' => array(
 *			'id',//уникальный id значения
 *			'value_table',
 *			'value_id',
 *			'private_comment',
 *			'comments' => array(
 *				'segment_id' => 'комментарий'
 *			)
 *		)
 *	)
 * )
 *
 * @author olga
 */
namespace Models\CatalogManagement\CatalogHelpers\CatalogPosition;
use Models\CatalogManagement\CatalogPosition;
class ValueComment extends CatalogPositionHelper{
    const TABLE_VALUES = 'properties_values';
    const TABLE_COMMENTS = 'properties_values_comments';
    protected static $i = NULL;
    private static $cache = array();
    private $loadItemsQuery = array();
	/**
	 *
	 * @var array информация о том, что комменты поменялись
	 */
	private $commentUpdated = array();
    /**
     * предупреждение, что данные для указанных CatalogPositions попали в кеш данных чтобы можно было подготовить доп. данные
     * @param CatalogPosition $v
     * @param array $propertiesBySegments свойства по сегментам
     */
    public function onLoad(CatalogPosition $v, $propertiesBySegments = NULL){
        if (!empty($propertiesBySegments)){
			$prop_ids = array();
			foreach ($propertiesBySegments as $s_id => $s_props){
				foreach ($s_props as $prop_key => $data){
					if (empty($data['id'])){
						continue;
					}
					$prop_ids[$data['id']] = $data['id'];
				}
			}
			$props = \Models\CatalogManagement\Properties\Factory::get($prop_ids, $v['segment_id']);
            foreach ($propertiesBySegments as $s_id => $s_props){
                foreach ($s_props as $prop_key => $data){
					if (empty($data['id']) || empty($props[$data['id']])){
						continue;
					}
                    $property = $props[$data['id']];
//                    if (empty($data['val_id']) && !empty($data['value'])){//переход со старых версий(заплатка)
//						$db = \App\Builder::getInstance()->getDB();
//						if ($property['set'] != 1){
//							$data['value'] = array($data['value']);
//						}
//						$object_class = get_class($v);
//						foreach ($data['value'] as $val){
//							$data['val_id'][] = $db->query('INSERT INTO ?# SET ?# = ?d, `property_id` = ?d, `value` = ?s, `segment_id` = ?d', $property['table'], $object_class::TABLE_PROP_OBJ_ID_FIELD, $v['id'], $property['id'], $s_id, NULL);
//						}
//                    }
					if (!is_array($data['val_id'])){
						$data_val_id = empty($data['val_id']) ? array($data['val_id']) : array();
					}else{
						$data_val_id = $data['val_id'];
					}
					foreach ($data_val_id as $val_id){
						if (!empty($val_id)){
							$this->loadItemsQuery[$property['table']][$val_id] = $val_id;
						}
					}
                }
            }
			//\Models\CatalogManagement\Properties\Factory::clearCache(); //мне не ясно зачем сбрасывается кеш тут..
        }
    }
	public function onPropertyLoad(CatalogPosition $v, &$propertiesBySegments){
		if (empty($propertiesBySegments)){
			return;
		}
		foreach ($propertiesBySegments as $s_id => &$properties){
			if (empty($properties)){
				return;
			}
			foreach ($properties as $p_key => &$p){
				$this->onValueMake($v, $p);
			}
		}
	}
    private function onValueMake(CatalogPosition $v, &$propValues){
        if (empty($propValues['val_id'])){//если не передан 'val_id' значит данные нужны для проверки и данный хелпер не требуется
            return NULL;
        }
        $property = \Models\CatalogManagement\Properties\Factory::getById($propValues['id'], $v['segment_id']);
		$updated = FALSE;
		if (is_array($propValues['val_id'])){
			foreach ($propValues['val_id'] as $v_id){
				if (!empty($this->commentUpdated[$property['table']][$v_id])){
					$updated = TRUE;
					if (!empty(self::$cache[$property['table']][$v_id])) {
						unset(self::$cache[$property['table']][$v_id]);
					}
				}
			}
		}else{
			if (!empty($this->commentUpdated[$property['table']][$propValues['val_id']])){
				$updated = TRUE;
				if (!empty(self::$cache[$property['table']][$propValues['val_id']])) {
					unset(self::$cache[$property['table']][$propValues['val_id']]);
				}
			}
		}
        if (!empty($propValues['pv_id']) && !empty($propValues['comments']) && $updated){
            return NULL;//если данные уже лежали в кэше и их не редактировали, то не надо их заново подсчитывать
        }
		if (!is_array($propValues['val_id'])){
			$propValues['val_id'] = array($propValues['val_id']);
		}
		foreach ($propValues['val_id'] as $val_id){
			if (empty($val_id)){
				continue;
			}
			//если ValueMake вызывается из конструктора(когда данные берутся не из кэша в БД), то запрос будет делаться для каждого значения!!!
			if (empty($this->loadItemsQuery[$property['table']][$val_id]) && empty(self::$cache[$property['table']][$val_id])){
				$this->loadItemsQuery[$property['table']][$val_id] = $val_id;
			}
		}
		$this->loadData();
		foreach ($propValues['val_id'] as $val_id){
			if (empty($val_id)){
				continue;
			}
			if (!empty(self::$cache[$property['table']][$val_id])){
				$data = self::$cache[$property['table']][$val_id];
				//$data['id'] - id перемножения таблиц на значения (т.е. уникальный id значения свойства)
				$propValues['comments']['private'][$val_id] = !empty($data['private_comment']) ? $data['private_comment'] : '';
				$propValues['comments']['public'][$val_id] = !empty($data['comments']) ? $data['comments'] : '';//комментарии по сегментам
				if (!empty($this->commentUpdated[$property['table']]) && 
					!empty($this->commentUpdated[$property['table']][$val_id])
				){
					unset($this->commentUpdated[$property['table']][$val_id]);//теперь будет лежать в кэше, поэтому не нужна информация о только что сохраненных комметах
				}
			}
		}
		if ($property['set'] != 1){
			$propValues['val_id'] = reset($propValues['val_id']);
		}
        return NULL;
    }
    public function loadData(){
        if (empty ($this->loadItemsQuery)){
            return;
        }
        $sql = array();
        $sql_vars = array();
        foreach ($this->loadItemsQuery as $table => $values){
            if (!empty($values)){
                $sql[] = '`v`.`value_table` = ?s AND `v`.`value_id` IN (?i)';
                $sql_vars[] = $table;
                $sql_vars[] = $values;
            }
        }
        if (empty($sql)){
            return;
        }
		//основная таблица дополнений (приватные комменты, уникальный id значения)
        $sql_string = 'SELECT `v`.`id`, `v`.`private_comment`, `v`.`value_table`, `v`.`value_id` 
            FROM `'.self::TABLE_VALUES.'` AS `v`
            WHERE (' . implode(') OR (', $sql) . ')';
        $db = \App\Builder::getInstance()->getDB();
        array_unshift($sql_vars, $sql_string);
        $sql_result = call_user_func_array(array($db, 'query'), $sql_vars);
        $data = $sql_result->select('value_table', 'value_id');
        $dataById = $sql_result->select('id');
        $public_comments = !empty($dataById) ? $db->query('
			SELECT * FROM `'.self::TABLE_COMMENTS.'` WHERE `pv_id` IN (?i)', 
				array_keys($dataById)
		)->getCol(array('pv_id', 'segment_id'), 'comment') : array();
        foreach ($this->loadItemsQuery as $table => $values){
            foreach ($values as $val_id){
				if (!empty($val_id)){
					if (empty($data[$table][$val_id])){
						$data[$table][$val_id]['id'] = $db->query('INSERT INTO `'.self::TABLE_VALUES.'` SET `value_table` = ?s, `value_id` = ?d', $table, $val_id);
						//чтобы не смотреть в базу, все равно мы знаем все поля
						$data[$table][$val_id]['value_table'] = $table;
						$data[$table][$val_id]['value_id'] = $val_id;
						$data[$table][$val_id]['private_comment'] = NULL;
					}
					self::$cache[$table][$val_id] = $data[$table][$val_id];
					$pv_id = $data[$table][$val_id]['id'];//уникальный id значения из единой таблицы
					self::$cache[$table][$val_id]['comments'] = !empty($public_comments[$pv_id]) ? $public_comments[$pv_id] : array();
				}
            }
        }
        $this->loadItemsQuery = array();
    }

	public function onValueChange($action, CatalogPosition $entity, \Models\CatalogManagement\Properties\Property $property, $v_id, $old_values, $additional_data, $segment_id){
        if ($action == 'delete'){
            return $this->onValueDelete($property, $v_id);
        }
		$db = \App\Builder::getInstance()->getDB();
		if (is_null($v_id)){
			return NULL;
		}
		$real_update = FALSE;//флаг о том, изменилось ли значение комментария
		if (!empty($additional_data['comments'])){
			$pv_id = $db->query('SELECT `id` FROM `'.self::TABLE_VALUES.'` WHERE `value_id` = ?d AND `value_table` = ?s', $v_id, $property['table'])->getCell();
			if (empty($pv_id)){
				$pv_id = $db->query('INSERT INTO `'.self::TABLE_VALUES.'` SET `value_table` = ?s, `value_id` = ?d', $property['table'], $v_id);
			}
			if (!empty($additional_data['comments']['public'])){
				$data = $additional_data['comments']['public'];
				if (is_array($data)){
					foreach ($data as $segment_id => $comment){
						if (!empty($comment)){
							$old_comment = (!empty($entity['properties'][$property['key']]['comments']) && isset($entity['properties'][$property['key']]['comments']['public'][$v_id][$segment_id])) ? $entity['properties'][$property['key']]['comments']['public'][$v_id][$segment_id] : NULL;
							if ($comment != $old_comment){
								$db->query('REPLACE INTO `'.self::TABLE_COMMENTS.'` SET `pv_id` = ?d, `segment_id` = ?d, `comment` = ?s', $pv_id, $segment_id, $comment);
								$real_update = TRUE;
							}
						}else{
							$db->query('DELETE FROM `'.self::TABLE_COMMENTS.'` WHERE `pv_id` = ?d AND `segment_id` = ?d', $pv_id, $segment_id);
							$real_update = TRUE;
						}
					}
				}
			}
			if (isset($additional_data['comments']['private'])){
				$old_comment = (!empty($entity['properties'][$property['key']]['comments']) && !empty($entity['properties'][$property['key']]['comments']['private'][$v_id])) ? $entity['properties'][$property['key']]['comments']['private'][$v_id] : NULL;
				if ($old_comment != $additional_data['comments']['private']){
					$db->query('
						UPDATE `'.self::TABLE_VALUES.'` 
							SET `private_comment` = ?s 
							WHERE `value_id` = ?d 
								AND `value_table` = ?s', 
							$additional_data['comments']['private'], 
							$v_id, 
							$property['table']
					);
					$real_update = TRUE;
				}
			}
		}
		if ($real_update){
			$this->commentUpdated[$property['table']][$v_id] = $v_id;
		}
	}
	
	private function onValueDelete(\Models\CatalogManagement\Properties\Property $property, $v_id){
		$db = \App\Builder::getInstance()->getDB();
		$pv_id = $db->query('SELECT `id` FROM `'.self::TABLE_VALUES.'` WHERE `value_id` = ?d AND `value_table` = ?s', $v_id, $property['table'])->getCell();
		$db->query('DELETE FROM `'.self::TABLE_COMMENTS.'` WHERE `pv_id` = ?d', $pv_id);
		$db->query('DELETE FROM `'.self::TABLE_VALUES.'` WHERE `id` = ?d', $pv_id);
	}
	function onDelete($id, $entity_type, $entity, $remove_from_db){}
    public function onCleanup(){
		$db = \App\Builder::getInstance()->getDB();
        $db->query('
            DELETE `pv`, `pvc`
            FROM `'.self::TABLE_VALUES.'` AS `pv`
                LEFT JOIN `'.\Models\CatalogManagement\Item::TABLE.'` AS `i` ON (`i2c`.`item_id` = `i`.`id`)
            WHERE
                `i`.`id` IS NULL
        ');
	}
}