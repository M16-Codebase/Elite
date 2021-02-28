<?php
/**
 * Description of AdminArticles
 *
 * @author olga
 */
namespace Modules\Posts;
use Models\ContentManagement\Post;

class AdminBlog extends Pages{
    const POSTS_TYPE = 'blog';
    const SORT_MODE = 'pub_date';
    protected function loadStatusList(){
        $this->getAns()->add('status_list', Post::getPostStatusList());
    }
    /**
     * @param Post $post
     * @return array
     */
    protected function getPostFormData(Post $post){
        $form_data = $post->asArray();
        $form_data['tags'] = implode(', ', $post['tag_list']);
        return $form_data;
    }
}
