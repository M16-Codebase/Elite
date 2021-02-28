<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 13.07.15
 * Time: 19:51
 */

namespace Models\ContentManagement\PostHelpers;


use Models\ContentManagement\Post;
use Models\Seo\PagePersister;

class MetaTagsBinding extends PostHelper
{
    protected static $i = NULL;

    private static $update_cache = array();
    /**
     * событие перед изменением
     */
    public function preUpdate(Post $post, &$params, &$errors = NULL){
        if (!empty($params['key']) && $params['key'] != $post['key']){
            self::$update_cache[$post['id']] = $post->getUrl();
        }
        return $post;
    }
    /**
     * событие после изменения Post
     */
    public function onUpdate(Post $post){
        if (!empty(self::$update_cache[$post['id']])){
            PagePersister::getInstance()->updateMetaTagBinding(self::$update_cache[$post['id']], $post->getUrl());
            unset(self::$update_cache[$post['id']]);
        }
        return $post['id'];
    }

}