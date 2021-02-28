<?php
namespace Models\ContentManagement\PostHelpers\Interfaces;
use Models\ContentManagement\Comment;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of iItemDataProvider
 *
 * @author olga
 */
interface iCommentDataProvider{
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList();
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Comment $comment, $field);
    /**
     * предупреждение, что данные для указанных Comments попали в кеш данных
     */
    public function onLoad(Comment $comment);
    /**
     * событие на создание нового Comment
     */
    public function onCreate($id);
     /**
     * событие после изменения Comment
     */
    public function onUpdate($id);
     /**
     * событие перед изменением
     */
    public function preUpdate(Comment $comment, &$params, &$errors);
    
    public function onDelete($id);
    
    public function onCleanup();
}

?>