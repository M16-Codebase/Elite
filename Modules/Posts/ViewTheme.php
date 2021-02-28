<?php
/**
 * Description of View
 *
 * @author olga
 */
namespace Modules\Posts;
use Models\ContentManagement\Post;
use App\Auth\Account\Admin;
use Models\ContentManagement\Theme;

class ViewTheme extends View{
    const POSTS_TYPE = 'pages';
	const PAGE_SIZE = 10;
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
    public function index() {
		$default_segment = \App\Segment::getInstance()->getDefault(true);
        $themes = \Models\ContentManagement\Theme::getInstance()->search(array(
            'segment_id' => $default_segment['id'],
            'type' => static::POSTS_TYPE,
            'post_type' => static::POSTS_TYPE,
            'status' => array(Post::STATUS_PUBLIC, Post::STATUS_CLOSE),
            'only_filled' => TRUE
        ));
//            $this->db->query('SELECT `t`.*, COUNT(`p`.`id`) AS `count` '
//			. 'FROM `themes` AS `t` '
//			. 'INNER JOIN `posts` AS `p` ON '
//			. '(`p`.`theme_id` = `t`.`id` AND `p`.`status` IN (?l) AND `p`.`type` = ?s AND '
//			. '(`p`.`segment_id` IS NULL OR `p`.`segment_id` = ?d)) '
//			. 'GROUP BY `t`.`id`',
//			array(\Models\ContentManagement\Post::STATUS_PUBLIC,
//				\Models\ContentManagement\Post::STATUS_CLOSE),
//			static::POSTS_TYPE,
//			$default_segment['id']
//		)->select('id');
		$this->getAns()->add('themes', $themes);
	}

    public function section(){
		$default_segment = \App\Segment::getInstance()->getDefault(true);
        $theme_id = $this->request->query->get('theme');
        if (empty($theme_id)){
            return $this->redirect($this->getModuleUrl());
		}
        $page = $this->request->query->get('page', 1);
        if ($page < 1){
            return $this->redirect($this->getModuleUrl());
        }
        $status = array(Post::STATUS_PUBLIC, Post::STATUS_CLOSE);
        if ($this->account->isPermission(static::POSTS_TYPE . '-admin', 'edit')){
            $status[] = Post::STATUS_NEW;
        }
        $posts = Post::search(array('type'=>static::POSTS_TYPE, 'theme_id'=>$theme_id, 'status'=>$status, 'segment_id' => $default_segment['id']), $count, ($page-1)*static::PAGE_SIZE, static::PAGE_SIZE, 'num');
		if (empty($posts)){
			return $this->redirect($this->getModuleUrl());
		}
        $this->getAns()->add('posts', $posts)
                ->add('count', $count)
                ->add('current_theme_id', $theme_id)
                ->add('current_theme', $this->db->query('SELECT * FROM `themes` WHERE `id` = ?d', $theme_id)->getRow())
                ->add('pageSize', static::PAGE_SIZE)
                ->add('pageNum', $page);
    }
    public function post(){
        $post = Post::getById($this->request->query->get('id'));
        if (empty($post)){
            return $this->notFound();
        }
        $this->getAns()
            ->add('current_theme', Theme::getInstance()->getById($post['theme_id'], $post['segment_id']));
        return parent::post();
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
}

?>