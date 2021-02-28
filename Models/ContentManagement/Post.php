<?php
namespace Models\ContentManagement;

use Models\ContentManagement\PostHelpers\Interfaces\iPostDataProvider;
use LPS\Components\Translit;
/**
 * Посты
 * @TODO работу с тегами надо переписать, всех условий связанных с тегами можно избежать. достаточно после загрузки разобрать, перед сохранением собрать
 */
class Post implements \ArrayAccess {
    const MIN_TITLE_CHARS = 3;
    const MIN_TEXT_CHARS = 10;
    const MAX_REGISTRY_LEN = 1000;
    const TABLE_NAME = 'posts';
    const COMMENTS_TABLE = 'comments';
    /** статусы */
    const STATUS_NEW	= 'new';
    const STATUS_HIDDEN = 'hidden';
    const STATUS_PUBLIC = 'public';
    const STATUS_CLOSE	= 'close';//закрыто от комментариев
    const STATUS_DELETE = 'delete';
    protected static $post_status_list = array(
        self::STATUS_NEW	=> 'черновик',
        self::STATUS_PUBLIC	=> 'опубликован',
        self::STATUS_CLOSE => 'опубликован, комментарии закрыты',
        self::STATUS_HIDDEN	=> 'скрыт',
        self::STATUS_DELETE	=> 'удален'
    );
    // Массив допустимых статусов при редактировании (нельзя удалять пост через edit)
    protected static $allow_update_status = array(self::STATUS_NEW, self::STATUS_CLOSE, self::STATUS_PUBLIC, self::STATUS_HIDDEN);
    /** поля, загружаемые из базы "как есть"*/
    protected static $loadFields = array('id', 'status', 'type', 'title', 'top', 'last_id', 'first_id',
        'comments', 'num', 'annotation', 'theme_id', 'segment_id', 'tags', 'data', 'key', 'page_url_id', 'complete_text', 'site_links_done', 'full_version', 'last_update'
    );
    /** дополнительные вычисляемые поля из базы */
    protected static $allowFields = array('timestamp', 'last_timestamp', 'pub_timestamp', 'comment_ids');
    /**
     * текст поста (объект комментария)
     * @var Comment
     */
    private $post_text = NULL;
    /** разрешенные параметры для редактирования */
    protected static $updateFields = array(
        'type',    // у поста может быть тип, для того чтобы различать разные типы постов
        'status',  // у постов есть статусы, по умолчанию: new - новый, mod - на модерации, public - опубликован, close - закрыт(комменты писать нельзя), delete - удален
        'title',   // У постов могут быть заголовки
        'top',     // Посты можно прибивать "к верху", для того чтобы менять порядок следования их в выдаче
        'theme_id', // Посты можно разбивать по темам
        'num',       // Позиция поста
        'annotation', //Аннотация к посту
        'segment_id',
        'data',
		'key',
        'tags',        // тэги для поиска
        'complete_text',  // текст поста со вставленными ссылками
        'site_links_done' // флаг о проведенной перелинковке поста
    );
    private static $loadIds = array();
    private static $registry = array();
    /**
     * @var DbSimple_Mysql
     */
    protected $db = null;
    protected $data = array();
    private $needSave = false;
    /**
     *
     * @param array $ids
     */
    public static function prepare($ids){
        if (!empty($ids)){
            if (!is_array($ids)){
                $ids = array($ids);
            }
            $ids = array_diff($ids, array_keys(self::$registry), self::$loadIds);
            if (!empty($ids)){
                self::$loadIds = array_merge($ids, self::$loadIds);
            }
        }
    }
    /**
     * @param array $ids
     * @return Post[]
     */
    public static function factory($ids){
        if (empty($ids)){
            return array();
        }
        $getIds = array_unique(array_merge($ids, self::$loadIds));
        $db = \App\Builder::getInstance()->getDB();
        if (count(self::$registry) + count($getIds) > self::MAX_REGISTRY_LEN){
            self::clearRegistry();
        }
        if (!empty(self::$registry)){
            $getIds = array_diff($getIds, array_keys(self::$registry));
        }
        if (!empty($getIds)){
            $result = $db->query('
                SELECT `p`.`'.implode('`,`p`.`', static::$loadFields).'`,
                    UNIX_TIMESTAMP(`first`.`dt`) AS `timestamp`,
                    UNIX_TIMESTAMP(`first`.`pub_date`) AS `pub_timestamp`,
                    UNIX_TIMESTAMP(IF(`last`.`dt` IS NULL, `first`.`dt`, `last`.`dt`)) AS `last_timestamp`,
                    GROUP_CONCAT(`c`.`id` SEPARATOR ",") AS `comment_ids`
                FROM
                    `'.self::TABLE_NAME.'` AS `p`
                    INNER JOIN `'. Comment::TABLE .'` AS `first` ON (`first`.`id` = `p`.`first_id`)
                    INNER JOIN `'. Comment::TABLE .'` AS `c`     ON (`c`.`post_id` = `p`.`id`)
                    LEFT  JOIN `'. Comment::TABLE .'` AS `last`  ON (`last`.`id` = `p`.`last_id`)
                WHERE
                    `p`.`id` IN (?i)
                GROUP BY `p`.`id`', $getIds)
            ->select('id');
            foreach ($getIds as $id){
                if (empty($result[$id])){
                    self::$registry[$id] = NULL;
                } else {
                    $post_class = $result[$id]['type'] == 'pages' ? '\Models\ContentManagement\SegmentPost' : '\Models\ContentManagement\Post';
                    self::$registry[$id] = new $post_class($result[$id]);
                }
            }
        }
		$result = array();
		foreach ($ids as $id){
			$result[$id] = !empty(self::$registry[$id]) ? self::$registry[$id] : NULL;
		}
        return $result;
    }
    /**
     * 
     * @param int $id
     * @return Post
     */
    public static function getById($id){
        if (empty($id)){
            return NULL;
        }
        $posts = static::factory(array($id));
        return !empty($posts[$id]) ? $posts[$id] : NULL;
    }
	/**
	 * 
	 * @param type $key
	 * @param type $segment_id
	 * @return Post
	 */
	public static function getByKey($key, $segment_id = NULL){
		$posts = self::search(array('key' => $key, 'segment_id' => $segment_id), $count, 0, 1);
		if (empty($posts)){
			return NULL;
		}
		return reset($posts);
	}
    /**
     * вычистить информацию из реестра
     * @param array $ids
     */
    public static function clearRegistry($ids = NULL){
        if (empty($ids)){
            $ids = array_keys(self::$registry);
        }
        $ids = is_array($ids) ? $ids : array($ids);
        foreach ($ids as $id){
            $post = isset(self::$registry[$id]) ? self::$registry[$id] : NULL;//не используем getById, т.к. данная функция используется в factory, т.е. получится бесконечная рекурсия
            if (!empty($post)){
                $post->save();
                unset(self::$registry[$id]);
            }
        }
    }
    /**
     * Создание
     * @param string $type
     * @param string $theme_id
     * @return int
     */
    public static function create($type = NULL, $theme_id = NULL, $full_version = TRUE) {
        $db = \App\Builder::getInstance()->getDB();
        if (empty($type)){
            $type = 'default';
        }
        //вычисляем последний номер позиции
        $max_num = $db->query('
            SELECT MAX(`num`) FROM ?# WHERE ?#=?',
            self::TABLE_NAME,
            !empty($theme_id) ? 'theme_id' : 'type',
            !empty($theme_id) ? $theme_id : $type
        )->getCell();
        $post_id = $db->query('INSERT INTO `'.self::TABLE_NAME.'` SET ?a',
            array('type'=> $type, 'status'=>self::STATUS_NEW, 'num' => $max_num + 1, 'theme_id' => $theme_id, 'full_version' => $full_version ? 1 : 0)
        );
        $user = \App\Builder::getInstance()->getAccount()->getUser();
        $first_id = Comment::create($post_id, '', array('user_id' => $user['id']));//создаем первый комментарий
        if (empty($first_id)){
            throw new \ErrorException('Не создается первый комментарий');
        }
        //обновляем данные о комменте в посте
        $db->query('UPDATE `'.self::TABLE_NAME.'` SET `first_id` = ?, `last_id` = ? WHERE `id` = ?', $first_id, $first_id, $post_id);
        foreach (self::$dataProviders as $p){
            /* @var $p iPostDataProvider */
            $p->onCreate($post_id);
        }
        return $post_id;
    }

    public function copy(&$errors = array()){
        $post_id = static::create($this['type'], $this['theme_id'], $this['full_version']);
        $post = static::getById($post_id);
        if (empty($post)) {
            $errors['post'] = 'cant_create';
            return FALSE;
        }
        /** @var \Models\ImageManagement\Collection $old_gallery */
        $old_gallery = $this['gallery'];
        $new_gallery = $old_gallery->copy($errors, $img_url_match);
        $body = $post->_makeText();
        // Заменяем галерею
        \Models\ImageManagement\Collection::delete($body['collection_id']);
        $body->edit(array('collection_id' => $new_gallery['id']));
        $update_data = array();
        foreach(static::$updateFields as $field){
            $update_data[$field] = $this[$field];
        }
        // Тело поста копируем неперелинкованным
        $update_data['complete_text'] = NULL;
        $update_data['site_link_done'] = 0;
        $text = $this['raw_text'];
        if (!empty($img_url_match['old']) && !empty($img_url_match['new'])) {
            $text = str_replace($img_url_match['old'], $img_url_match['new'], $text);
        }
        $update_data['text'] = $text;
        $post->edit($update_data, $errors);
        // Комментарии
        $comments = $this['comments'];
        if (!empty($comments)){
            foreach($comments as $c){
                $post->addComment($c['text'], array('user_id' => $c['user_id']));
            }
        }
        return $post;
    }
    /**
     * Получение постов на пересечении параметров
     * Список допустимых параметров:
     *  (array|string)status // фильтр по статусу
     *  (array|string)type   // фильтр по типу
     *  (array|int)theme_id, (array|string)theme_key // фильтры по теме
     *  (array|string)tags, (array|string)no_tags // фильтры по тегам статьи
     *  (array|int)user_id   // пользователь или список пользователей, которые написали пост
     *  (array|int)id        // допустимых постов или id искомого поста
     *  (array|string)ip     // IP адресс(а) с которого(рых) опубликован пост
     *  (timestamp)before_dt, (timestamp)after_dt  // временной интервал (только админ может видеть посты которые будут опубликованны)
     *  (array|int)no_id, (array|int)no_theme_id   // множества значений которые не должны быть найдены
     *  string where_token    // произвольный кусок SQL вставляется в конец секции where ! Использовать ОСТОРОЖНО! НЕ ВАЛИДИРУЕТСЯ
     *  string from_token     // произвольный кусок SQL вставляется в конец секции where ! Использовать ОСТОРОЖНО! НЕ ВАЛИДИРУЕТСЯ
     *  array order_by_ids   // сортировка по id списку содержащему порядок следования айдишников
     *
     * @param array $params
     * @param int $count общее количество постов удовлетворяющих критериям поиска
     * @param int $start c какой позиции начинать считывать результат выборки
     * @param int $page_size сколько элементов считать из выборки
     * @param enum $sort one of list "top", "comment", "post", "comments_count" порядок сортировки.
     * @return Post[]
     */
    public static function search($params, &$count=false, $start=0, $page_size=100, $sort = 'num') {
    	$sort_sql = array(
	        'top'                 => '`p`.`top` DESC, `p`.`last_id` DESC',
	        'comment'             => '`p`.`last_id` DESC',
	        'post'                => '`p`.`id` DESC',
	        'comments_count'      => '`p`.`comments` DESC',
	        'id'                  => '`p`.`id`',
            'num'                 => '`p`.`num`',
            'dt'                  => '`first`.`dt` DESC',
			'pub_date'			  => '`first`.`pub_date` DESC'
	    );
        if (!empty($params['theme_all'])){
            // Список постов по всем вложенным темам
            $themes = Theme::getInstance()->search(array('theme_all' => $params['theme_all']));
            if (empty($themes)){
                return array();
            } else {
                $params['theme_id'] = array_keys($themes);
            }
            unset($params['theme_all']);
        }
        $params['order_token'] = isset($sort_sql[$sort]) ? $sort_sql[$sort] : $sort;
        $params = static::normalizeParams($params);
        $allowParams = array_merge(static::$loadFields, static::$allowFields, array('order_by_ids', 'select_token', 'from_token', 'where_token', 'order_token', 'from_dt', 'to_dt', 'public_blog', 'tag', 'not_id', 'from_pub_date', 'to_pub_date', 'empty_complete_text', 'empty_segment_id', 'not_empty_segment_id', 'date', 'time'));
        $params = static::prepareParams($params, $allowParams);
        $db = \App\Builder::getInstance()->getDB();
        $result = $db->query('
                SELECT '.($count !== false ? 'SQL_CALC_FOUND_ROWS' : '').'
                `p`.`id`' .
                (!empty($params['select_token']) ? ", \n" . implode(", \n", $params['select_token']) : '') . '
            FROM
                `'.self::TABLE_NAME.'` AS `p`
                INNER JOIN `'.self::COMMENTS_TABLE.'` AS `first` ON (`first`.`id` = `p`.`first_id`)
                LEFT  JOIN `'.self::COMMENTS_TABLE.'` AS `last`   ON (`last`.`id` = `p`.`last_id`)
                '.(!empty($params['from_token']) ? implode("\n", $params['from_token']) : ''). '
            WHERE
                `p`.`status` != "'.self::STATUS_DELETE.'"
                { AND `p`.`status`  '.((!empty($params['status']) and is_array($params['status'])) ? 'IN (?l)' : ' = ?s').' }
                { AND `p`.`type`    '.((!empty($params['type']) and is_array($params['type'])) ? 'IN (?l)' : ' = ?s').' }
                { AND `p`.`id`     '.((!empty($params['id']) and is_array($params['id'])) ? 'IN (?i)' : ' = ?d').' }
                { AND `p`.`id` != ?d }
                { AND `first`.`dt` >= FROM_UNIXTIME(?d) }
                { AND `first`.`dt` <= FROM_UNIXTIME(?d) }
				{ AND `first`.`pub_date` >= FROM_UNIXTIME(?d) }
                { AND `first`.`pub_date` <= FROM_UNIXTIME(?d) }
                { AND (`segment_id` = ?d OR `segment_id` IS NULL)}
                { AND `segment_id` IS NULL AND ?d}
                { AND `segment_id` IS NOT NULL AND ?d}
				{ AND `p`.`theme_id` ' . ((!empty($params['theme_id']) && is_array($params['theme_id'])) ? 'IN (?i)' : '= ?d') . '}
                { AND `first`.`pub_date` <= NOW() AND ?d}
                { AND `p`.`tags` LIKE ?s}
				{ AND `p`.`key` = ?s}
				{ AND `p`.`site_links_done` = ?s}
				{ AND `p`.`complete_text` IS NULL AND ?d}
				{ AND `p`.`page_url_id` ' . (!empty($params['page_url_id']) && is_array($params['page_url_id']) ? 'IN (?l)' : '= ?s') . '}
				'.(!empty($params['where_token']) ? ' AND '. implode("\n AND ", $params['where_token']):'').'
            GROUP BY `p`.`id` /* сделано для совместимости с возможными from_token */
            ORDER BY '.(!empty($params['order_by_ids']) ? 'FIELD(`p`.`id`, ' . implode(', ', $params['order_by_ids']) . ')' : $params['order_token']).'
            LIMIT ?d, ?d',
            !empty($params['status'])  ? $params['status']  : $db->skipIt(),
            !empty($params['type'])    ? $params['type']    : $db->skipIt(),
            !empty($params['id'])      ? $params['id']      : $db->skipIt(),
            !empty($params['not_id'])  ? $params['not_id']  : $db->skipIt(),
            !empty($params['from_dt']) ? $params['from_dt'] : $db->skipIt(),
            !empty($params['to_dt'])   ? $params['to_dt']   : $db->skipIt(),
			!empty($params['from_pub_date']) ? $params['from_pub_date'] : $db->skipIt(),
            !empty($params['to_pub_date'])   ? $params['to_pub_date']   : $db->skipIt(),
            isset($params['segment_id']) ? $params['segment_id'] : $db->skipIt(),
            !empty($params['empty_segment_id']) ? 1 : $db->skipIt(),
            !empty($params['not_empty_segment_id']) ? 1 : $db->skipIt(),
			!empty($params['theme_id']) ? $params['theme_id'] : $db->skipIt(),
            !empty($params['public_blog']) ? 1 : $db->skipIt(),
            !empty($params['tag']) ? '%.'.$params['tag'].'.%' : $db->skipIt(),
			!empty($params['key']) ? $params['key'] : $db->skipIt(),
            // опции для перелинковки
            isset($params['site_links_done']) ? (!empty($params['site_links_done']) ? 1 : 0) : $db->skipIt(),
            !empty($params['empty_complete_text']) ? 1 : $db->skipIt(),
            !empty($params['page_url_id']) ? $params['page_url_id'] : $db->skipIt(),

            $start,
            $page_size
        )->getCol('id', 'id');
        // получить количество записей
        if ($count!==false)
            $count = $db->query('SELECT FOUND_ROWS()')->getCell();
        return empty($result) ? array() : self::factory($result);
    }
    /**
     * Нормализация позволяет писать в параметрах флаги и они будут преобразованны в ключи массива с единичным значением
     * @param array $params
     * @return array
     */
    protected static function normalizeParams(array $params) {
        if (!isset($params[0]))
            return $params;
        foreach ($params as $k => $v) {
            if (is_int($k)) {
                unset($params[$k]);
                $result[$v] = 1;
            }
        }
        return $result;
    }

    protected static function prepareParams(array $params, array $allowParams) {
        foreach ($params as $k => $v) {
            if (in_array($k, $allowParams)) {
                $params[$k] = $v;
            } else {
                throw new \Exception('Incorrect params key:' . $k); //ошибка логическая, так что валимся смело
            }
        }
        return $params;
    }
    public static function getPostStatusList(){
    	return static::$post_status_list;
    }
    public static function getLoadFields(){
        return self::$loadFields;
    }
    /**
     * 
     * @param int $id post id
     */
    public static function deleteFromDB($id){
        $post = self::getById($id);
        if (empty($post)){
            return false;
        }
        $db = \App\Builder::getInstance()->getDB();
        $db->query('DELETE FROM `'.self::TABLE_NAME.'` WHERE `id` = ?d', $id);
        //вслед за постами надо удалить комменты
        $comment_ids = $db->query('SELECT `id` FROM `'.Comment::TABLE.'` WHERE `post_id` = ?d', $id)->getCol(NULL, 'id');
        $db->query('DELETE FROM `'.Comment::TABLE.'` WHERE `post_id` = ?d', $id);
        self::clearRegistry($id);
        Comment::clearRegistry($comment_ids);
    }
    protected function __construct($data){
        $this->db = \App\Builder::getInstance()->getDB();
        $object_fields = array_merge(static::$loadFields, static::$allowFields);
        foreach ($object_fields as $field){
            if (!array_key_exists($field, $data)){
                throw new \LogicException('Недостаточно данных для создания объекта. Нет поля ' . $field);
            }
            $this->data[$field] = $field == 'comment_ids' ? explode(',', $data[$field]) : $data[$field];
        }
        Comment::prepareIds($this->data['comment_ids']);
        foreach (self::$dataProviders as $p){
            /** @var $p iPostDataProvider */
            $p->onLoad($this);
        }
    }
    public function __destruct() {
        $this->save();
    }
    protected function getData($key){
        if ($key == 'url'){
            // урл основан на ключе поста если ключ задан, в противном случае транслит заголовка
            return '/'.$this->data['type'].'/'.(!empty($this->data['theme_key'])?$this->data['theme_key'].'-':'').Translit::UrlTranslit(!empty($this->data['key']) ? $this->data['key'] : $this->data['title']).'-'.$this->data['id'] . '/';//.'.html';
        }
        if (array_key_exists($key, $this->data)){
            return $this->data[$key];
        }else{
            throw new \LogicException('У поста не предусмотрен параметр ' . $key);
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
    public function _makeText(){
        if (empty($this->post_text)){
            $this->post_text = Comment::getById($this->data['first_id']);
            $comment = $this->post_text;
        }
        return $this->post_text;
    }

    /**
     *
     * @param string $text
     * @param array $author
     * @return int
     */
    public function addComment($text='', $author = array()){
        return Comment::create($this->getData('id'), $text, $author);
    }
    public function getComments(){
        return Comment::search(array('post_id' => $this->data['id']));
    }
    public function delete() {
        $this->setData('status', self::STATUS_DELETE);
        foreach (self::$dataProviders as $p){
            /* @var $p iPostDataProvider */
            $p->onDelete($this);
        }
        self::clearRegistry(array($this['id']));
    }
    public static function validateFormData($params, &$errors){
        $params = static::normalizeParams($params);
        if (!empty($params))
            static::validate($params, $errors);
        return $params;
    }
    /**
     * Валидация
     * @param array $params
     * @param array $errors
     * @param string $status
     * @return bool
     */
    protected static function validate($params, &$errors, $status = self::STATUS_NEW){
        $status = isset($params['status']) ? $params['status'] : $status;
        $errors = array();
        $validator = \Models\Validator::getInstance(\App\Builder::getInstance()->getRequest());
        if (in_array($status, array(self::STATUS_CLOSE, self::STATUS_PUBLIC))){
            $validator->checkValue($params['title'], 'checkString', $errors['title'], array('count_min' => static::MIN_TITLE_CHARS));
            $validator->checkValue($params['text'], 'checkString', $errors['text'], array('count_min' => static::MIN_TEXT_CHARS));
        }
        if (isset($params['status']) && !in_array($params['status'], self::$allow_update_status)){
            $errors['status'] = \Models\Validator::ERR_MSG_INCORRECT_FORMAT;
        }
        if (isset($params['theme_id'])){
            if (!empty($params['theme_id'])){
                $theme = Theme::getInstance()->getById($params['theme_id']);
                if (empty($theme)){
                    $errors['theme'] = 'not_found';
                } elseif ($theme['theme_count']) {
                    $errors['theme'] = 'has_child_themes';
                }
            } else {
                $params['theme_id'] = NULL;
            }
        }
        return \Models\Validator::isErrorsEmpty($errors);
    }

    /**
     * Удаляет из параметров неизменившиеся значения
     * @param array $params
     * @return array
     */
    private function cleanNotChangedParams($params){ 
        foreach ($params as $k => $value){
            $key = ($k == 'text') ? 'raw_text' : $k;
            if ($value == $this[$key]){
                unset($params[$k]);
            }
        }
        return $params;
    }
    /**
     * Редактирование
     * @param array $params
     * @return boolean
     * @throws \LogicException
     */
    public function edit($params, &$errors = array()) {
        if (!empty($params['last_update']) && $params['last_update'] < $this['last_update']){
            $errors['obj'] = 'already_changed';
            return FALSE;
        }
        $params = static::normalizeParams($params);
        foreach (static::$dataProviders as $p){
            /* @var $p iPostDataProvider */
            $p->preUpdate($this, $params);
        }
        foreach($params as $k=>$v){
            if (!in_array($k, static::$updateFields) && !in_array($k, Comment::getUpdateFields())){
                unset($params[$k]);
            }
        }
        if (empty ($params))
            return true;
        if (!$this->validate($params, $errors, $this['status']))
            return false;
        if (!empty($params['status']) && in_array($params['status'], array(self::STATUS_CLOSE, self::STATUS_PUBLIC)) && empty($params['key'])){
            // при публикации поста замораживаем урл, записываем тайтл транслитом в ключ если ключ еще не был задан
            // пока ключ поста не задан урл меняется при любых изменениях заголовка поста
            // как только мы задали ключ - урл фиксируется и редактируется только при смене ключа
            $params['key'] = Translit::UrlTranslit(!empty($params['title']) ? $params['title'] : $this['title']);
        }
        // При валидации нужны все поля, поэтому очистку неизменных делаем после валидации
        $params = $this->cleanNotChangedParams($params);
        if (empty ($params))
            return NULL;//нечего менять
        if (!empty($params['theme_id'])){
            // Если меняется тема, нужно не поломать порядок постов, поэтому тащим пост в конец....
            $change_post_theme = TRUE;
            $max_num = $this->db->query('
            SELECT MAX(`num`) FROM ?# WHERE `theme_id`=? AND `type`=?',
                self::TABLE_NAME,
                $params['theme_id'],
                $this['type']
            )->getCell();
            $params['num'] = $max_num+1;
        }
        foreach (static::$updateFields as $field){
            if (array_key_exists ($field, $params)){
                if ($field == 'theme_id' && empty($params[$field])){
                    $params[$field] = NULL;
                }
                $this->setData($field, $params[$field]);
                unset($params[$field]);
            }
        }
        if ($this->_makeText()){
            $this->post_text->edit($params, $this->needSave);
        }else{
            throw new \LogicException('У поста нет первого коммента!!!');
        }
        foreach (static::$dataProviders as $p){
        /* @var $p iPostDataProvider */
            $p->onUpdate($this);
        }
        if (!empty($change_post_theme)){
            // ....Если меняется тема .... а потом в начало
            $this->changePosition(1);
        }
        return TRUE;//что-то поменялось
    }
    public function save(){
        if ($this->needSave) {
            $updateFields = array();
            foreach (static::$updateFields as $field) {
                $updateFields[$field] = $this->data[$field];
            }
            $this->db->query('UPDATE `' . self::TABLE_NAME . '` SET ?a, `last_update` = NOW() WHERE `id` = ?d', $updateFields, $this->data['id']);
        }
        $this->needSave = false;
    }
    public function changePosition($move_num){
        if (!empty($move_num)){
            if ($this['num'] > $move_num){
                $this->db->query('
                    UPDATE `'.self::TABLE_NAME.'`
                    SET `num`=`num`+1
                    WHERE `theme_id`=?d AND `type`=?s AND `num`>=?d AND `num`<?d',
                    $this['theme_id'],
                    $this['type'],
                    $move_num,
                    $this['num']
                 );
            }else{
                $this->db->query('
                    UPDATE `'.self::TABLE_NAME.'`
                    SET `num`=`num`-1
                    WHERE `theme_id`=?d AND `type`=?s AND `num`<=?d AND `num`>?d',
                    $this['theme_id'],
                    $this['type'],
                    $move_num,
                    $this['num']
                );
            }
            $this->setData('num', $move_num);
            $this->save();
        }
    }
    
     static function cleanup(){
        $db = \App\Builder::getInstance()->getDB();
        $db->query('
            DELETE `p`, `c`
            FROM `'.Comment::TABLE.'` AS `c`
                LEFT JOIN `'.self::TABLE_NAME.'` AS `p` ON (`c`.`post_id` = `p`.`id`)
            WHERE
                `p`.`id` IS NULL OR
                `p`.`status`="'.self::STATUS_DELETE.'"
        ');
        Comment::cleanup();
        foreach (self::$dataProviders as $p){
            /** @var $p iPostDataProvider */
            $p->onCleanup();
        }
     }
     
     public function asArray(){
         $result = $this->data;
         foreach(self::$dataProvidersByFields as $field => $provider){
             $result[$field] = $this[$field];
         }
         // из-за перелинковки у нас появились поля raw_text и complete_text, поле text возвращает complete_text если это возможно
         // для редактора постов нам нужен raw_text
         $result['text'] = $this['raw_text'];
         $result['author'] = $this['author'];
         $result['email'] = $this['email'];
         $result['user_id'] = $this['user_id'];

         return $result;
     }
	 
	 public function getUrl($segment_id = null){
         return \App\Configs\PostConfig::getUrl($this, $segment_id);
	 }
    
    /******************************* работа с iPostDataProvider *****************************/
    /**
     * @var iPostDataProvider[]
     */
    static $dataProviders = array();
    /**
     * @var iPostDataProvider[]
     */
    static $dataProvidersByFields = array();

    /**
     * @static
     * @param iPostDataProvider $provider
     */
    static function addDataProvider(iPostDataProvider $provider){
        self::$dataProviders[get_class($provider)] = $provider;
        foreach ($provider->fieldsList() as $field){
            self::$dataProvidersByFields[$field] = $provider;
        }
    }

    /**
     * @static
     * @param iPostDataProvider $provider
     */
    static function delDataProvider(iPostDataProvider $provider){
        unset(self::$dataProviders[get_class($provider)]);
    }
    
    /******************************* ArrayAccess *****************************/

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists ($offset){
        if (in_array($offset, array('access', 'url', 'raw_text', 'complete_text'))){
            return true;
        }
        if(isset($this->data[$offset]) || isset(static::$dataProvidersByFields[$offset])){
            return true;
        }
        $this->_makeText();
        if (!empty($this->post_text[$offset])){
            return true;
        }
        return false;
    }

    /**
     * Для перелинковки (SEO) было добавлено поле complete_text, предназначенное для поста со вставленными ссылками
     * теперь по
     * @param string $offset
     * @throws \Exception
     * @return mixed
     */
    public function offsetGet ($offset){
        if(isset(static::$dataProvidersByFields[$offset])) {
            return static::$dataProvidersByFields[$offset]->get($this, $offset);
        } elseif ($offset == 'raw_url') {
            // Мы не используем $post.url нигде, только в конструкторе урла, поэтому в ArrayAccess он доступен через raw_url
            // Не использовать нигде в коде, нужен урл — $post->getUrl($segment_id)
            return $this->getData('url');
        }elseif (array_key_exists($offset, $this->data)) {
            return $this->getData($offset);
        }elseif ($offset == 'text' && $this->getData('complete_text')) {//@TODO возможно надо в хелперы запихнуть
            return $this->getData('complete_text');
        }elseif ($this->_makeText()){
            return $this->post_text[($offset == 'raw_text' ? 'text' : $offset)];
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