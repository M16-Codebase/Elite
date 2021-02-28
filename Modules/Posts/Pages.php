<?php
namespace Modules\Posts;
use Models\ContentManagement\Post;
use Models\ImageManagement\TmpCollection;

class Pages extends \LPS\AdminModule{
    const POSTS_TYPE = 'pages';
    const MIN_TITLE_CHARS = 3;
    const MIN_TEXT_CHARS = 10;
    const PAGE_SIZE = 30;
    const SORT_MODE = 'num';

    protected function init(){
        $this->loadStatusList();
    }
    protected function loadStatusList(){
        $status_list = Post::getPostStatusList();
        //Внутри функции статус опубликован меняется на статус закрыт (а названия для удобства интерфейса меняются в обратную сторону), т.к. для статических страниц не предусмотренно комментирование
        $status_list[Post::STATUS_CLOSE] = $status_list[Post::STATUS_PUBLIC];
        unset($status_list[Post::STATUS_PUBLIC]);
        $this->getAns()->add('status_list', $status_list);
    }

    /**
     * @param Post $post
     * @return array
     */
    protected function getPostFormData(Post $post){
        return $post->asArray();
    }
    public function index(){
        if (\LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE){
            if ($this->request->query->has('s')){
                $s_id = $this->request->query->get('s');
                $segment = \App\Segment::getInstance()->getById($s_id);
                if (empty($segment) && (!empty($s_id) || \LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE)) {
                    return $this->notFound();
                }
            }
        }
        $this->postsList(true);
    }

    public function postsList($inner = false){
        $ans = $inner ? $this->getAns() : $this->setJsonAns();
        $params = array('type' => static::POSTS_TYPE);
        $theme_id = $this->request->request->get('theme_id', $this->request->query->get('theme_id'));
        if (!empty($theme_id)){
            $params['theme_id'] = $theme_id;
        }
        if (\LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE){
            $segment_id = $this->request->request->get('s', $this->request->query->get('s'));
            if (empty($segment_id)){
                $params['empty_segment_id'] = TRUE;
            } else {
                $params['segment_id'] = $segment_id;
                $params['not_empty_segment_id'] = TRUE;
            }
        }
        $page = $this->request->request->get('page', $this->request->query->get('page', 1));
        $params['status'] = array(Post::STATUS_CLOSE, Post::STATUS_NEW, Post::STATUS_PUBLIC, Post::STATUS_HIDDEN);
        $posts = Post::search($params, $count, ($page-1)*static::PAGE_SIZE, static::PAGE_SIZE, static::SORT_MODE);
        $this->getAns()->add('posts', $posts)
            ->add('posts_count', $count)
            ->add('count', $count)
            ->add('pageNum', $page)
            ->add('pageSize', static::PAGE_SIZE);
        if ($redirect = $this->request->request->get('redirect')){
            $ans->addData('url', $redirect);
        }
    }

    public function createPostFields(){
        $this->setJsonAns();
        if (\LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE) {
            $segment_id = $this->request->request->get('segment_id');
            if (!empty($segment_id)) {
                $this->getAns()
                    ->setFormData(array(
                        'segment_id' => $segment_id
                    ));
            }
        }
    }

    public function edit(){
        $result = $this->editPost(TRUE);
        if (!is_null($result)){
            return $result;
        }
    }
    /**
     * @ajax редактирование
     * @param bool $inner
     * @return void|\Symfony\Component\HttpFoundation\Response
     */
    public function editPost($inner = FALSE){
        if (!$inner){
            $this->setJsonAns();
        }
        $post_id = $this->request->query->get('id', $this->request->request->get('id'));
        $post_creation_hash = $this->request->request->get('post_creation_hash');
        if (!empty($post_creation_hash)){
            $params = Post::validateFormData($this->request->request->all(), $errors);
            if (!empty($params) && empty($errors)){
                $theme_id = $this->request->request->get('theme_id', NULL);
                $theme_id = !empty($theme_id) ? $theme_id : NULL;
                $post_id = Post::create(static::POSTS_TYPE, $theme_id);
                $postData = Post::getById($post_id);
                $postData->edit(array(
                    'type'=>static::POSTS_TYPE,
                    'segment_id' => $this->request->request->get('segment_id', NULL)
                ));
                $postData->changePosition(1);
                $gallery_data = json_decode($this->request->request->get('gallery_data'), true);
                $gallery = TmpCollection::getGallery($post_creation_hash, !empty($gallery_data) ? $gallery_data : array());
                if (!empty($gallery)){
                    $url_replace = $gallery->importToCollection($postData['gallery']);
                    $post_body = $this->request->request->get('text');
                    if (!empty($url_replace['from']) && !empty($post_body)){
                        $this->request->request->set('text', str_replace($url_replace['from'], $url_replace['to'], $post_body));
                    }

                    $gallery->deleteGallery();
                }
            } elseif (!empty($errors)){
                if (!$inner){
                    $this->getAns()->setEmptyContent()->setErrors($errors);
                }
            }
        } else {
            $postData = Post::getById($post_id);
        }
        if (empty($postData)) {
            if ($inner){
                return $this->notFound();
            } else {
                $this->getAns()->setEmptyContent();
                return;
            }
        }
//        $send_form = $this->additionalPostPrepareParams($this->request->request->all());
        $send_form = $this->request->request->all();
        unset($send_form['post_creation_hash']);
        if (!empty($send_form)) {
            if (empty($send_form['segment_id'])) {
                $send_form['segment_id'] = NULL;
            }
            $postData->edit($send_form, $errors);
            if (empty($errors)){
                if (!$inner){
                    $this->getAns()->setStatus('ok');
                    Post::clearRegistry($postData['id']);
                    $postData = Post::getById($postData['id']);
                    if (!empty($post_creation_hash)){
                        $this->getAns()
                            ->addData('url', $this->getModuleUrl() . 'edit/?id=' . $postData['id']);
                    } else {
                        $this->getAns()->addData('last_update', $postData['last_update']);
                    }
                }else{
                    return $this->redirect($this->getRedirectUrl('edit'), $postData);
                }
            } else {
                if (!$inner){
                    $this->getAns()->setErrors($errors);
                }else{
                    foreach($errors as $key => $value){
                        $this->getAns()
                            ->add('errors', $errors)
                            ->add('save_error_field', $key);
                        break;
                    }
                }
            }
        }
        $this->loadStatusList();
        $this->getAns()
            ->add('post', $postData)
            ->setFormData($this->getPostFormData($postData));
    }

    /**
     * Дополнительная обработак входных параметров, нужно для кастомизации редактирования в других типах
     * @param array $params
     * @return array
     */
    protected function additionalPostPrepareParams($params){
        return $params;
    }

    public function del(){
        $id = $this->request->request->get('id');
        $errors = array();
        $redirect = NULL;
        if (!empty($id)){
            $post=Post::getById($id);
            if (!empty($post)){
                $this->request->request->set('theme', $post['theme_id']);
                $redirect = $this->getRedirectUrl('del', $post);
                $post->delete();
            } else {
                $errors['post'] = 'not_found';
            }
        } else {
            $errors['id'] = 'empty';
        }
        if (!empty($errors)){
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            return $this->getPostDeleteAns($redirect);
        }
    }

    /**
     * @param string $action
     * @param Post|\Models\ContentManagement\SegmentPost|null $post
     * @return string
     */
    protected function getRedirectUrl($action, $post = NULL){
        $segment_suffix = (\LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE && !empty($post) && !empty($post['segment_id']) ? 's='.$post['segment_id'] : '');
        if ($action == 'edit'){
            return $this->getModuleUrl().'edit/?id=' . $this->request->query->get('id', $this->request->request->get('id', 0)) . (!empty($segment_suffix) ? '&'.$segment_suffix : '');
        }
        return $this->getModuleUrl() . (!empty($segment_suffix) ? '?'.$segment_suffix : '');
    }

    /**
     * В текстах к страницам список постов находится не в index, поэтому нужна промежуточная функция
     * @param string $redirect
     * @return mixed
     */
    protected function getPostDeleteAns($redirect = NULL){
        $this->request->request->set('redirect', $redirect);
        return $this->run('postsList');
    }
    /**
     * @ajax
     */
    public function movePost(){
        $post_id = $this->request->request->get('id', $this->request->query->get('id'));
        $position = $this->request->request->get('position', $this->request->query->get('position'));
        if (!empty($position) && !empty($post_id)){
            $post = Post::getById($post_id);
            $post->changePosition($position);
            $this->request->request->set('type', $post['type']);
            $this->request->request->set('theme', $post['theme_id']);
            Post::clearRegistry();
            return $this->run('postsList');
        } else {
            $ans = $this->setJsonAns()->setEmptyContent();
            if (empty($position)){
                $ans->addErrorByKey('position', 'empty');
            }
            if (empty($post_id)){
                $ans->addErrorByKey('post_id', 'empty');
            }
        }
    }

    public function metaTags(){

    }
}
?>