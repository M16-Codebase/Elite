<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 13.05.15
 * Time: 16:03
 */

namespace Models\ContentManagement\PostHelpers;
use Models\ContentManagement\Post;

class PostTags extends PostHelper{
    protected static $i = NULL;
    protected static $fieldList = array('tag_list');
    private $cache = array();

    /**
     * возвращает значение дополнительного поля
     */
    public function get(Post $post, $field){
        if (in_array($field, self::$fieldList)){
            if (empty($this->cache[$post['id']])){
                $this->cache[$post['id']] = array_filter(explode('.', $post['tags']));
            }
            return $this->cache[$post['id']];
        }
    }

    /**
     * событие после изменения Post
     */
    public function onUpdate(Post $post){
        unset($this->cache[$post['id']]);
        return $post['id'];
    }

    /**
     * событие перед изменением
     */
    public function preUpdate(Post $post, &$params, &$errors = NULL){
        if (array_key_exists('tags', $params)){
            if (!empty($params['tags'])){
                $tags = is_array($params['tags']) ? $params['tags'] : explode(',', $params['tags']);
                $tags = array_filter(array_map('trim', $tags));
                $params['tags'] = '.' . implode('.', $tags) . '.';
            } else {
                $params['tags'] = NULL;
            }
        }
        return $post;
    }
}