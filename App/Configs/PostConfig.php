<?php
/**
 * Конфиг для постов
 *
 * @author poche_000
 */
namespace App\Configs;
use Models\ContentManagement\Post;
class PostConfig {
	public static $post_types = array(
//		'news' => 'Новости',
		'article' => 'Статьи'
	);
/* *********** тексты к страницам ******************/

	const PAGE_POST_MAIN = 'main_page';
	const PAGE_POST_PAYMENT = 'payment';
	const PAGE_POST_DELIVERY = 'delivery';
        
    public static $page_posts = array(
        self::PAGE_POST_DELIVERY => 'Доставка',
        self::PAGE_POST_PAYMENT => 'Оплата'
    );
	
	public static $page_region_posts = array(
		self::PAGE_POST_MAIN => 'На главной'
	);

    /**
     * @param Post $post
     * @param int|null $segment_id
     * @return null|string
     */
	public static function getUrl(Post $post, $segment_id = null){
        if (!empty($post['segment_id']) && $post['segment_id'] != $segment_id) {
            return null;
        }
        if (empty($segment_id)) {
            $segment = \App\Segment::getInstance()->getDefault(true);
        } else {
            $segment = \App\Segment::getInstance()->getById($segment_id);
        }
		return $segment->getUrlPrefix() . $post['raw_url'];
	}

    private static $fields = array(
        'title' => 'Название',
        'key' => 'Ключ',
        'annotation' => 'Аннотация',
        'position' => 'Позиция',
        'status' => 'Статус',
        'text' => 'Текст',
        'segment_id' => 'Сегмент',
        'theme_id' => 'Тема',
        'tags' => 'Тэги'
    );
    public static function getFields(){
        return self::$fields;
    }

    private static $catalog_allow_post_types = array(
    );

    /**
     * @return string[]
     */
    public static function getAllowPropertyPosts() {
        return self::$catalog_allow_post_types;
    }
}
