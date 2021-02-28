<?php
namespace Models\CatalogManagement\CatalogHelpers\Type;
use Models\CatalogManagement\Type AS TypeEntity;
use Models\ContentManagement\Post as PostEntity;

class AttachedPosts extends TypeHelper{
    /**типы прикрепляемых статей*/
    const A_WARRANTY = 'warranty';
    const A_TOOLS = 'tools';
    const A_TECHNOLOGY = 'technology';
    const A_INSTALLATION = 'installation';
    public static $article_types = array(
        self::A_WARRANTY => 'Гарантия',
        self::A_TOOLS => 'Инструменты',
        self::A_TECHNOLOGY => 'Технологии',
        self::A_INSTALLATION => 'Внедрение');
    protected static $i = NULL;
    private $loadItemsQuery = array();
     private $dataCache = array();
    private $only_public_status = TRUE;
    /**
     * @return AttachedPosts
     */
    protected function __construct($publicOnly = NULL){
        parent::__construct();
        $this->only_public_status = !empty($publicOnly) ? TRUE : FALSE;
    }
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList(){
        return array('attaches', 'inheritable_attaches');
    }
    /**
     * возвращает значение дополнительного поля
     */
    public function get(TypeEntity $type, $field, $segment_id = NULL){
        if ($field=='attaches' || $field == 'inheritable_attaches'){
            if (!isset ($this->dataCache[$type['id']])){
                $this->loadData();
            }
            return !empty($this->dataCache[$type['id']][$field]) ? $this->dataCache[$type['id']][$field] : array();
        }
        return NULL;
    }
    /**
     * уведомление, что данные для указанных Types попали в кеш данных и могут быть востребованы
     */
    public function onLoad(TypeEntity $type){
        $this->loadItemsQuery[$type['id']] = $type['id'];
    }


    /**
     * @return array
     */
    public function loadData(){
        $statuses = array(PostEntity::STATUS_CLOSE, PostEntity::STATUS_PUBLIC);
        if ($this->only_public_status === FALSE){
            $statuses[] = PostEntity::STATUS_NEW;
        }
        $inheritable_posts = array();//наследуемые посты
        $posts = array();//посты, прикрепленные к типу
        $all_types = TypeEntity::factory($this->loadItemsQuery);//все зарегистрированные типы
        $types_ids = array();
        foreach ($all_types as $type){
            $types_ids = array_merge($types_ids, array($type->getData('id')), array_reverse($type->getData('parents')));//массив типов: сам + родители
        }
        $attaches = \Models\InternalLinkManager::getInstance()->search(
                array('catalog_type'=>$types_ids),
                array_keys(self::$article_types));//смотрим все связи с типами
        $posts_ids = array();
        $attaches_by_target = array();
        foreach (self::$article_types as $a_type => $a_type_rus){//пробегаемся по всем видам статей, чтобы собрать все айдишники статей
            if (!empty($attaches[$a_type])){
                $posts_ids = array_merge(array_keys($attaches[$a_type]), $posts_ids);
                foreach ($attaches[$a_type] as $obj_id => $obj){
                    $attaches_by_target[$a_type][$obj['target_id']] = $obj_id;//распределим статьи по типам
                }
            }
        }
        $all_posts = PostEntity::search(array('id' => $posts_ids, 'status' => $statuses));
        foreach ($all_types as $type){
            $parent_ids = array_reverse($type['parents']);
            foreach (self::$article_types as $a_type => $a_type_rus){//пробегаемся по всем видам статей
                $inheritable_posts[$a_type] = array();
                $posts[$a_type] = array();
                if (!empty($attaches[$a_type])){
                    //статья, прикрепленная к данному типу
                    $posts[$a_type] = !empty($attaches_by_target[$a_type][$type->getData('id')]) && !empty($all_posts[$attaches_by_target[$a_type][$type->getData('id')]]) ? $all_posts[$attaches_by_target[$a_type][$type->getData('id')]] : NULL;
                    //вычисляем наследуемую статью
                    foreach ($parent_ids as $p_id){
                        if (!empty($attaches_by_target[$a_type][$p_id])){
                            $type_post_id = $attaches_by_target[$a_type][$p_id];
                            $inheritable_posts[$a_type] = $p_id == $type->getData('id') ? $posts[$a_type] : (!empty($all_posts[$type_post_id]) ? $all_posts[$type_post_id] : NULL);
                            if (!empty($inheritable_posts[$a_type])){
                                //$inheritable_posts[$a_type]['type_id'] = $type['id'];
                            }
                            break;
                        }
                    }
                }
            }
            $this->dataCache[$type['id']]['attaches'] = $posts;
            $this->dataCache[$type['id']]['inheritable_attaches'] = $inheritable_posts;
        }
    }

    public function onCreate(TypeEntity $type, $params){
        $post_id = PostEntity::create(TypeEntity::POST_TYPE);
        $this->db->query('UPDATE `'.  TypeEntity::TABLE.'` SET `post_id` = ?d WHERE `id` = ?d', $post_id, $type['id']);
    }
    /**
     *
     * @param TypeEntity $type
     */
    public function onDelete(TypeEntity $type){
        PostEntity::deleteFromDB($type['post_id']);
    }
}
?>