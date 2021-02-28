<?php
namespace Models\CatalogManagement\CatalogHelpers\Type;
use App\Configs\CatalogConfig;
use Models\CatalogManagement\Type;
use Models\CatalogManagement\TypeCoverImageCollection;
use Models\CatalogManagement\ItemsDefaultImageCollection;
use Models\Logger as MainLogger;
use Models\ContentManagement\Post as PostEntity;
class AdditionalFields extends TypeHelper{
	const TABLE_FIELDS = 'item_types_fields';
    const LOG_ENTITY_TYPE = 'item_type';

    const ITEM_COVER_URL_PATH = '/templates/Admin/img/item-icons/';
    const ITEM_COVER_FILES_PATH = '/templates/base/Admin/img/item-icons/';
    const ITEM_COVER_PROJECT_FILES_PATH = '/templates/project/Admin/img/item-icons/';
    private $item_cover_list = array();

    protected static $i = NULL;
    private $loadItemsQuery = array();
    private $dataCache = array();
    private static $additional_fields = array();
    private $post_to_type = array();
    private static $fields_list = array(
        'post',
        'post_id',
        'allow_item_property',//только для каталога: позволить использовать айтемы каталога как тип свойства
        'allow_variant_property',//только для каталога: позволить использовать варианты каталога как тип свойства
        'word_cases',//падежи айтемов\вариантов
        // Поля, переехавшие из SegmentFields
//        'title',
        'segment_data',
        // Images
        'cover_image_id',
        'cover',
        'default_image_id',
        'default',
        'number_prefix',
        'item_cover_name',
        'item_cover',
        'allow_item_url',
        'search_by_sphinx',
        'show_metatags_tab',
        'show_props_tab',
        'show_groups_tab',
        'show_text_tab',
        'show_cover_tab',
        'show_banner_tab',
        'enable_view_mode',
        'allow_segment_properties'
    );
    private static $lang_fields_list = array('title');
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList(){
        if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE) {
            return array_merge(self::$fields_list, self::$lang_fields_list);
        }
        return self::$fields_list;
    }
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Type $type, $field, $segment_id = NULL){
        if (in_array($field, $this->fieldsList())){
            $this->loadData();
            $segment_fields = !empty($this->dataCache[$type['id']]) ? $this->dataCache[$type['id']] : NULL;
            if ($field == 'segment_data'){
                if (!empty($segment_fields['post_id'])) {
                    foreach($segment_fields['post_id'] as $s_id => $post_id) {
                        $segment_fields['post'][$s_id] = \Models\ContentManagement\Post::getById($post_id);
                    }
                }
                return $segment_fields;
            } elseif ($field == 'item_cover') {
                // url обложки айтема
                $file_name = $type['item_cover_name'];
                $file_list = $this->getItemCoversList();
                return !empty($file_list[$file_name]) ? $file_list[$file_name] : NULL;
            }
			//если свойство не сегментированное и единственное в таблице, то его и отдаем
			if (!empty($segment_fields[$field]) && count($segment_fields[$field] == 1) && !empty($segment_fields[$field][0])){
                if ($field == 'cover'){
                    return !empty($segment_fields['cover_image_id'][0]) ? \Models\ImageManagement\Image::getById($segment_fields['cover_image_id'][0]) : NULL;
                }elseif ($field == 'default'){
                    return !empty($segment_fields['default_image_id'][0]) ? \Models\ImageManagement\Image::getById($segment_fields['default_image_id'][0]) : NULL;
                }elseif ($field == 'post'){
                    return !empty($segment_fields['post_id'][0]) ? \Models\ContentManagement\Post::getById($segment_fields['post_id'][0]) : NULL;
                }
				return $segment_fields[$field][0];
			}
			//если свойство сегментированно, то отдаем его по сегментам
            if (!empty($segment_fields[$field][$segment_id])){
                if ($field == 'cover'){
                    return !empty($segment_fields['cover_image_id'][$segment_id]) ? \Models\ImageManagement\Image::getById($segment_fields['cover_image_id'][$segment_id]) : NULL;
                }elseif ($field == 'default'){
                    return !empty($segment_fields['default_image_id'][$segment_id]) ? \Models\ImageManagement\Image::getById($segment_fields['default_image_id'][$segment_id]) : NULL;
                }elseif ($field == 'post'){
                    return !empty($segment_fields['post_id'][$segment_id]) ? \Models\ContentManagement\Post::getById($segment_fields['post_id'][$segment_id]) : NULL;
                }
                return $segment_fields[$field][$segment_id];
            }
        }
        return NULL;
    }
    /**
     * уведомление, что данные для указанных Types попали в кеш данных и могут быть востребованы
     */
    public function onLoad(Type $type){
        if (!isset($this->dataCache[$type['id']])){
            $this->loadItemsQuery[$type['id']] = $type['id'];
        }
    }
    public function loadData(){
        if (empty ($this->loadItemsQuery)){
            return;
        }
        $db = \App\Builder::getInstance()->getDB();
        $segment_data = $db->query('
            SELECT `type_id`, `field`, IF (`segment_id` IS NULL, 0, `segment_id`) AS `segment_id`, `value` 
            FROM `'.self::TABLE_FIELDS.'` 
                WHERE `type_id` IN (?i)
            ',  $this->loadItemsQuery
        )->getCol(array('type_id', 'field', 'segment_id'), 'value');
        if (!empty($segment_data)){
            $this->dataCache = $segment_data + $this->dataCache;
            foreach($segment_data as $type_id => $fields_data){
                if (!empty($fields_data['post_id'])){
                    \Models\ContentManagement\Post::prepare($fields_data['post_id']);
                }
                if (!empty($fields_data['cover'])){
                    $this->dataCache[$type_id]['cover_image_id'] = $fields_data['cover'];
                    \Models\ImageManagement\Image::prepare($fields_data['cover']);
                }
                if (!empty($fields_data['default'])){
                    $this->dataCache[$type_id]['default_image_id'] = $fields_data['default'];
                    \Models\ImageManagement\Image::prepare($fields_data['default']);
                }
                if (!empty($fields_data['rules'][0])){
                    $this->dataCache[$type_id]['rules'][0] = json_decode($fields_data['rules'][0], TRUE);
                }
                if (!empty($fields_data['word_cases'][0])){
                    $this->dataCache[$type_id]['word_cases'][0] = json_decode($fields_data['word_cases'][0], TRUE);
                }
                if (!empty($fields_data['post'])){
                    $this->dataCache[$type_id]['post_id'] = $fields_data['post'];
                    PostEntity::prepare($fields_data['post']);
                } else {
                    //для перехода от старых версий, и чтобы картинки тоже можно было подгружать к посту, создаем пост сразу.
                    if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE) {
                        $segments = \App\Segment::getInstance()->getAll();
                        foreach($segments as $s) {
                            $post_id = $this->createPost($type_id, $s['id']);
                            $this->dataCache[$type_id]['post_id'][$s['id']] = $post_id;
                        }
                    } else {
                        $post_id = $this->createPost($type_id);
                        $this->dataCache[$type_id]['post_id'][0] = $post_id;
                    }
                    PostEntity::prepare($this->dataCache[$type_id]['post_id']);
                }
            }
        }
		$this->loadItemsQuery = array();
    }
//    public function loadDatas(){
////        if (empty($this->segment_id)){
////            throw new \Exception('При регистрации хелпера не задан segment_id');
////        }
//        $db = \App\Builder::getInstance()->getDB();
//        if (!empty($this->loadItemsQuery)){
//            $data = $db->query(
//                'SELECT `f`.`type_id`, `f`.`value` AS `post_id`, `f`.`segment_id` FROM `'.Type::TABLE_FIELDS.'` AS `f`
//				WHERE `f`.`type_id` IN (?i) AND `f`.`field` = "post"',//' AND `f`.`segment_id` IN (?i)',
//                $this->loadItemsQuery
////                $this->segment_id
//            )->getCol(array('type_id', 'segment_id'), 'post_id');
//            $post_ids = array();
//            foreach ($this->loadItemsQuery as $type_id){
//                if (!empty($data[$type_id][$this->segment_id])){
//                    $post_ids[$type_id] = $data[$type_id][$this->segment_id];
//                }else{
//                    //для перехода от старых версий, и чтобы картинки тоже можно было подгружать к посту, создаем пост сразу.
//                    $post_id = PostEntity::create(self::POST_TYPE);
//                    $db->query('REPLACE INTO `'.Type::TABLE_FIELDS.'` '
//                        . 'SET `type_id` = ?d, `field` = "post", `value` = ?d, `segment_id` = ?d',
//                        $type_id, $post_id, $this->segment_id);
//                    $post_ids[$type_id] = $post_id;
//                }
//            }
//            $this->dataCache = $post_ids;
//            PostEntity::prepare($post_ids);
//        }
//        $this->loadItemsQuery = array(); // конечные данные в кеше, так что чистим очередь
//    }
    public function preCreate(&$params, &$errors){
        foreach ($this->fieldsList() as $f){
            if (array_key_exists($f, $params)){
                if ($f == 'title'){
                    if (empty($params[$f])){
                        $errors['title'] = \Models\Validator::ERR_MSG_EMPTY;
                    }else{
                        $segments = \App\Segment::getInstance()->getAll();
                        foreach ($segments as $s){
                            if (empty($params[$f][$s['id']])){
                                $errors['title['.$s['id'].']'] = \Models\Validator::ERR_MSG_EMPTY;
                            }
                        }
                    }
                }elseif($f == 'word_cases'){
                    $params[$f] = json_encode($params[$f], JSON_UNESCAPED_UNICODE);
                }
                self::$additional_fields['create'][$f] = $params[$f];
                unset($params[$f]);
            }
        }
    }
    /**
     * После создания, что делать с доп полями
     * @param Type $type
     * @param array $params
     * @return int
     */
    public function onCreate(Type $type, $params){
        if (!empty(self::$additional_fields['create'])){
            $db = \App\Builder::getInstance()->getDB();
            foreach (self::$additional_fields['create'] as $field_name => $data){
                if (is_array($data)){
                    foreach ($data as $s_id => $val){
                        if (!is_string($val)){
                            throw new \LogicException('Неверно переданы данные для создания типа');
                        }
                        $db->query('INSERT INTO `'.self::TABLE_FIELDS.'` SET `type_id` = ?d, `field` = ?s, `segment_id` = ?d, `value` = ?s', $type['id'], $field_name, $s_id, $val);
                    }
                }else{
                    $db->query('INSERT INTO `'.self::TABLE_FIELDS.'` SET `type_id` = ?d, `field` = ?s, `segment_id` = ?d, `value` = ?s', $type['id'], $field_name, NULL, $data);
                }
            }
            unset(self::$additional_fields['create']);
            $this->onLoad($type);
        }
    }
    public function preUpdate(Type $type, &$params, &$errors){
        foreach ($this->fieldsList() as $f){
            if (array_key_exists($f, $params)){
                if($f == 'word_cases'){
                    $params[$f] = json_encode($params[$f], JSON_UNESCAPED_UNICODE);
                } elseif ($f == 'title'){
                    if (empty($params[$f])){
                        $errors['title'] = \Models\Validator::ERR_MSG_EMPTY;
                    }else{
                        $segments = \App\Segment::getInstance()->getAll();
                        foreach ($segments as $s){
                            if (empty($params[$f][$s['id']])){
                                $errors['title['.$s['id'].']'] = \Models\Validator::ERR_MSG_EMPTY;
                            }
                        }
                    }
                } elseif ($f == 'number_prefix') {
                    // Для базы обращений number_prefix обязателен
                    $catalog = $type->getCatalog();
                    if ($catalog['key'] == CatalogConfig::FEEDBACK_KEY){
                        $val = $params[$f];
                        if (empty($val)){
                            $errors[] = array(
                                'key' => $f,
                                'title' => 'Префикс номера обращения',
                                'error' => \Models\Validator::ERR_MSG_EMPTY
                            );
                        } else {
                            // И уникален
                            $db = \App\Builder::getInstance()->getDB();
                            $val_exists = $db->query('SELECT 1 FROM ?# WHERE `type_id` != ?d AND `field` = ?s AND `value` = ?s LIMIT 1',
                                self::TABLE_FIELDS,
                                $type['id'],
                                $f,
                                $val)->getCell();
                            if ($val_exists) {
                                $errors[] = array(
                                    'key' => $f,
                                    'title' => 'Префикс номера обращения',
                                    'error' => 'unique'
                                );
                            }
                        }
                    }
                }
                self::$additional_fields[$type['id']][$f] = $params[$f];
                unset($params[$f]);
            }
        }
    }
    /**
     * @param Type $type
     * @return type
     * @throws \LogicException
     */
    public function onUpdate(Type $type){
        if (empty(self::$additional_fields[$type['id']])){
            return;
        }
        $this->loadData();
        $db = \App\Builder::getInstance()->getDB();
        foreach (self::$additional_fields[$type['id']] as $field_name => $data){
            if (is_array($data)){
                foreach ($data as $s_id => $val){
                    if (!is_string($val)){
                        throw new \LogicException('Неверно переданы данные для редактирования типа');
                    }
                    if (isset($this->dataCache[$type['id']][$field_name][$s_id]) && $this->dataCache[$type['id']][$field_name][$s_id] == $val){
                        continue;
                    }
                    $db->query('REPLACE INTO `'.self::TABLE_FIELDS.'` SET `type_id` = ?d, `field` = ?s, `segment_id` = ?d, `value` = ?s', $type['id'], $field_name, $s_id, $val);
                    $this->dataCache[$type['id']][$field_name][$s_id] = $val;
                    if (\LPS\Config::ENABLE_LOGS){
                        $log_data = array(
                            'type' => MainLogger::LOG_TYPE_EDIT,
                            'entity_type' => self::LOG_ENTITY_TYPE,
                            'entity_id' => $type['id'],
                            'attr_id' => $field_name,
                            'segment_id' => $s_id,
                            'additional_data' => array(
                                't' => $type['title'],
                                't_is_c' => $type->isCatalog(),
                                't_c' => $type->getCatalog()['title'],
                                'v' => $val
                            )
                        );
                        MainLogger::add($log_data);
                    }
                }
            }else{
                $checker_val = $field_name == 'word_cases' ? json_decode($data, TRUE) : $data;
                if (isset($this->dataCache[$type['id']][$field_name]) && array_key_exists(0, $this->dataCache[$type['id']][$field_name]) && $this->dataCache[$type['id']][$field_name][0] == $checker_val){
                    continue;
                }
                $db->query('REPLACE INTO `'.self::TABLE_FIELDS.'` SET `type_id` = ?d, `field` = ?s, `segment_id` = 0, `value` = ?s', $type['id'], $field_name, $data);
                $this->dataCache[$type['id']][$field_name][0] = $checker_val;
                if (\LPS\Config::ENABLE_LOGS){
                    $log_data = array(
                        'type' => MainLogger::LOG_TYPE_EDIT,
                        'entity_type' => self::LOG_ENTITY_TYPE,
                        'entity_id' => $type['id'],
                        'attr_id' => $field_name,
                        'additional_data' => array(
                            't' => $type['title'],
                            't_is_c' => $type->isCatalog(),
                            't_c' => $type->getCatalog()['title'],
                            'v' => $checker_val
                        )
                    );
                    MainLogger::add($log_data);
                }
            }
        }
        unset(self::$additional_fields[$type['id']]);
    }
    /**
     *
     * @param Type $type
     */
    public function onDelete(Type $type){
        $db = \App\Builder::getInstance()->getDB();
        $db->query('DELETE FROM `'.self::TABLE_FIELDS.'` WHERE `type_id` = ?d', $type['id']);
        if (!empty($this->dataCache[$type['id']]['cover_image_id'])){
            foreach($this->dataCache[$type['id']]['cover_image_id'] as $cover_id){
                \Models\ImageManagement\Image::del($cover_id);
            }
        }
        if (!empty($this->dataCache[$type['id']]['default_image_id'])){
            foreach($this->dataCache[$type['id']]['default_image_id'] as $default_image_id){
                \Models\ImageManagement\Image::del($default_image_id);
            }
        }
    }
    /**
     * Для некоторых хелперов возвращаем в массиве знаения доп полей
     * @param Type $type
     * @param type $data
     */
    public function asArray(Type $type, &$data){
        foreach ($this->fieldsList() as $f){
            $data[$f] = $this->get($type, $f);
        }
    }

    /**
     * Загрузка обложки к типу товара
     * @param Type $type
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|null $FILE
     * @param string $error
     * @return \Models\ImageManagement\Image
     */
    public function uploadCover(Type $type, \Symfony\Component\HttpFoundation\File\UploadedFile $FILE = null, &$error = null){
        $db = \App\Builder::getInstance()->getDB();
        $cover = $type['cover'];
        $collection = TypeCoverImageCollection::getById(TypeCoverImageCollection::COLLECTION_ID);
        if (empty($collection)){
            throw new \LogicException('Не задана галерея для обложек типов');
        }
        $action = 'edit';
        if (empty($FILE)){
            if (!empty($type['cover'])){
                $db->query('DELETE FROM `'.Type::TABLE_FIELDS.'` WHERE `type_id` = ?d AND `field`="cover"', $type['id']);
                \Models\ImageManagement\Image::del($type['cover_image_id']);
            }
            $action = 'delete';
            $cover = NULL;
        }else{
            if (empty($cover)){
                $cover = $collection->addImage($FILE, '', $error, FALSE, FALSE);
                if (empty($error)){
                    $db->query('REPLACE INTO `'.Type::TABLE_FIELDS.'` SET `type_id` = ?d, `field`="cover", `segment_id` = NULL, `value` = ?d', $type['id'], $cover['id']);
                }
                $action = 'add';
            }else{
                $error = $cover->reload($FILE);
            }
        }
        $catalog = $type->getCatalog();
        $data = array(
            'type' => MainLogger::LOG_TYPE_IMG,
            'entity_type' => 'item_type',
            'entity_id' => $type['id'],
            'attr_id' => 'cover',
            'comment' => $action,
            'additional_data' => array(
                't' => $type['title'],
                'is_c' => $type->isCatalog(),
                'c' => $catalog['title']
            )
        );
        MainLogger::add($data);
        return $cover;
    }
    /**
     *
     * @param \Models\CatalogManagement\Type $type
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $FILE
     * @param string $error
     * @return \Models\ImageManagement\Image
     * @throws \LogicException
     */
    public function uploadDefault(Type $type, \Symfony\Component\HttpFoundation\File\UploadedFile $FILE = null, &$error = null){
        $db = \App\Builder::getInstance()->getDB();
        $default = $type['default'];
        $collection = ItemsDefaultImageCollection::getById(ItemsDefaultImageCollection::COLLECTION_ID);
        if (empty($collection)){
            throw new \LogicException('Не задана галерея для дефолтных обложек типов');
        }
        $action = MainLogger::LOG_TYPE_EDIT;
        if (empty($FILE)){
            if (!empty($type['default'])){
                $db->query('DELETE FROM `'.Type::TABLE_FIELDS.'` WHERE `type_id` = ?d AND `field`="default"', $type['id']);
                \Models\ImageManagement\Image::del($type['default_image_id']);
            }
            $action = MainLogger::LOG_TYPE_DEL;
            $default = NULL;
        }else{
            if (empty($default)){
                $default = $collection->addImage($FILE, '', $error, FALSE, FALSE);
                if (empty($error)){
                    $db->query('REPLACE INTO `'.Type::TABLE_FIELDS.'` SET `type_id` = ?d, `field`="default", `segment_id` = NULL, `value` = ?d', $type['id'], $default['id']);
                }
                $action = MainLogger::LOG_TYPE_CREATE;
            }else{
                $error = $default->reload($FILE);
            }
        }
        $catalog = $type->getCatalog();
        $data = array(
            'type' => MainLogger::LOG_TYPE_IMG,
            'entity_type' => 'item_type',
            'entity_id' => $type['id'],
            'attr_id' => 'default',
            'comment' => $action,
            'additional_data' => array(
                't' => $type['title'],
                't_is_c' => $type->isCatalog(),
                'c_t' => $catalog['title']
            )
        );
        MainLogger::add($data);
        return $default;
    }

    public function createPost($type_id, $segment_id = NULL){
        $post_id = PostEntity::create(Type::POST_TYPE);
        $this->db->query('INSERT INTO `'.  Type::TABLE_FIELDS.'` SET `type_id` = ?d, `field` = "post", `value` = ?d, `segment_id` = ?d ',
            $type_id,
            $post_id,
            $segment_id
        );
        //создание в логи не пишем, так как нет смысла
        return $post_id;
    }

    public function editPost(Type $type, $params = array(), $segment_id){
        if (empty($params)){
            return;
        }
        /* @var $post PostEntity */
        $post = $this->get($type, 'post', $segment_id);// $type['post'];
        if (empty($post)){
            $post_id = $this->createPost($type['id'], $segment_id);
            $post = PostEntity::getById($post_id);
        }
        $post_old_data = $post->asArray();
        $updates = array();
        foreach ($params as $f => $v){
            if (array_key_exists($f, $post_old_data) && $v != $post_old_data[$f]){
                $updates[$f] = $v;
            }
        }
        if (!empty($updates)){
            $post->edit($updates, $errors);
            if (empty($errors)){
                $catalog = $type->getCatalog();
                $data = array(
                    'type' => MainLogger::LOG_TYPE_POST,
                    'entity_type' => 'item_type',
                    'entity_id' => $type['id'],
                    'attr_id' => 'post',
                    'comment' => MainLogger::LOG_TYPE_EDIT,
                    'additional_data' => array(
                        't' => $type['title'],
                        't_is_c' => $type->isCatalog(),
                        'c_t' => $catalog['title'],
                        'v' => $post['id']
                    )
                );
                MainLogger::add($data);
            }
        }
    }

    public function getTypeByPost(PostEntity $post){
        if ($post['type'] != Type::POST_TYPE){
            return FALSE;
        }
        if (!isset($this->post_to_type[$post['id']])){
            $data = $this->db->query('SELECT `type_id`, `segment_id` FROM `' . Type::TABLE_FIELDS . '` WHERE `field` = "post" AND `value` = ?d', $post['id'])->getRow();
            if (!empty($data)){
                $this->post_to_type[$post['id']] = Type::getById($data['type_id'], $data['segment_id']);
            } else {
                $this->post_to_type[$post['id']] = FALSE;
            }
        }
        return $this->post_to_type[$post['id']];
    }

    /**
     * Список файлов для обложки айтема в админке каталога
     * @return array
     */
    public function getItemCoversList(){
        if (empty($this->item_cover_list)){
            $work_dir = file_exists(\LPS\Config::getRealDocumentRoot().self::ITEM_COVER_PROJECT_FILES_PATH)
                ? \LPS\Config::getRealDocumentRoot().self::ITEM_COVER_PROJECT_FILES_PATH
                : \LPS\Config::getRealDocumentRoot().self::ITEM_COVER_FILES_PATH;
            if (file_exists(\LPS\Config::getRealDocumentRoot().self::ITEM_COVER_FILES_PATH)){
                $file_list = scandir($work_dir, SCANDIR_SORT_ASCENDING);
                foreach($file_list as $file_name){
                    if (substr($file_name, 0, 1) == '.'){
                        // Пропускаем папки и скрытые файлы
                        continue;
                    }
                    $this->item_cover_list[$file_name] = self::ITEM_COVER_URL_PATH . $file_name;
                }
            }
        }
        return $this->item_cover_list;
    }
}
