<?php
/**
 * Блоки с информацией, которые нужны на страницах
 *
 * @author olga
 */
namespace App;
use Models\AuthenticationManagement\SocialAuth;
use Models\ContentManagement\Post;
use Models\CatalogManagement\Type;
use Models\MenuManagement\MenuItem;

class Blocks {
	const LIGHT_BRADCRUMBS = FALSE;//облегченные крошки (без вложенных типов)
	const LIGHT_LEFT_MENU = FALSE;//левое меню без подтипов
    const NEWS_BLOCK_LENGTH = 2;
	const LENGTH_MANUFS = 100;//количество производителей
//	const ACTUAL_BLOCK_SIZE = 4;
//	const BLOGS_PAGE_COUNT = 6;
	/**
	 *
	 * @var Blocks
	 */
	private static $instance = NULL;
	/**
	 * Список допустимых методов - результат
	 * @var type 
	 */
	private $allow_types = array(
		'post' => NULL,
		'postsByKeys' => NULL,
		'news' => NULL,
		'types' => NULL, 
		'path' => NULL,
        'payments' => NULL,
		'orderStatuses' => NULL,
        'formCreator' => NULL,
        'socialLinks' => NULL,
        'menu' => NULL,
		'favoriteIds' => NULL
	);
	/**
	 * 
	 * @return Blocks
	 */
	public static function getInstance(){
		if (is_null(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}

    /**
     * Точка входа. Вытаскивает данные из запрашиваемого метода
     * @param string $type
     * @param array $data
     * @param int $segment_id
     * @throws \Exception
     * @return type
     */
	public function get($type = NULL, $data = array(), $segment_id = NULL){
		if (is_null($segment_id)){
			//по умолчанию сегмент в публичной части сайта, т.к. идея класса для вывода информации именно на публичные страницы
			$default_segment = \App\Segment::getInstance()->getDefault(true);
			$segment_id = $default_segment['id'];
		}
		if (empty($segment_id)){
			$segment_id = 0;
//			throw new \Exception('Info block предназначен для использования в конкретном сегменте');
		}
		if (is_null($type) || !array_key_exists($type, $this->allow_types)){
			throw new \Exception('Неверно задан параметр $type. Возможные значения: ' . implode(array_keys($this->allow_types)));
		}
		$data_hash = md5(serialize($data));
		if (isset($this->allow_types[$type][$segment_id][$data_hash])){//чтобы верстальщики при запросе одного и того же получали из "кэша"
			return $this->allow_types[$type][$segment_id][$data_hash];
		}
		$method_name = 'get' . ucfirst($type);//@TODO расширить возможности внесения названий методов (составные)
        $this->allow_types[$type][$segment_id][$data_hash] = $this->$method_name($data, $segment_id);
		return $this->allow_types[$type][$segment_id][$data_hash];
	}
	public function getAllowTypes(){
		return array_keys($this->allow_types);
	}
	private function getPost($data, $segment_id){
		$post = NULL;
		if (!empty($data)){
			$post = Post::getByKey($data, $segment_id);
		}
		return $post;
	}
	private function getPostsByKeys($data, $segment_id){
		$params = array(
			'segment_id' => $segment_id,
			'key' => $data
		);
		$return_result = array();
		$posts = Post::search($params);
		foreach ($posts as $p){
			$return_result[$p['key']] = $p;
		}
		return $return_result;
	}
	/**
	 * Новости
	 * @param array $data
	 * @param int $segment_id
	 * @return type
	 */
	private function getNews($data, $segment_id){
		$limit = !empty($data['limit']) ? $data['limit'] : self::NEWS_BLOCK_LENGTH;
		$news_params['segment_id'] = $segment_id;
        $news_params['status'] = array(Post::STATUS_CLOSE, Post::STATUS_PUBLIC);
        $account = \App\Builder::getInstance()->getAccount();
        if ($account->isPermission('news-admin', 'edit')){
            $news_params['status'][] = Post::STATUS_NEW;
        }
        $news_params['type'] = \Modules\Posts\News::POSTS_TYPE;
        return Post::search($news_params, $count, 0, $limit, 'dt');
	}
	/**
	 * Типы для левого меню
	 * @param array $data
	 * @param int $segment_id
	 * @return type
	 */
	private function getTypes($data, $segment_id){
		$main_type_id = $data ? $data : Type::DEFAULT_TYPE_ID;
		$main_type = Type::getById($main_type_id, $segment_id);
        $statuses[] = Type::STATUS_VISIBLE;
        $account = \App\Builder::getInstance()->getAccount();
        if ($account->isPermission('catalog-type', 'updateHidden')){
            $statuses[] = Type::STATUS_HIDDEN;
        }
        $first_children = CatalogMethods::filterNonVisibleTypes($main_type->getChildren());
        $menu = array();
        foreach ($first_children as $f_child){
            $menu[$f_child['id']]['info'] = $f_child;
			if (!self::LIGHT_LEFT_MENU){
				$menu[$f_child['id']]['types'] = CatalogMethods::getTypeChildren($f_child['id']);
			}
        }
        return $menu;
	}
    /**
     * Составляем "хлебные крошки"
     * @param array $data
     * @param int $segment_id
     * @return array
     */
    private function getPath($data, $segment_id){
		$type_id = $data['type_id'] ? $data['type_id'] : Type::DEFAULT_TYPE_ID;
        $statuses = array(Type::STATUS_VISIBLE);
        $account = \App\Builder::getInstance()->getAccount();
		if ($account->isPermission('catalog-type', 'updateHidden')){
			$statuses[] = Type::STATUS_HIDDEN;
		}
        $path = array();
		$item_type = Type::getById($type_id);
		if ($type_id == Type::DEFAULT_TYPE_ID){
			$parents = array($type_id => $item_type);
		}else{
			$parents = $item_type->getParents();
		}
		foreach ($parents as $parent){
			$path[$parent['id']]['title'] = $parent['title'];
			$path[$parent['id']]['entity'] = $parent;
			if (!self::LIGHT_BRADCRUMBS){
				$path[$parent['id']]['children'] = !empty($data['admin_page']) ? $parent->getChildren() : CatalogMethods::filterNonVisibleTypes($parent->getChildren(), TRUE);
			}
		}
		return $path;
    }
    private function getPayments($data, $segment_id){
        return Payments\Pay2Pay::getPayMethods();
    }
	private function getOrderStatuses($data, $segment_id){
		return OrderManagement\Order::getStatuses();
	}
    private function getFormCreator($data, $segment_id){
        return \Models\FormConstruct::getInstance();
    }
    private function getSocialLinks($data, $segment_id){
        return SocialAuth::getSocialLinksList();
    }

    private function getMenu($data, $segment_id){
        return MenuItem::getMenuItemsByKey($data);
    }

	private function getFavoriteIds($data, $segment_id) {
		$account = \App\Builder::getInstance()->getAccount();
		return $account->getFavoriteData($data)['entity_ids'];
	}
}