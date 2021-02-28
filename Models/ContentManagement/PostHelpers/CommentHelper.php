<?php
namespace Models\ContentManagement\PostHelpers;
use Models\ContentManagement\Comment;
/**
 * Description of CommentHelper
 *
 * @author charles manson
 */
class CommentHelper implements Interfaces\iCommentDataProvider{
    protected static $i = NULL;
    protected static $fieldList = array();
	/**
	 *
	 * @return Images
	 */
    public static function factory(){
        if (empty (static::$i)){
            static::$i = new static();
        }
        return static::$i;
    }
    protected function __construct(){
        Comment::addDataProvider($this);
    }
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList(){
        return static::$fieldList;
    }
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Comment $comment, $field){}
    /**
     * предупреждение, что данные для указанных Comments попали в кеш данных
     */
    public function onLoad(Comment $comment){
        return $comment;
    }
    /**
     * событие на создание нового Comment
     */
    public function onCreate($id){
        return $id;
    }
     /**
     * событие после изменения Comment
     */
    public function onUpdate($id){
        return $id;
    }
     /**
     * событие перед изменением
     */
    public function preUpdate(Comment $comment, &$params, &$errors){
        return $comment;
    }
    
    public function onDelete($id){
        return $id;
    }
    
    public function onCleanup(){}
}
