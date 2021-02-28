<?php
/**
 * Description of Articles
 *
 * @author olga
 */
namespace Modules\Posts;
use Models\InternalLinkManager AS ILM;
use Models\CatalogManagement\Type;
use Models\ContentManagement\Post as Post;
use Models\ContentManagement\Comment as Comment;
use Models\ContentManagement\PostHelpers\Images;
class Blog extends View {
    const POSTS_TYPE = 'blog';
    const PAGE_SIZE = 10;

    public function index(){
        $tag = $this->request->query->get('tag');
        $page = $this->request->query->get('page', 1);
        $status = array(Post::STATUS_CLOSE, Post::STATUS_PUBLIC);
        if ($this->account->isPermission('blog-admin', 'edit')){
            $status[] = Post::STATUS_NEW;
        }
        $segment = \App\Segment::getInstance()->getDefault(true);
        Images::factory();
        $search_params = array('type'=>static::POSTS_TYPE, 'status'=> $status, 'segment_id' => $segment['id']);
        if (!is_null($tag)){
            $search_params['tag'] = $tag;
        }
        if (is_null($this->account->getUser())){
            $search_params['public_blog'] = true;
        }
        $posts = Post::search($search_params, $count, ($page-1) * static::PAGE_SIZE, static::PAGE_SIZE, 'pub_date');
        $this->getAns()
            ->add('posts', $posts)
            ->add('count', $count)
            ->add('pageNum', $page)
            ->add('arrows_only', true)
            ->add('pageSize', static::PAGE_SIZE)
				/*->add('news', \Modules\Main\View::getNews())*/;
    }
    
	public function post(){
        \Models\ContentManagement\PostHelpers\Images::factory();
        $session = \App\Builder::getInstance()->getCurrentSession();
		$post = \Models\ContentManagement\Post::getById($this->request->query->get('id'));
        if (is_null($this->account->getUser()) && $post['pub_timestamp'] > time()){
            return $this->notFound();
        }
		$l = ILM::getInstance();
		$types = array();
        $posts = array();
        $user = $this->account->getUser();
        $delete_btn = $this->account->isPermission('blog', 'removeComment');
        $this->getAns()->add('types', $types)
                ->add('posts', $posts)
                ->add('comments', $post->getComments())
                ->add('user', NULL)//is_null($user) ? NULL : $user->getStaff())
                ->add('comment_author', $session->get('author'))
                ->add('email', $session->get('email'))
                ->add('delete_btn', $delete_btn)
				/*->add('news', \Modules\Main\View::getNews())*/;
		return parent::post();
	}
    
    public function addComment(){
        $segment = \App\Segment::getInstance()->getDefault(true);
        $session = \App\Builder::getInstance()->getCurrentSession();
        $ans = $this->setJsonAns();
        $errors = array();
        $post = Post::getById($this->request->request->get('id'));
        $author = array();
        $user = $this->account->getUser();
        $staff = NULL;
        if (!is_null($user)) {
            $author['user_id'] = $user->getId();
            $staff = $user->getStaff();
        }
        if (is_null($staff)) {
            \Models\Validator::getInstance($this->request)
                ->checkFewResponseValues(
                    array(
                        'author' => array('type' => 'checkEmpty'),
                        'email' => array('type' => 'checkEmail', 'options' => array('empty' => true))
                    ), $errors);
            $author['author'] = $this->request->request->get('author');
            $author['email'] = $this->request->request->get('email');
            if (empty($errors)){
                $session->set('author', $this->request->request->get('author'));
                $session->set('email', $this->request->request->get('email'));
            }
        }
        
        $comment = $this->request->request->get('comment');
        if (is_null($post)){
            $errors[] = 'Пост не найден';
        } elseif($post['status'] != Post::STATUS_PUBLIC){
            $errors[] = 'Комментирование запрещено';
        } elseif(empty($comment)){
            $errors[] = 'Пустой комментарий';
        }
        
        if (empty($errors)){
            $post->addComment($comment, $author);
            $ans->addData('status', 'ok')
                ->add('post', $post)
                ->add('comments', $post->getComments())
                ->add('request_segment', $segment);
        } else {
            $ans->setEmptyContent()->setErrors($errors);
        }
    }
    
    public function removeComment(){
        $segment = \App\Segment::getInstance()->getDefault(true);
        $ans = $this->setJsonAns();
        $errors = array();
        $id = $this->request->request->get('id');
        $comment = Comment::getById($id);
        if (!$this->account->isPermission('blog', 'removeComment')){
            $errors[] = 'Ошибка доступа';
        } elseif (empty($comment)) {
            $errors[] = 'Комментарий не найден';
        } else {
            $post = Post::getById($comment['post_id']);
            if ($comment['id'] == $post['first_id']){
                $errors[] = 'Невозможно удалить тело поста';
            }
        }
        if (empty($errors)){
            $comment->delete();
            $comments = $post->getComments();
            if (isset($comments[$id])){
                unset($comments[$id]);
            }
            $ans->addData('status', 'ok')
                ->add('post', $post)
                ->add('comments', $comments)
                ->add('request_segment', $segment);
        } else {
            $ans->setEmptyContent()->setErrors($errors);
        }
    }
}

