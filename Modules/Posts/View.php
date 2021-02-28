<?php
/**
 * Description of View
 *
 * @author olga
 */
namespace Modules\Posts;
use App\Configs\SphinxConfig;
use Models\ContentManagement\Post;
use App\Auth\Account\Admin;
use Models\ContentManagement\PostHelpers\Images;
use Models\SphinxManagement\SphinxSearch;

class View extends \LPS\WebModule{
    const POSTS_TYPE = 'disabled';
    const PAGE_SIZE = 12;

	protected function init(){
        parent::init();
        $this->protectedFunctionsList['searchposts'] = true;
    }
    public function route($route) {
        $action = parent::route($route);
        if (preg_match('~\-(\d+)$~', $route, $regs)){
            if (empty($this->routeTail)){
                $this->routeTail = $action;
            }
            $this->request->query->set('id', intval($regs[1]));
            $action = 'post';
        }
        return $action;
    }
    public function index(){
        $status = array(Post::STATUS_CLOSE, Post::STATUS_PUBLIC);
        if ($this->account->isPermission('service-admin', 'edit')){
            $status[] = Post::STATUS_NEW;
        }
        $segment = \App\Segment::getInstance()->getDefault(true);
        Images::factory();
        $search_params = array('type'=>static::POSTS_TYPE, 'status'=> $status, 'segment_id' => $segment['id']);
        $search = $this->request->query->get('s');
        $page = $this->request->query->get('page', 1);
        if (!empty($search)){
            $sphinx = SphinxSearch::factory(SphinxConfig::POSTS_KEY);
            $ids = $sphinx->setLimit(0, 1000000000)->select('id', $search);
            $search_params['id'] = $ids;
            $search_params['order_by_ids'] = $ids;
        }
        $posts = Post::search($search_params, $count, ($page-1) * static::PAGE_SIZE, static::PAGE_SIZE, 'num');
        if (!empty($posts) && !empty($sphinx)){
            $docs = array();
            foreach($posts as $id=>$p){
                $docs[$id] = $p['text'];
            }
            $this->getAns()->add('search_matches', $sphinx->getMatchBlocks($docs, $search));
        }
        $this->getAns()
            ->add('posts', $posts)
            ->add('count', $count)
            ->add('pageNum', $page)
            ->add('pageSize', static::PAGE_SIZE);
    }
    public function post(){
        $status = array(Post::STATUS_CLOSE, Post::STATUS_PUBLIC);
        if ($this->account->isPermission('service-admin', 'edit')){
            $status[] = Post::STATUS_NEW;
        }
        $post = Post::getById($this->request->query->get('id'));
        $segment = \App\Segment::getInstance()->getDefault(true);
        $url_prefix = $segment->getUrlPrefix();
        $post_url = $post->getUrl($post['segment_id']);
        if (empty($post_url)) {
            return $this->notFound();
        } elseif ($post_url != $url_prefix . $this->getModuleUrl() . $this->routeTail . '/'){
            return $this->redirect($post_url);
        }
        if (empty($post) || !in_array($post['status'], $status)){
            return $this->notFound();
        }
        $this->getAns()->add('post', $post);
//            ->add('next_post', $post['next_post'])
//            ->add('prev_post', $post['prev_post']);
    }
    /**
     * @ajax
     */
    public function getPostText(){
        $post_id = $this->request->request->get('post_id');
        if (!empty($post_id)){
            $post = Post::getById($post_id);
            return empty($post) ? '' : $post['text'];
        }else{
            return '';
        }
    }

    /**
     * Поиск постов через сфинкс
     * @param string $phrase — поисковая фраза
     * @param int $page
     * @param int $page_size
     * @param int|bool $count
     * @param string|string[] $post_types — тип или список типов постов
     * @return \Models\ContentManagement\Post[]
     */
    public function searchPosts($phrase, $page = 1, $page_size = 20, &$count = false, $post_types = null){
        if (!SphinxConfig::ENABLE_SPHINX) {
            throw new \LogicException('SphinxSearch отключен');
        }
        if (empty($post_types)) {
            $post_types = static::POSTS_TYPE;
        }
        $sphinx = SphinxSearch::factory(SphinxConfig::POSTS_KEY, $this->segment['id']);
        $sphinx->setLimit(0, 1000000)->setGroup(null);
        $ids = $sphinx->select('`id`', $phrase)->getCol('id', 'id');
        if (empty($ids)) {
            $count = 0;
            return array();
        }
        $posts = Post::search(
            array(
                'id' => $ids,
                'order_by_ids' => $ids,
                'type' => $post_types,
                'status' => array(Post::STATUS_PUBLIC, Post::STATUS_CLOSE),
                'segment_id' => $this->segment['id']
            ),
            $count, ($page - 1) * $page_size, $page_size);
        return $posts;
    }
}