<?php
namespace Models\ContentManagement\PostHelpers;
use Models\ContentManagement\Post;
use Models\ContentManagement\Comment AS CommentEntity;
/**
 *  Механизм связывания изображений с постами. Изображения привязываются к текстам,
 *  в частности изображение привязанное по Post_id должно реально привязываться по first_id
 */

class Comments extends PostHelper{
    protected static $i = NULL;
    protected static $fieldList = array('comments');
    protected $db;
	protected function __construct(){
        $this->db = \App\Builder::getInstance()->getDB();
        parent::__construct();
	}
    /**
     * возвращает значение дополнительного поля
     */
    function get(Post $post, $field){
        if (!in_array($field, $this->fieldsList())){
            throw new \InvalidArgumentException('Неверное название дополнительного поля');
        }
        if (!isset ($this->dataCache[$post['id']]))
            $this->loadData();
        unset($this->dataCache[$post['id']][$post['first_id']]);//теоретически этот коммент есть всегда, и его не должно быть в выводе всех комментариев
        return $this->dataCache[$post['id']];
    }

    private $loadItemsQuery = array();
    private $dataCache = array();
    /**
     * уведомление, что данные для указанных Items попали в кеш данных и могут быть востребованы
     */
    function onLoad(Post $post){
        $id = $post['id'];
        if (!isset($this->dataCache[$id])){
            CommentEntity::prepareIds($post['comment_ids']);
			$this->loadItemsQuery[$id] = $post['comment_ids'];
        }
    }

    protected function loadData(){
        if (empty ($this->loadItemsQuery))
            return;
        $comment_ids = array();
        foreach ($this->loadItemsQuery as $post_id => $c_ids){
            $comment_ids += $c_ids;
        }
        $comments = CommentEntity::factory($this->loadItemsQuery);
        foreach ($this->loadItemsQuery as $post_id => $c_ids){
            $this->dataCache[$post_id] = array_intersect_key($comments, array_flip($c_ids));
        }
        $this->loadItemsQuery = array(); // конечные данные в кеше, так что чистим очередь
    }
    
    public function onDelete(Post $post){
        $comment_ids = $post['comment_ids'];
        foreach ($comment_ids as $id){
            CommentEntity::deleteFromDB($id);
        }
        if (!empty($this->dataCache[$post['id']])){
            unset($this->dataCache[$post['id']]);
        }
    }
}