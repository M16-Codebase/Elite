<?php
/**
 * Региональные тексты к страницам
 *
 * @author pochepochka
 */
namespace Modules\Segment;
use App\Configs\PostConfig;
use Models\ContentManagement\SegmentPost;
use MysqlSimple\Exceptions\Exception;

class Text extends \Modules\Posts\Pages{
    // Допуск только разработчикам и сеошникам
    const DEFAULT_ACCESS = \App\Configs\AccessConfig::ACCESS_DISALLOW_ALL;
    protected static $module_custom_permissions = array(
        \App\Configs\AccessConfig::ROLE_SUPER_ADMIN => true,
        \App\Configs\AccessConfig::ROLE_SEO_ADMIN => true
    );
	const MIN_TITLE_CHARS = 0;
	public function index(){
        $this->urlList(TRUE);
	}

    public function urlList($inner = false){
        $ans = $inner ? $this->getAns() : $this->setJsonAns();
        $ans->add('urls', SegmentPost::getPageUrlList());
    }

    public function editUrlFields(){
        $id = $this->request->request->get('id');
        $url_data = !empty($id) ? SegmentPost::getPageUrlById($id) : NULL;
        if (empty($url_data)){
            $this->setJsonAns()->setEmptyContent()->addErrorByKey('id', empty($id) ? 'empty' : 'not_found');
        } else {
            $this->setJsonAns()->setFormData($url_data);
        }
    }

    public function editPageUrl(){
        if (SegmentPost::editPageUrl(
            $this->request->request->get('id'),
            $this->request->request->get('url'),
            $this->request->request->get('title'),
            $errors
        )){
            return $this->run('urlList');
        } else {
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        }
    }

    public function movePageUrl(){
        $errors = array();
        $id = $this->request->request->get('id');
        $position = $this->request->request->get('position');
        if (empty($id)){
            $errors['id'] = 'empty';
        }
        if (empty($position)){
            $errors['position'] = 'empty';
        }
        if (empty($errors)){
            SegmentPost::movePageUrl($id, $position, $errors);
        }
        if (!empty($errors)){
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            return $this->run('urlList');
        }
    }

    public function deletePageUrl(){
        if (SegmentPost::deletePageUrl($this->request->request->get('id'))){
            return $this->run('urlList');
        } else {
            $this->setJsonAns()->setEmptyContent();
        }
    }

    public function urlSection(){
        if ($redirect = $this->request->request->get('redirect')){
            $this->setJsonAns()->addData('url', $redirect);
        }
        $url_data = SegmentPost::getPageUrlById($this->request->request->get('section_id', $this->request->query->get('id')));
        if (empty($url_data)){
            return $this->notFound();
        }
        $params = array('page_url_id' => $url_data['id']);
        if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_NONE){
            $params['segment_id'] = 0;
        }
        $posts = SegmentPost::search($params, $count, 0, 100000);
        $posts_result = array();
        if (!empty($posts)){
            foreach($posts as $post){
                if (empty($post['key'])){
                    continue;
                }
                $key = $post['key'];
                if (empty($posts_result[$key])){
                    $posts_result[$key] = array(
                        'title' => '',
                        'posts' => array()
                    );
                }
                $posts_result[$key]['posts'][!empty($post['segment_id']) ? $post['segment_id'] : 0] = $post;
                if (empty($posts_result[$key]['title']) && is_null($post['segment_id'])) {
                    $posts_result[$key]['title'] = $post['title'];
                }
            }
        }
        $this->getAns()
            ->add('url_data', $url_data)
            ->add('posts', $posts_result);
    }
	
	public function create(){
		$segment_id = NULL;
        $ans = $this->setJsonAns()->setEmptyContent();
        $errors = array();
        if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_NONE){
            $segment = \App\Segment::getInstance()->getDefault();
            $segment_id = $segment['id'];
        } else {
            $segment_id = $this->request->request->get('segment_id');
            if (!empty($segment_id)){
                $segment = \App\Segment::getInstance()->getById($segment_id);
                if (empty($segment)){
                    $errors['segment_id'] = 'not_found';
                }
            } else {
                $segment_id = NULL;
            }
        }
        $page_url_id = $this->request->request->get('page_url_id');
        if (empty($page_url_id) || !SegmentPost::getPageUrlById($page_url_id)){
            $errors['page_url_id'] = empty($page_url_id) ? 'empty' : 'not_found';
        }
        $key = $this->request->request->get('key');
        if (empty($key)){
            $errors['key'] = 'empty';
        }
        $post = SegmentPost::getPostByKeyUrlAndSegment($key, $page_url_id, $segment_id);
        if (!empty($post)){
            $ans->addData('url', $this->getModuleUrl() . 'edit/?id=' . $post['id']);
        } else {
            if (!empty($segment_id)){
                $parent_post = SegmentPost::getPostByKeyUrlAndSegment($key, $page_url_id, NULL);
            }
            $title = $this->request->request->get('title');
            if (empty($parent_post) && empty($title)){
                $errors['title'] = 'empty';
            }
            if (empty($errors)){
                $post_id = SegmentPost::create(static::POSTS_TYPE, NULL, $this->request->request->get('full_version', 1));
                if (empty($post_id)){
                    throw new Exception('Не создаётся пост!');
                }
                $post = SegmentPost::getById($post_id);
                $post->edit(array(
                    'page_url_id' => $page_url_id,
                    'segment_id' => $segment_id,
                    'key' => $key,
                    'title' => empty($title) && !empty($parent_post) ? $parent_post['title'] : $title,
                    'text' => !empty($parent_post) ? $parent_post['text'] : ''
                ), $errors);
                $ans->addData('url', $this->getModuleUrl() . 'edit/?id=' . $post['id']);
            }
        }
        if (!empty($errors)){
            $ans->setErrors($errors);
        }
	}

    public function edit(){
        $post = SegmentPost::getById($this->request->query->get('id', $this->request->request->get('id')));
        if (!empty($post)){
            $used_segments = $this->db->query('SELECT `segment_id` FROM `' . SegmentPost::TABLE_NAME . '` WHERE `key` = ?s AND `page_url_id` = ?d', $post['key'], $post['page_url_id'])->getCol(NULL, 'segment_id');
            $this->getAns()
                ->add('url_data', SegmentPost::getPageUrlById($post['page_url_id']))
                ->add('used_segments', $used_segments);
        }
        return parent::edit();// $this->editPost($post);
    }

    /**
     * @param string $action
     * @param \Models\ContentManagement\Post|SegmentPost|null $post
     * @return string
     */
    protected function getRedirectUrl($action, $post = NULL){
        if ($action == 'del' && !empty($post)){
            return $this->getModuleUrl().'urlSection/?id=' . $post['page_url_id'];
        }
        return parent::getRedirectUrl($action, $post);
    }
    protected function getPostDeleteAns($redirect = NULL){
        $this->request->request->set('redirect', $redirect);
        return $this->run('urlSection');
    }

    public function del(){
        $id = $this->request->request->get('id');
        if (!empty($id)) {
            $post = SegmentPost::getById($id);
            if (!empty($post)){
                $this->request->request->set('section_id', $post['page_url_id']);
            }
        }
        return parent::del();
    }
}
