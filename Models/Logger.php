<?php
/**
 * Логируем всё что угодно!!!
 *
 * @author olya
 */
/*
--
-- Table structure for table `values_log`
--

CREATE TABLE IF NOT EXISTS `values_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('create','edit','attr','images','delete') NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `entity_type` enum('item','variant') CHARACTER SET utf8 NOT NULL,
  `entity_id` smallint(5) unsigned NOT NULL,
  `attr_id` smallint(5) unsigned DEFAULT NULL,
  `segment_id` tinyint(4) unsigned DEFAULT NULL,
  `user_id` smallint(6) unsigned DEFAULT NULL,
  `comment` text CHARACTER SET utf8,
  `additional_data` text CHARACTER SET utf8,
  PRIMARY KEY (`id`),
  KEY `user` (`user_id`),
  KEY `EntityAttr` (`entity_type`,`entity_id`,`attr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
*/ 
namespace Models;
class Logger {
    const TABLE = 'values_log';
    const STACK_SIZE = 5000;
    const LOG_TYPE_CREATE       = 'create';
    const LOG_TYPE_EDIT         = 'edit';
    const LOG_TYPE_ATTR         = 'attr';
	const LOG_TYPE_ATTR_GROUP   = 'attr_group';
    const LOG_TYPE_IMG          = 'images';
    const LOG_TYPE_POST         = 'post';
    const LOG_TYPE_ASSOC        = 'assoc';
    const LOG_TYPE_DEL          = 'delete';
    const LOG_TYPE_TRANSFER_ITEM    = 'transfer_item';
    const LOG_TYPE_TRANSFER_VARIANT = 'transfer_variant';
    private static $log_types = array(
		self::LOG_TYPE_CREATE,
        self::LOG_TYPE_EDIT,
        self::LOG_TYPE_ATTR,
		self::LOG_TYPE_ATTR_GROUP, 
        self::LOG_TYPE_IMG,
        self::LOG_TYPE_POST,
        self::LOG_TYPE_ASSOC,
        self::LOG_TYPE_DEL,
        self::LOG_TYPE_TRANSFER_ITEM,
        self::LOG_TYPE_TRANSFER_VARIANT
    );
    private static $allow_fields = array(
        'type',
        'catalog_id',
        'entity_type',
        'entity_id',
        'attr_id',
        'segment_id',
        'comment',
        'additional_data',
        'user_id',
        'cli',
        'hidden'
    );
    private static $allow_search_fields = array(
        'id', 'type', 'from', 'to', 'catalog_id', 'entity_type', 'entity_id', 'segment_id', 'user_id', 'attr_id', 'cli', 'hidden', 'not_hidden'
    );
    private $user_id = NULL;
    /**
     * @var Logger
     */
    private static $i = NULL;

    private $logger_cache = array();

    private function __construct(){
    }

    public function __destruct(){
        $this->save();
    }
    public function save(){
        if (!empty($this->logger_cache)){
            $db = \App\Builder::getInstance()->getDB();
            $insert_data = array();
            foreach($this->logger_cache as $line){
                $insert_data[] = ''
                    . (!empty($line['type']) ? $db->escape_value($line['type']) : 'NULL') . ', '
                    . (!empty($line['time']) ? $db->escape_value($line['time']) : 'NULL') . ', '
                    . (!empty($line['catalog_id']) ? $db->escape_value($line['catalog_id']) : 'NULL') . ', '
                    . $db->escape_value($line['entity_type']) . ', '
                    . $db->escape_int($line['entity_id']) . ', '
                    . (!empty($line['attr_id']) ? $db->escape_value($line['attr_id']) : 'NULL') . ', '
                    . (!empty($line['segment_id']) ? $db->escape_int($line['segment_id']) : 'NULL') . ', '
                    . (!empty($line['user_id']) ? $db->escape_int($line['user_id']) : 'NULL') . ', '
                    . (!empty($line['comment']) ? $db->escape_value($line['comment']) : 'NULL') . ', '
                    . (!empty($line['additional_data']) ? $db->escape_value($line['additional_data']) : 'NULL') . ', '
                    . (!empty($line['cli']) ? $db->escape_int($line['cli']) : 'NULL') . ', '
                    . (isset($line['hidden']) ? $db->escape_int($line['hidden']) : 'NULL')
                ;
            }
            $db->nakedQuery(
                'INSERT INTO `'.self::TABLE.'`(`type`, `time`, `catalog_id`, `entity_type`, `entity_id`, `attr_id`, `segment_id`, `user_id`, `comment`, `additional_data`, `cli`, `hidden`) VALUES ('.implode('),(', $insert_data).')'
            );
            $this->logger_cache = array();
        }
    }
    /**
     * @return Logger
     */
    public static function getInstance(){
        if (empty(self::$i)){
            self::$i = new self();
        }
        return self::$i;
    }
    /**
     * Добавляем строчку лога
     * @param type $data
     * @return type
     */
    public static function add($data){
		if (!\LPS\Config::ENABLE_LOGS){
			return;
		}
        self::getInstance()->log($data);
    }
    /**
     * ДОбавляем строчку лога
     * @param type $data
     * @throws \InvalidArgumentException
     */
    public function log($data){
        if (!in_array($data['type'], self::$log_types)){
            throw new \InvalidArgumentException('Incorrect log type "'.$data['type'].'". see '.__CLASS__.'::LOG_TYPE_* const');
        }
        $upd_data = array_intersect_key($data, array_flip(self::$allow_fields));
        if (empty($upd_data['cli'])){
            $upd_data['cli'] = \LPS\Config::isCLI() ? 1 : 0;
        }
        if (empty($upd_data['user_id']) && !\LPS\Config::isCLI() && empty($this->user_id)){
            $user = \App\Builder::getInstance()->getAccount()->getUser();
            if (!empty($user)){
                $this->user_id = (int) $user->getId();
            }
        }
        if (!empty($this->user_id) && empty($upd_data['user_id'])){
            $upd_data['user_id'] = $this->user_id;
        }
        if (isset($upd_data['additional_data']['c_id'])){
            $upd_data['catalog_id'] = $upd_data['additional_data']['c_id'];
            unset($upd_data['additional_data']['c_id']);
        }
        if (isset($upd_data['additional_data'])){
            $upd_data['additional_data'] = json_encode($upd_data['additional_data'], JSON_UNESCAPED_UNICODE);
        }
        $upd_data['time'] = microtime(1);
        $this->logger_cache[] = $upd_data;
        if (count($this->logger_cache) > self::STACK_SIZE){
            $this->save();
        }
    }
    /**
     * Иногда надо напрямую объяснять логеру, от какого пользователя идут изменения (например из крона)
     * @param int $user_id
     */
    public function setUserId($user_id){
        $this->user_id = $user_id;
    }
    
    /**
     * Выбор данных из лога
     * @param array $params ключи: 'id', 'type', 'from', 'to', 'entity_type', 'entity_id', 'segment_id', 'user_id', 'attr_id' все могут быть массивами или не быть вовсе
     * @param int $start
     * @param int $page_size
     * @param array $group задайте любые ключи для группировки в многоуровневый массив
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function search($params, &$count = 0, $start = 0, $page_size = 100000, $group = array('id')){
        $allow_keys = self::$allow_search_fields;
        foreach (array_keys($params) as $k){
            if (!in_array($k, $allow_keys)){
                throw new \InvalidArgumentException('Incorrect $params use this keys: '. implode(', ', $allow_keys));
            }
        }
        $db = \App\Builder::getInstance()->getDB();
        foreach ($allow_keys as $k){
            if (!isset($params[$k])){
                if (array_key_exists($k, $params)){//если NULL, то подставляем 1 для плейсхолдера в IS NULL
                    $params[$k] = 1;
                }else{
                    $params[$k] = $db->skipIt();
                }
			}else{
				if ($k == 'from'){
					$params['from'] = strtotime($params['from']);
				}elseif ($k == 'to'){
					$params['to'] = strtotime($params['to']);
				}
			}
        }
        $sql_part = '
            FROM  `'.self::TABLE.'` AS `vl`
            WHERE 1
                {AND `vl`.`id` '.(is_array($params['id']) ? ' IN (?i)' : ' = ?d').'}
                {AND `vl`.`type` '.(is_array($params['type']) ? ' IN (?l)' : ' = ?s').'}
                {AND `vl`.`time` > ?s}
                {AND `vl`.`time` < ?s}
                {AND `vl`.`catalog_id`'.(is_array($params['catalog_id']) ? ' IN (?i)' : ' = ?d').'}
                {AND `vl`.`entity_id`'.(is_array($params['entity_id']) ? ' IN (?i)' : ' = ?d').'}
				{AND `vl`.`entity_type`'.(is_array($params['entity_type']) ? ' IN (?l)' : ' = ?s').'}
                {AND `vl`.`attr_id`'.(is_array($params['attr_id']) ? ' IN (?l)' : ' = ?s').'}
                {AND `vl`.`segment_id` '.(is_array($params['segment_id']) ? ' IN (?i)' : ' = ?d').'}
                {AND `vl`.`user_id` '.(is_array($params['user_id']) ? ' IN (?i)' : ' = ?d').'}
                {AND `vl`.`cli` = ?d}
                {AND `vl`.`hidden` IS NULL AND ?d}
                {AND `vl`.`hidden` != ?d}
            ORDER BY `vl`.`id` DESC';
        $result = $db->query('
            SELECT `vl`.* ' . $sql_part . ' LIMIT ?d, ?d', $params['id'],
            $params['type'],
            $params['from'],
			$params['to'],
            $params['catalog_id'],
            $params['entity_id'],
			$params['entity_type'],
            $params['attr_id'],
            $params['segment_id'],
            $params['user_id'],
            $params['cli'],
            $params['hidden'],
            $params['not_hidden'],
            $start, $page_size
        );
        $result = call_user_func_array(array($result, 'select'), $group);
        $count = $db->query('SELECT COUNT(*) ' . $sql_part,
            $params['id'],
            $params['type'],
            $params['from'],
			$params['to'],
            $params['catalog_id'],
            $params['entity_id'],
			$params['entity_type'],
            $params['attr_id'],
            $params['segment_id'],
            $params['user_id'],
            $params['cli'],
            $params['hidden'],
            $params['not_hidden'])->getCell();
        foreach ($result as &$l){
            $l['additional_data'] = json_decode($l['additional_data'], TRUE);
        }
		return $result;
    }
    /**
     * Получить id всех пользователей, который что-ибо хоть раз меняли
     */
    public static function getUsers(){
        $db = \App\Builder::getInstance()->getDB();
        return $db->query('SELECT DISTINCT(`user_id`) FROM `'.self::TABLE.'`')->getCol('user_id', 'user_id');
    }
}
