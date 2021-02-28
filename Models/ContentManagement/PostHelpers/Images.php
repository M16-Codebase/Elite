<?php
namespace Models\ContentManagement\PostHelpers;
use Models\ContentManagement\Comment;
use Models\ContentManagement\CommentImageCollection;
/**
 *  Механизм связывания изображений с постами. Изображения привязываются к текстам,
 *  в частности изображение привязанное по Comment_id должно реально привязываться по first_id
 */

class Images extends CommentHelper{
    protected static $i = NULL;
    protected static $cache = array();
    protected static $fieldList = array('gallery');
    protected $db;
    
    protected function __construct(){
        $this->db = \App\Builder::getInstance()->getDB();
        parent::__construct();
    }
    /**
     * возвращает значение дополнительного поля
     */
    function get(Comment $comment, $field){
        if (!in_array($field, $this->fieldsList())){
            throw new \InvalidArgumentException('Неверное название дополнительного поля');
        }
        $c_id = $comment['id'];
        if (!isset(self::$cache[$c_id])){
            if (empty($comment['collection_id'])){
                $collection_id = CommentImageCollection::create(\Models\ImageManagement\Collection::TYPE_COMMENT);
                $comment->edit(array('collection_id' => $collection_id));
            }
            self::$cache[$c_id] = CommentImageCollection::getById($comment['collection_id']);
        }
        return self::$cache[$c_id];
    }
    /**
     * уведомление, что данные для указанных Comments попали в кеш данных и могут быть востребованы
     */
    function onLoad(Comment $comment) {
         CommentImageCollection::prepare(array($comment['collection_id']));
    }
    /**
     * 
     * @param int $id comment id
     */
    function onDelete($id){
        $comment = Comment::getById($id);
        CommentImageCollection::delete($comment['collection_id']);
        if (!empty(self::$cache[$id])){
            unset(self::$cache[$id]);
        }
    }
}