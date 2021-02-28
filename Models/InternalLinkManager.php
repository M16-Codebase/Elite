<?php

/**
 * Специальная сущность позволяющая связать что угодно с чем угодно
 *
 * @author Alexander
 */
namespace Models;
class InternalLinkManager {
    const TABLE = "internal_links";
    
    const TARGET_TYPE_VARIANT = 'catalog_variant';
    const TARGET_TYPE_ITEM = 'catalog_item';
    const TARGET_TYPE_TYPE = 'catalog_type';
    const TARGET_TYPE_FILE = 'file';
    const TARGET_TYPE_PROPERTY = 'property';
    
    const OBJECT_TYPE_VARIANT = 'catalog_variant';
    const OBJECT_TYPE_ITEM = 'catalog_item';
    const OBJECT_TYPE_TYPE = 'catalog_type';
    const OBJECT_TYPE_FILE = 'file';
    const OBJECT_TYPE_ARTICLE = 'article';
	/**
	 * @var \MysqlSimple\Controller
	 */
	protected $db = null;

    /**
     * @see self::search()
     * @var int
     */
    protected $defaultLimit = 10;
    /**
     * @param int $defaultLimit
     */
    public function setDefaultLimit($defaultLimit) {
        $this->defaultLimit = $defaultLimit;
    }
    /**
     * @var InternalLinkManager
     */
    static protected $instance = null;
    /**
     * @return InternalLinkManager
     */
    static function getInstance() {
        if (empty(self::$instance))
            self::$instance = new InternalLinkManager();
        return self::$instance;
    }

    public function __construct($defaultLimit = null){
        $this->defaultLimit = $defaultLimit;
		$this->db = \App\Builder::getInstance()->getDB();
	}

    /**
     * Добавление новой связи
     * @param string $targert_type
     * @param int $target_id
     * @param string $obj_type
     * @param int $obj_id
     * @param string $comment любое сообщение
     * @return bool
     */
    public function add($target_type, $target_id, $obj_type, $obj_id, $comment = ''){
        return $this->db->query('REPLACE INTO `'.self::TABLE.'` SET `target_type` = ?, `target_id` = ?d, `obj_type` = ?, `obj_id` = ?d, `comment` = ?', $target_type, $target_id, $obj_type, $obj_id, $comment);
    }
    /**
     * Удаление группы связей на пересечении параметров
     * @param string $targert_type
     * @param int $target_id
     * @param string $obj_type
     * @param int $obj_id
     * @param string $comment любое сообщение
     * @return bool
     */
    public function delete($target_type = null, $target_id = null, $obj_type = null, $obj_id = null, $comment = null){
        return $this->db->query('DELETE FROM `'.self::TABLE.'` WHERE 1 
            { AND `target_type`  = ?  }
            {AND `target_id` = ?d }
            {AND `obj_type`  IN (?l)  }
            {AND `obj_id`    = ?d }
            {AND `comment` LIKE ? }',
            empty($target_type) ? $this->db->skipIt() : $target_type,
            empty($target_id)    ? $this->db->skipIt() : $target_id,
            empty($obj_type)     ? $this->db->skipIt() : (is_array($obj_type) ? $obj_type : array($obj_type)),
            empty($obj_id)       ? $this->db->skipIt() : $obj_id,
            empty($comment)      ? $this->db->skipIt() : '%'.$comment.'%'
        );
    }

    /**
     * Возвращает все связанные сущности, сгруппированные по типу для переданных целей
     * @param array $params набор целей в формате массива с ключами - типами целей, а значениями - массив или значение возможных значений целей
     * @param array $obj_types искомые типы связанных сущностей
     * @param int $limit ограничение на количество искомых объектов, NULL - использовать стандартный лимит
     * @param bool $save_order если TRUE, то порядок объектов будет в соответствии с запрашиваемымм порядком целей
     * @return \MysqlSimple\Result
     */
    public function search($params, $obj_types = null, $limit = null, $save_order = true, $obj = array(), $target_type = NULL){
        if (is_null($limit) && !empty ($this->defaultLimit) ){
            $limit = $this->defaultLimit;
        }
        if (empty ($params) && empty($obj))
            return false;
        $where = array();
        $vars  = array('sql'=>'SELECT *');
		if (!empty($params) || !empty($obj_types)){
			$vars['sql'] .= ', `obj_type`, `obj_id`';
		}elseif(!empty($obj) || !empty($target_type)){
			$vars['sql'] .= ', `target_type`, `target_id`';
		}
		$vars['sql'] .= ' FROM `'.self::TABLE.'` WHERE ';
        $order = array();
		if (!empty($params)){
			foreach ($params as $t_type=>$t_ids){
                if (!empty($t_ids)){
                    $where[] = ' (`target_type` = ?s AND `target_id` '.(is_array($t_ids) ? 'IN (?i)' : '= ?d').') ';
                    $vars[]  = $t_type;
                    $vars[]  = $t_ids;
                    $order[] = $t_type;
                }
			}
		}elseif (!empty($obj)){
			foreach ($obj as $o_type => $o_ids){
                if (!empty($o_ids)){
                    $where[] = ' (`obj_type` = ?s AND `obj_id` '.(is_array($o_ids) ? 'IN (?i)' : '= ?d').') ';
                    $vars[]  = $o_type;
                    $vars[]  = $o_ids;
                }
			}
		}
		if (!empty($where)){
			$vars['sql'] .= "\n(\t".implode(" OR \n\t", $where)."\n)";
		}else{
            $vars['sql'] .= '1';
        }
        if (!empty ($obj_types)){
            $vars['sql'] .= ' AND (`obj_type`'. (is_array($obj_types) ? ' IN (?l)' : '=?'). ")\n" ;
            $vars[]  = $obj_types;
        }elseif(!empty($target_type)){
			$vars['sql'] .= ' AND (`target_type`'. (is_array($target_type) ? ' IN (?l)' : '=?'). ")\n" ;
            $vars[]  = $target_type;
		}
		if (!empty($params) || !empty($obj_types)){
			$vars['sql'] .= ' GROUP BY `obj_type`, `obj_id` '."\n"; // необходима группировка, т.к. искомые объекты могут быть привязанны к нескольким искомым целям
		}elseif(!empty($obj) || !empty($target_type)){
			$vars['sql'] .= ' GROUP BY `target_type`, `target_id` '."\n";
		}
        if ($save_order && count($order) >= 2){ //сортировка имеет смысл только если количество типов цели 2 и более
            $vars_tmp = array(); //знаки вопроса в строке ростут справа налево (тоесть во обратном порядке), поэтому и переменные нужно класть в массив значений в обратном порядке
            array_shift($order); //первый элемент сопоставляется с 0 без сравнения, поэтому он не нужен
            $num = 1;
            $order_token = 'IF (`target_type` = ?s, '.$num.', 0)'; //расстановка приоритетов происходит путем сравнения со вторым элементом
            $vars_tmp[]  = array_shift($order);
            while ($t_type = array_shift($order)) { // сравнение происходит столько раз, сколько нужно для выяснения порядка
                $num++;
                $order_token = 'IF (`target_type` = ?s, '.$num.', '.$order_token.')';
                $vars_tmp[]  = $t_type;
            }
            $vars['sql'] .= ' ORDER BY '.$order_token."\n";
            $vars_tmp = array_reverse($vars_tmp);
            foreach ($vars_tmp as $v){
                $vars[] = $v;
            }
        }
        if (!empty($limit)){
            $vars['sql'] .=  ' LIMIT ?d';
            $vars[] = $limit;
        }
        return call_user_func_array(array($this->db, 'query'), $vars);
    }
	/**
	 * 
	 * @param array $obj_types какие привязанные сущности мы хотим забрать array('types')
	 * @param array $target_ids array('target_type' => array(ids)) к каким целям привязаны искомые сущности
	 * @param int $limit
	 * @param bool $save_order
	 * @return array(
	 *		'obj_type' => array(
	 *			'obj_id' => array(data)
	 *		)
	 *	)
	 */
	public function getObjectsByTarget($obj_types, $target_ids, $limit = NULL, $save_order = TRUE){
		$result = $this->search($target_ids, $obj_types, $limit, $save_order);
		return $result->select('obj_type', 'obj_id');
	}
	/**
	 * 
	 * @param array $target_types какие типы целей ищем array('types')
	 * @param array $object_ids array('object_type' => array(ids)) у каких привязанных сущностей хотим забрать
	 * @param int $limit
	 * @param bool $save_order
	 * @return array(
	 *		'target_type' => array(
	 *			'target_id' => array(data)
	 *		)
	 *	)
	 */
	public function getTargetsByObject($target_types, $object_ids, $limit = NULL, $save_order = TRUE){
		$result = $this->search(NULL, NULL, $limit, $save_order, $object_ids, $target_types);
		return $result->select('target_type', 'target_id');
	}
}

?>