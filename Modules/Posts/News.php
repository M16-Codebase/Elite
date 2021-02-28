<?php
/**
 * Description of News
 *
 * @author olga
 */
namespace Modules\Posts;
use App\Configs\SphinxConfig;
use Models\ContentManagement\Post;
use Models\SphinxManagement\SphinxSearch;

class News extends View{
    const POSTS_TYPE = 'news';
    /**
     * Вычисляет название метода из маршрута
     *
     * @param string $route
     * @return string
     */
    protected function init(){
        parent::init();
        $segment = \App\Segment::getInstance()->getDefault(true);
        $status = array(Post::STATUS_CLOSE, Post::STATUS_PUBLIC);
        $years = $this->db->query('
            SELECT DATE_FORMAT(`c`.`pub_date`, "%Y") AS `year` FROM `comments` AS `c`
            INNER JOIN `posts` AS `p` ON (`c`.`id` = `p`.`first_id` AND `p`.`type` = ? AND `p`.`status` IN (?l) AND `p`.`segment_id` = ?d)
            GROUP BY `year`
            ORDER BY `year` DESC
        ', static::POSTS_TYPE, $status, $segment['id'])->getCol('year', 'year');
        $this->getAns()->add('years', $years);
        
    }
    public function route($route){
        $routeTokens = explode('/', $route, 2);
        if (preg_match('~^[\d]{4}$~', $routeTokens[0])){
            $action = 'index';
            $this->routeTail = $routeTokens[0];
        }else{
            $action = parent::route($route);
        }
        return $action;
    }
    public function index(){
		$segment = \App\Segment::getInstance()->getDefault(true);
        $status = array(Post::STATUS_CLOSE, Post::STATUS_PUBLIC);
        $page = $this->request->query->get('page', 1);
        if ($this->account instanceof \App\Auth\Account\Admin){
            $status[] = Post::STATUS_NEW;
        }
        
        $req_year = NULL;
        if (!empty($this->routeTail)){
            $route = explode('/', $this->routeTail);
            if (!empty($route[0]) && preg_match('~[\d]{4}~', $route[0])){
                $req_year = $route[0];
            }
        }
        $search_params = array(
			'type'=>static::POSTS_TYPE, 
			'status'=> $status, 
			'segment_id' => $segment['id']
		);
        if ($req_year){
            $search_params['from_pub_date'] = strtotime('01.01.' . $req_year . ' 00:00:01');
            $search_params['to_pub_date'] = strtotime('31.12.' . $req_year . ' 23:59:59');
        }
        $offset = ($page == 1) ? 0 : ($page-1) * static::PAGE_SIZE - 1;
        $limit = ($page == 1) ? static::PAGE_SIZE - 1 : static::PAGE_SIZE;
        $search = $this->request->query->get('s');
        if (!empty($search)){
            $sphinx = SphinxSearch::factory(SphinxConfig::POSTS_KEY);
            $ids = $sphinx->setLimit(0, 1000000000)->select('id', $search)->getCol('id', 'id');
            $search_params['id'] = $ids;
            $search_params['order_by_ids'] = $ids;
        }
        $posts = Post::search(
            $search_params, 
			$count, 
			$offset, 
			$limit, 
			'pub_date');
        if (!empty($posts) && !empty($sphinx)){
            $docs = array();
            foreach($posts as $id=>$p){
                $docs[$id] = $p['text'];
            }
            $this->getAns()->add('search_matches', $sphinx->getMatchBlocks($docs, $search));
        }
        $this->getAns()->add('posts', $posts)
            ->add('req_year', $req_year)
            ->add('count', $count+1)
            ->add('pageNum', $page)
            ->add('pageSize', static::PAGE_SIZE);
    }
    
    public function post(){
        $params['status'] = array(Post::STATUS_CLOSE, Post::STATUS_PUBLIC);
        if ($this->account instanceof AccountAdmin){
            $params['status'][] = Post::STATUS_NEW;
        }
        $params['type'] = static::POSTS_TYPE;
        if (\LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE){
            $params['segment_id'] = \App\Segment::getInstance()->getDefault(true)['id'];
        }
        \Models\ContentManagement\PostHelpers\Images::factory();
        $news = Post::search($params, $count, 0, 5, 'dt');
        $this->getAns()->add('last_news', $news);
        return parent::post();
    }
}

?>