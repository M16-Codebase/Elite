<?php
namespace Models\ContentManagement\PostHelpers;
use Models\ContentManagement\Post;
/**
 * Description of PostHelper
 *
 * @author charles manson
 */
abstract class PostHelper implements Interfaces\iPostDataProvider{
    protected static $i = NULL;
    protected static $fieldList = array();
    /**
	 *
	 * @return PostDateTime
	 */
    public static function factory(){
        if (empty (static::$i)){
            static::$i = new static();
        }
        return static::$i;
    }
	protected function __construct(){
        Post::addDataProvider($this);
	}
    public function fieldsList(){
        return static::$fieldList;
    }
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Post $post, $field){}
    /**
     * предупреждение, что данные для указанных Posts попали в кеш данных
     */
    public function onLoad(Post $post){
        return $post;
    }
    /**
     * событие на создание нового Post
     */
    public function onCreate($id){
        return $id;
    }
     /**
     * событие после изменения Post
     */
    public function onUpdate(Post $post){
        return $post['id'];
    }
     /**
     * событие перед изменением
     */
    public function preUpdate(Post $post, &$params, &$errors = NULL){
        return $post;
    }
    
    public function onDelete(Post $post){}
    
    public function onCleanup(){}
}
