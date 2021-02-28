<?php
namespace Models\ContentManagement\PostHelpers\Interfaces;
use Models\ContentManagement\Post;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of iItemDataProvider
 *
 * @author olga
 */
interface iPostDataProvider{
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList();
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Post $post, $field);
    /**
     * предупреждение, что данные для указанных Posts попали в кеш данных
     */
    public function onLoad(Post $post);
    /**
     * событие на создание нового Post
     */
    public function onCreate($id);
     /**
     * событие после изменения Post
     */
    public function onUpdate(Post $post);
     /**
     * событие перед изменением
     */
    public function preUpdate(Post $post, &$params, &$errors = NULL);
    
    public function onDelete(Post $post);
    
    public function onCleanup();
    
}

?>