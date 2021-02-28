<?php
/**
 * Управление группами свойств
 *
 *
 * @author olga
 */
namespace Models\CatalogManagement;
use Models\CatalogManagement\CatalogHelpers\Interfaces\iGroupDataProvider;
/**
 * Типы товаров
 */
class Group implements \ArrayAccess{
    /**группы свойств*/
    const TABLE = 'property_groups';
    /**
     * @var Group[]
     */
    private static $registry;
    /**
     * Данные о группе
     * @var array()
     */
    private $data = array();
    /**
     * id сегмента, установленного при создании группы
     * @var int
     */
    private $segment_id = NULL;
    /**
     * Разрешенные параметры для различных сущностей системы
     * @see Catalog::checkAllowed()
     */
    static protected $allowParams = array(
		'id',		// id
        'title',	// наименование
        'type_id',	// родительская рубрика
        'key',		// ключ
        'group',	// группировать ли свойства из этой группы в отдельный массив
        'position',	// сортировка
    );
    /**
     * редактируемые извне параметры
     * @see Catalog::checkAllowed()
     */
    static protected $editableParams = array(
        'title',
		'type_id',
        'key',
        'group',
		'position'
    );
	private static $loadIds = array();
	public static function prepare($type_ids){
		if (empty($type_ids)){
			return;
		}
		if (!is_array($type_ids)){
			$type_ids = array($type_ids);
		}
		self::$loadIds = array_merge(self::$loadIds, $type_ids);
	}
	/**
     * получить массив групп в типе
     * @param array $type_ids
	 * @param int $segment_id
     * @return array('type_id' => Group[])
     */
    public static function get($type_ids = NULL, $segment_id = NULL){
		if (empty($type_ids)){
			$type_ids = array(Type::DEFAULT_TYPE_ID);
		}elseif(!is_array($type_ids)){
			$type_ids = array($type_ids);
		}
        $return_ids = $type_ids;
		$return_data = array();
		foreach ($type_ids as $num => $id){
			if (!empty(self::$registry[$id])){
				$return_data = $return_data + self::$registry[$id];
				unset($type_ids[$num]);
			}
		}
        $type_ids = array_merge(self::$loadIds, !empty($type_ids) ? $type_ids : array());
        $type_ids = array_unique($type_ids);
        if (empty($type_ids)){
			return $return_data;
		}
		$db = \App\Builder::getInstance()->getDB();
        $main_data = $db->query('
			SELECT `g`.*, `t`.`title` AS `type_title`
            FROM `'.self::TABLE.'` AS `g`
            INNER JOIN `'.Type::TABLE_ITEM_TYPES.'` AS `t` ON (`t`.`id` = `g`.`type_id`)
            WHERE `g`.`type_id` IN (?i)
            ORDER BY `t`.`position`, `g`.`position`'
            ,
            $type_ids)
        ->select('id');
		foreach ($main_data as $group_id => $data){
			self::$registry[$data['type_id']][$group_id] = new Group($data, $segment_id);
            if (in_array($data['type_id'], $return_ids)) {
                $return_data[$group_id] = self::$registry[$data['type_id']][$group_id];
            }
		}
        self::$loadIds = array();
		return $return_data;
    }
	/**
	 * 
	 * @param int $type_id
	 * @param int $id
	 * @return Group
	 */
	public static function getById($type_id, $id, $segment_id = NULL){
		if (!empty(self::$registry[$type_id][$id])){
			return self::$registry[$type_id][$id];
		}elseif($type_id != Type::DEFAULT_TYPE_ID){
			$type = Type::getById($type_id);
			$parent_ids = $type['parents'];
			foreach ($parent_ids as $p_id){
				if (!empty(self::$registry[$p_id][$id])){
					return self::$registry[$p_id][$id];
				}
			}
		}
		$type = Type::getById($type_id, $segment_id);
		$search_types = array_merge(array($type_id), $type['parents']);
		$type_groups = self::get($search_types, $segment_id);
		if (!empty($type_groups[$id])){
			return $type_groups[$id];
		}
		return NULL;
	}

    /**
     * Проверка ключа на доступность
     * @param string $key
     * @param array $type_ids
     * @param int $ignore_id
     * @return bool
     * @throws \MysqlSimple\Exceptions\InvalidArgumentException
     */
    private static function isKeyExists($key, $type_ids, $ignore_id = NULL){
        $db = \App\Builder::getInstance()->getDB();
        return $db->query('SELECT 1 FROM ?# WHERE `type_id` IN (?i) AND `key` = ?s{ AND `id` != ?d}', self::TABLE, $type_ids, $key, !empty($ignore_id) ? $ignore_id : $db->skipIt())->getCell();
    }
	/**
     * добавление
     * @param string $title
     * @return int id
     */
    public static function add($title, $key, $type_id, &$errors = NULL){
		$db = \App\Builder::getInstance()->getDB();
        $type = Type::getById($type_id);
        $type_ids = $type['parents'];
        $type_ids[] = $type_id;
        $all_children = $type->getAllChildren(array(Type::STATUS_VISIBLE, Type::STATUS_HIDDEN));
        if (!empty($all_children)){
            foreach($all_children as $child_list){
                $type_ids = array_merge($type_ids, array_keys($child_list));
            }
        }
        if (empty($key)){
            if (is_array($title)){
                $first_title = reset($title);
                $key = \LPS\Components\Translit::Supertag($first_title);
            }else{
                $key = \LPS\Components\Translit::Supertag($title);
            }
            $create_key = $key;
            $i = 0;
            while (self::isKeyExists($create_key, $type_ids)){
                $i++;
                $create_key = $key . '_' . $i;
            }
            $key = $create_key;
        } elseif (self::isKeyExists($key, $type_ids)) {
            $errors['key'] = \Models\Validator::ERR_MSG_EXISTS;
        }
        if (empty($title)){
            $errors['title'] = \Models\Validator::ERR_MSG_EMPTY;
        }
        if (!empty($errors)){
            return FALSE;
        }
		foreach (self::$dataProviders as $p){
            /* @var $p iGroupDataProvider */
            $p->preCreate($title, $errors);
        }
		if (!empty($errors)){
			return false;
		}
        $max_num = $db->query('
            SELECT MAX(`position`)
            FROM `'.self::TABLE.'`
            WHERE `type_id` = ?d',
                $type_id)
        ->getCell();
        if (empty($max_num))
            $max_num = 1;
        $create_result = $db->query('
            INSERT INTO `'.self::TABLE.'`
            SET `type_id`=?d,
                `title`=?,
				`key`=?,
                `position`=?d',
                $type_id,
                !is_array($title) ? $title : '',
				$key,
                $max_num+1);
		foreach (self::$dataProviders as $p){
            /* @var $p iGroupDataProvider */
            $p->onCreate($create_result, array());
        }
        return $create_result;
    }
    /**
     * удаление
     * @param int $group_id
     * @return bool
     */
    public static function delete($group_id){
		$db = \App\Builder::getInstance()->getDB();
        $position = $db->query('
            SELECT `position`
            FROM `'.self::TABLE.'`
            WHERE `id`=?d',
                $group_id)
            ->getCell();
		$result = $db->query('
            DELETE FROM `'.self::TABLE.'`
            WHERE `id`=?d',
                $group_id);
		if ($result){
			$db->query('
				UPDATE `'.self::TABLE.'`
				SET `position`=`position`-1
				WHERE `position` > ?d',
					$position);
			$db->query('
			UPDATE `'.  Properties\Factory::TABLE.'`
			SET `group_id`=0
			WHERE `group_id`=?d',
				$group_id);
			foreach (self::$dataProviders as $p){
				/** @var $p iGroupDataProvider */
				$p->onDelete($group_id);
			}
		}
        return $result;
    }
    public static function clearCache($type_id = NULL) {
        if (!empty($type_id) && !empty(self::$registry[$type_id])){
            unset(self::$registry[$type_id]);
        }else{
            self::$registry = array();
        }
    }
    /**
     * @param array $data
     */
    protected function __construct(array $data, $segment_id = NULL){
        $this->segment_id = $segment_id;
        $this->data = $data;
        foreach (self::$dataProviders as $p){
            /** @var $p iGroupDataProvider */
            $p->onLoad($this);
        }
    }
    /**
     * Возвращает данные группы
     * @param string $key
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function getData($key){
        if($key == 'segment_id'){
            return $this->segment_id;
        }
        if (!array_key_exists($key, $this->data)){
            throw new \InvalidArgumentException($key . ' is not avalible');
        }
        return $this->data[$key];
    }

    /**
     * Изменение состояния объекта, не меняет БД
     * @param string $key
     * @param mixed $value
     */
    private function setData($key, $value){
        $this->data[$key] = $value;
    }

    /**
	 * Обновление данных о группе
     * @param array $params
     * @see $allowParams
     * @return bool
     */
    private function save($params, &$errors = NULL){
		foreach (self::$dataProviders as $p){
            /** @var $p iGroupDataProvider */
            $p->preUpdate($this, $params, $errors);
        }
		if (!Catalog::checkAllowed($params, self::$allowParams, false)){
            throw new \InvalidArgumentException('Incorrect $params');
        }
		$id = $this->getData('id');
        foreach ($params as $key => $value){
            $this->setData($key, $value);
        }
		$db = \App\Builder::getInstance()->getDB();
        $result = $db->query('UPDATE `'.self::TABLE.'` SET ?a WHERE `id`=?d', $params, $id);
        foreach (self::$dataProviders as $p){
            /** @var $p iGroupDataProvider */
            $p->onUpdate($this);
        }
        self::clearCache($this['type_id']);
        return $result;
    }

    /**
     * редактирование
     * @param string $title
     * @return bool
     */
    public function update($params, &$errors = NULL){
        $type = Type::getById($this['type_id']);
        $type_ids = $type['parents'];
        $type_ids[] = $type['id'];
        $all_children = $type->getAllChildren(array(Type::STATUS_VISIBLE, Type::STATUS_HIDDEN));
        if (!empty($all_children)){
            foreach($all_children as $child_list){
                $type_ids = array_merge($type_ids, array_keys($child_list));
            }
        }
		foreach ($params as $field => $value){
			if (!in_array($field, self::$editableParams)){
				throw new \Exception('Неверно заданы параметры для редактирования');
			} elseif ($field == 'title' && empty($value)){
                $errors[$field] = \Models\Validator::ERR_MSG_EMPTY;
            } elseif ($field == 'key'){
                if (empty($value)){
                    $errors[$field] = \Models\Validator::ERR_MSG_EMPTY;
                } elseif (self::isKeyExists($value, $type_ids, $this['id'])) {
                    $errors[$field] = \Models\Validator::ERR_MSG_EXISTS;
                }
            }
		}
        if (!empty($errors)){
            return FALSE;
        }
        $update_result = $this->save($params, $errors);
        return $update_result;
    }
    /**
     * поменять номер позиции
     * @param int $move
     */
    public function move($move){
        if (!empty($move)){
			$db = \App\Builder::getInstance()->getDB();
            $old_position = $this->getData('position');
            if ($old_position > $move){
                $db->query('
                    UPDATE `'.self::TABLE.'`
                    SET `position`=`position`+1
                    WHERE `type_id`=?d
                        AND `position`>=?d
                        AND `position`<?d',
                        $this->getData('type_id'),
                        $move,
                        $old_position);
            }else{
                $db->query('
                    UPDATE `'.self::TABLE.'`
                    SET `position`=`position`-1
                    WHERE `type_id`=?d
                        AND `position`<=?d
                        AND `position`>?d',
                        $this->getData('type_id'),
                        $move,
                        $old_position);
            }
			$this->save(array('position' => $move));
			return TRUE;
        }
		return FALSE;
    }

    public function asArray(){
        $result = array();
        foreach(self::$dataProviders as $p) {
            $result = $result + $p->asArray($this);
        }
        return $result + $this->data;
    }

    /******************************* ArrayAccess *****************************/

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists ($offset){
        if (isset(static::$dataProvidersByFields[$offset])){
            return true;
        }
        if ($offset == 'segment_id'){
            return isset($this->segment_id);
        }
        return isset($this->data[$offset]);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet ($offset){
        if(isset(self::$dataProvidersByFields[$offset])){
            return self::$dataProvidersByFields[$offset]->get($this, $offset);
        }
        return $this->getData($offset);
    }

    /**
     * @param string $offset
     * @param mixed $value
     * @throws \Exception
     */
    public function offsetSet ($offset, $value){
        throw new \Exception(get_class($this).' has only immutable Array Access');
    }

    /**
     * @param string $offset
     * @throws \Exception
     */
    public function offsetUnset ($offset){
        throw new \Exception(get_class($this).' has only immutable Array Access');
    }


    /******************************* работа с iGroupDataProvider *****************************/
    /**
     * @var iGroupDataProvider[]
     */
    static $dataProviders = array();
    /**
     * @var iGroupDataProvider[]
     */
    static $dataProvidersByFields = array();

    /**
     * @static
     * @param iGroupDataProvider $provider
     */
    static function addDataProvider(iGroupDataProvider $provider){
        self::$dataProviders[get_class($provider)] = $provider;
        foreach ($provider->fieldsList() as $field){
            self::$dataProvidersByFields[$field] = $provider;
        }
    }

    /**
     * @static
     * @param iGroupDataProvider $provider
     */
    static function delDataProvider(iGroupDataProvider $provider){
        unset(self::$dataProviders[get_class($provider)]);
    }
}

?>