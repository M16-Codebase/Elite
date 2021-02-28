<?php
/**
 * Description of Comment
 *
 * @author olga
 */
namespace Models\ContentManagement;
use Models\ContentManagement\PostHelpers\Interfaces\iCommentDataProvider;
class Comment implements \ArrayAccess{
    const TABLE = 'comments';
    const MAX_REGISTRY_LEN = 1000;
    const STATUS_PUBLIC = 'public';
    const STATUS_DELETED = 'delete';
    protected static $loadFields = array('id', 'post_id', 'user_id', 'status', 'text', 'dt', 'pub_date', 'collection_id', 'author', 'email');
    /** Разрешенные для редактирования поля*/
    protected static $updateFields = array(
        'user_id',// автор первого сообщения
        'status',
        'text',   // содержание первого сообщения
        'dt',      // время первого сообщения в посте
        'pub_date', // время отложенной публикации
        'collection_id', //id коллекции картинок
        'author',
        'email'
    );
    public static function getUpdateFields(){
        return static::$updateFields;
    }
    private static $registry = array();
    
    protected static $userIds = array();
    protected static $users = array();
    /**
     * @var DbSimple_Mysql
     */
    protected $db = null;
    protected $data = array();
    private $needSave = false;
    private static $loadIds = array();

    public static function prepareIds($ids = array()){
        if (!empty($ids)){
            $ids = array_diff($ids, array_keys(self::$registry), self::$loadIds);
            if (!empty($ids)){
                self::$loadIds = array_merge($ids, self::$loadIds);
            }
        }
    }
    /**
     * @param array $ids
     * @return Comment[]
     */
    public static function factory($ids){
        $getIds = array_unique(array_merge($ids, self::$loadIds));
        $db = \App\Builder::getInstance()->getDB();
        if (!empty(self::$registry)){
            $getIds = array_diff($getIds, array_keys(self::$registry));
        }
        if (count(self::$registry) + count($getIds) > self::MAX_REGISTRY_LEN){
            self::clearRegistry();
        }
        if (!empty($getIds)){
            $result = $db->query('
                SELECT `'.implode('`,`', static::$loadFields).'`
                FROM `'.self::TABLE.'`
                WHERE
                    `id` IN (?i)', $getIds)
            ->select('id');
            foreach ($getIds as $id){
                $comment = !empty($result[$id]) ? new Comment($result[$id]) : NULL;
                self::$registry[$id] = $comment;
            }
        }
        $result = array();
        foreach ($ids as $id){
            $result[$id] = !empty(self::$registry[$id]) ? self::$registry[$id] : NULL;
        }
        return $result;
    }

    /**
     * Взять по id
     * @param int $id
     * @return Comment|null
     */
    public static function getById($id){
        $images = self::factory(array($id));
        return !empty($images[$id]) ? $images[$id] : NULL;
    }
    /**
     * Создаем коммент к посту
     * @param int $post_id
     * @param string $text
     * @param array $author
     * @return int
     */
    public static function create($post_id, $text = '', $author = array()){
        $collection_id = CommentImageCollection::create(\Models\ImageManagement\Collection::TYPE_COMMENT);
        $db = \App\Builder::getInstance()->getDB();
        $comment_id = $db->query(
            'INSERT INTO `'.self::TABLE.'`
             SET
                `post_id` = ?d,
                `dt` = NOW(),
                `pub_date` = NOW(),
                `text` = ?s,
                {`user_id` = ?d, }
                `collection_id` = ?d
            ',
             $post_id,
             $text,
             array_key_exists('user_id', $author) ? $author['user_id'] : $db->skipIt(),
             $collection_id
        );
        foreach (self::$dataProviders as $p){
            /* @var $p iCommentDataProvider */
            $p->onCreate($comment_id);
        }
        return $comment_id;
    }
    /**
     * вычистить информацию из реестра
     * @param array $ids
     */
    public static function clearRegistry($ids = NULL){
        if (empty($ids)){
            $ids = array_keys(self::$registry);
        }
        foreach ($ids as $id){
            $comment = isset(self::$registry[$id]) ? self::$registry[$id] : NULL;//не используем getById, т.к. данная функция используется в factory, т.е. получится бесконечная рекурсия
            if (!empty($comment)){
                $comment->save();
                unset(self::$registry[$id]);
            }
        }
    }
    /**
     * Получение дополнительных текстов к постам на пересечении параметров
     * Список допустимых параметров:
     * (string)post_type,
     * (array|int)post_theme_id,
     * (array|int)post_no_theme_id,
     * (array|int)post_id,
     * (array|int)id,
     * (array|string)status
     * (array|string)ip
     * (array|string)user_id
     * (int)before_dt
     * (int)after_dt
     *
     * @param array $params
     * @param int $count общее количество комментов удовлетворяющих критериям поиска
     * @param int $start c какой позиции начинать считывать результат выборки
     * @param int $page_size сколько элементов считать из выборки
     * @param int $order порядок сортировки по дате
     * @param string $format full|count
     * @return array of comments rows
     */
    public static function search($params, &$count=null, $start=0, $page_size=100, $order='asc', $format='full') {
        $account = \App\Builder::getInstance()->getAccount();
        $db = \App\Builder::getInstance()->getDB();
        if ($start<0)
            $start=0;
        $allowParams=array(
            'post_id', 'id', 'status', 'user_id', 'ip', 'before_dt', 'after_dt',
        );
      //  if (!$this->checkParams($allowParams, $params))
      //      return false;
        if (!isset($params['before_dt']) OR (!$account instanceof Admin and $params['before_dt'] > time()))  {
        	//Все кроме админа не могут видеть посты, которые будут опубликованны
        	$params['before_dt'] = time();
        }
        $start = abs(intval($start)); $page_size = abs(intval($page_size));
        $query =
            'SELECT SQL_CALC_FOUND_ROWS ' .
 /*               `p`.`id`       AS `post_id`,
                `p`.`type`     AS `post_type`,
                `p`.`title`    AS `post_title`,
                `p`.`comments` AS `post_comments`,
                `p`.`theme_id`,*/
                '`c`.`id` ' .
 /*               `c`.`text`,
                `c`.`user_id`,
                `c`.`dt`,
                `c`.`status`,
                INET_NTOA(`c`.`ip`) AS `ip`, 
                `u`.`login` AS `user_login`,
                IF(`u`.`name`!="",`u`.`name`,`u`.`login`) AS `user_name`,
                UNIX_TIMESTAMP(`c`.`dt`) AS `timestamp`,
                INET_NTOA(`c`.`ip`),
                `t`.`keyword` AS `theme_key`,
                `t`.`title`   AS `theme_title`*/
           'FROM
                `comments` AS `c`
            INNER JOIN `posts` AS `p` ON (
                `p`.`id` = `c`.`post_id`
                AND `c`.`id` <> `p`.`first_id`
                { AND `p`.`type`     '.((!empty($params['post_type'])        and is_array($params['post_type']))        ? 'IN (?l)' : ' = ?').' }
                { AND `p`.`theme_id` '.((!empty($params['post_theme_id'])    and is_array($params['post_theme_id']))    ? 'IN (?i)' : ' = ?d').' }
                { AND `p`.`theme_id` '.((!empty($params['post_no_theme_id']) and is_array($params['post_no_theme_id'])) ? 'NOT IN (?i)' : ' != ?d').' }
                { AND `p`.`id`       '.((!empty($params['post_id'])          and is_array($params['post_id']))          ? 'IN (?i)' : ' = ?d').' }
            )
            INNER JOIN `comments` AS `first` ON (
                `first`.`id` = `p`.`first_id`
                {AND (
                        (`p`.`status` IN ("public", "close") AND `p`.`title`  != "")
                         OR (`first`.`user_id` = ?d  AND `p`.`status` != "delete")
                )}
            ) ' .
//            INNER JOIN `users`  AS `u` ON (`u`.`id` = `c`.`user_id` )
            'LEFT  JOIN `themes` AS `t` ON (`t`.`id` = `p`.`theme_id`)
            WHERE
                `c`.`status`              '.((!empty($params['status'])  and is_array($params['status']))  ? 'IN (?l)' : ' = ?' ).'
                { AND `c`.`id`            '.((!empty($params['id'])      and is_array($params['id']))      ? 'IN (?i)' : ' = ?d').' }
                { AND `c`.`user_id`       '.((!empty($params['user_id']) and is_array($params['user_id'])) ? 'IN (?i)' : ' = ?d').' }
                { AND INET_NTOA(`c`.`ip`) '.((!empty($params['ip'])      and is_array($params['ip']))      ? 'IN (?i)' : ' = ?d').' }
                { AND `c`.`dt` >= FROM_UNIXTIME(?d) }
                { AND `c`.`dt` <= FROM_UNIXTIME(?d) }
            ORDER BY `c`.`id` '.(strtolower($order)=='desc' ? 'DESC' : '').'
            LIMIT ?d, ?d
        ';
        $result = $db->query($query,
            !empty($params['post_type'])        ? $params['post_type']        : $db->skipIt(),
            !empty($params['post_theme_id'])    ? $params['post_theme_id']    : $db->skipIt(),
            !empty($params['post_no_theme_id']) ? $params['post_no_theme_id'] : $db->skipIt(),
            !empty($params['post_id'])          ? $params['post_id']          : $db->skipIt(),
            $account instanceof Admin ? $db->skipIt() : (
            	$account instanceof User ? $account->getUser()->getId() : 0
            ),
            !empty($params['status'])           ? $params['status']    : 'public',
            !empty($params['id'])               ? $params['id']        : $db->skipIt(),
            !empty($params['user_id'])          ? $params['user_id']   : $db->skipIt(),
            !empty($params['ip'])               ? $params['ip']        : $db->skipIt(),
            !empty($params['after_dt'])         ? $params['after_dt']  : $db->skipIt(),
            !empty($params['before_dt'])        ? $params['before_dt'] : $db->skipIt(),
            $start, $page_size
        )->getCol('id', 'id');//select('id');
        $count = $db->query('SELECT FOUND_ROWS()')->getCell();
        if ($format == 'count'){
            return $count;
    	}
/*    	$user_list=array();
		foreach ($result as &$comment){
            $comment['text'] = $this->prepareHTML($comment['text']);
            $user_list[]=$comment['user_id'];
        }
        $user_list = array_unique($user_list);
        if (class_exists('UsersManagers')){
        	$user_data = UsersManagers::factory()->getUsersData($user_list);
        	foreach ($result as &$comment){
        		$comment['user_info'] = isset($user_data[$comment['user_id']]) ? $user_data[$comment['user_id']] : false;
        	}

        }*/
        return static::factory($result);
    }
    public static function getLoadFields(){
        return self::$loadFields;
    }
    public static function deleteFromDB($id){
        foreach (self::$dataProviders as $p){
            /* @var $p iCommentDataProvider */
            $p->onDelete($id);
        }
        return \App\Builder::getInstance()->getDB()->query('DELETE FROM `'.self::TABLE.'` WHERE `id` = ?d', $id);
    }
    protected function __construct($data){
        $this->db = \App\Builder::getInstance()->getDB();
        foreach (static::$loadFields as $field){
            if (!array_key_exists($field, $data)){
                throw new \LogicException('Недостаточно данных для создания объекта. Нет поля ' . $field);
            }
            $this->data[$field] = $data[$field];
        }
        if (!is_null($data['user_id'])) {
            static::$userIds[$data['user_id']] = $data['user_id'];
        }
        foreach (self::$dataProviders as $p){
            /** @var $p iCommentDataProvider */
            $p->onLoad($this);
        }
    }
    public function __destruct() {
        $this->save();
    }
    private static function loadUsers(){
        $users = \App\Auth\Users\Factory::getInstance()->getUsers(array('ids' => array_diff_key(static::$userIds, static::$users)));
        foreach($users as $user){
            static::$users[$user['id']] = $user;
        }
    }
    
    private function getUser(){
        $user_id = $this->data['user_id'];
        if (is_null($user_id)){
            return NULL;
        }
        if (!array_key_exists($user_id, static::$users)) {
            static::$userIds[$user_id] = $user_id;
            static::loadUsers();
        }
        return static::$users[$this->data['user_id']];
    }
    private function getData($key){
        if (array_key_exists($key, $this->data)){
            return $this->data[$key];
        }else{
            throw new \LogicException('У комментария не предусмотрен параметр ' . $key);
        }
    }
    /**
     * Переписывает данные объекта
     * @param string $key
     * @param mixed $value
     * @throws \LogicException
     */
    protected function setData($key, $value) {
        if (array_key_exists($key, $this->data)) {
            if (array_search($key, static::$updateFields) !== false){
                $this->data[$key] = $value;
                $this->needSave = true;
            }else{
                throw new \LogicException('Поле '.$key.' нельзя редактировать');
            }
        }else{
            throw new \LogicException('Для объекта изображения не предусмотрен параметр ' . $key);
        }
    }
    public function delete(){
        $this->setData('status', self::STATUS_DELETED);
    }
    /**
     * Редактирование коммента
     * @param array $params
     * @return boolean
     */
    public function edit($params, &$needSave = FALSE) {
        if (empty ($params))
            return true;
        foreach (self::$updateFields as $field){
            if (array_key_exists ($field, $params)){
                $this->setData($field, $params[$field]);
                unset($params[$field]);
            }
        }
        if ($this->needSave) $needSave = TRUE; // Нужно для того, чтобы в посте знать, что тело поста изменилось
		$this->save();
    }
    /**
     * Сохраняет в базу данные объекта, если они были изменены
     */
    public function save(){
        if ($this->needSave) {
            $updateFields = array();
            foreach (self::$updateFields as $field) {
                $updateFields[$field] = $this->data[$field];
            }
            foreach (self::$dataProviders as $p){
            /* @var $p iCommentDataProvider */
                $p->preUpdate($this, $updateFields, $errors);
            }
            $this->db->query('UPDATE `' . self::TABLE . '` SET ?a WHERE `id` = ?d', $updateFields, $this->data['id']);
            foreach (self::$dataProviders as $p){
            /* @var $p iCommentDataProvider */
                $p->onUpdate($this->data['id']);
            }
        }
        $this->needSave = false;
    }
    
    public static function cleanup(){
        $db = \App\Builder::getInstance()->getDB();
        $db->query('
            DELETE FROM `'.self::TABLE.'` WHERE `status` = ?s', self::STATUS_DELETED
        );
        $db->query('
            DELETE `collection`
            FROM `'.  \Models\ImageManagement\Collection::TABLE.'` AS `collection`
                LEFT JOIN `'.self::TABLE.'` AS `comment` ON (`comment`.`collection_id` = `collection`.`id`)
            WHERE
                `comment`.`id` IS NULL
                AND `collection`.`type` = "'.\Models\ImageManagement\Collection::TYPE_COMMENT.'"
        ');
        \Models\ImageManagement\CollectionImage::cleanup();
        foreach (self::$dataProviders as $p){
        /* @var $p iCommentDataProvider */
            $p->onCleanup();
        };
    }
    /******************************* работа с iCommentDataProvider *****************************/
    /**
     * @var iCommentDataProvider[]
     */
    static $dataProviders = array();
    /**
     * @var iPostDataProvider[]
     */
    static $dataProvidersByFields = array();

    /**
     * @static
     * @param iCommentDataProvider $provider
     */
    static function addDataProvider(iCommentDataProvider $provider){
        self::$dataProviders[get_class($provider)] = $provider;
        foreach ($provider->fieldsList() as $field){
            self::$dataProvidersByFields[$field] = $provider;
        }
    }

    /**
     * @static
     * @param iCommentDataProvider $provider
     */
    static function delDataProvider(iCommentDataProvider $provider){
        unset(self::$dataProviders[get_class($provider)]);
    }
    /******************************* ArrayAccess *****************************/

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists ($offset){
        return isset($this->data[$offset]) || isset(static::$dataProvidersByFields[$offset]);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet ($offset){
        if(isset(static::$dataProvidersByFields[$offset])){
            return static::$dataProvidersByFields[$offset]->get($this, $offset);
        }elseif (array_key_exists($offset, $this->data)){
            return $this->getData($offset);
        }else{
            throw new \Exception(
                'Notice: '.get_class($this).' #'.$this['id'].' Undefined index: "'.$offset.'"'
            );
        }
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
}

?>
