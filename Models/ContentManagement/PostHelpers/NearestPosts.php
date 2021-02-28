<?php
/**
 * Description of NearestPosts
 *
 * @author charles manson
 */
namespace Models\ContentManagement\PostHelpers;
use Models\ContentManagement\Post;

class NearestPosts extends PostHelper{
    protected static $i = NULL;
    protected static $fieldList = array('next_post', 'prev_post');
    protected $db;
    
	protected function __construct(){
        $this->db = \App\Builder::getInstance()->getDB();
        parent::__construct();
	}
    /**
     * @TODO а если три раза запросят next_post, три запроса будет?
     * возвращает значение дополнительного поля
     */
    function get(Post $post, $field){
        if (!in_array($field, $this->fieldsList())){
            throw new \InvalidArgumentException('Неверное название дополнительного поля');
        }
        if ($field == 'next_post'){
            $query = 'SELECT `c`.`post_id` AS `id` FROM `comments` AS `c` INNER JOIN `posts` ON `c`.`post_id` = `posts`.`id` WHERE `posts`.`status` IN (\'public\', \'close\') AND `c`.`pub_date` < ?s AND `posts`.`type` = ?s AND `posts`.`segment_id` = ?d ORDER BY `pub_date` DESC LIMIT 1';
        } else {
            $query = 'SELECT `post_id` AS `id` FROM `comments` WHERE `dt` > ?s ORDER BY `dt` ASC LIMIT 1';
            $query = 'SELECT `c`.`post_id` AS `id` FROM `comments` AS `c` INNER JOIN `posts` ON `c`.`post_id` = `posts`.`id` WHERE `posts`.`status` IN (\'public\', \'close\') AND `c`.`pub_date` > ?s AND `posts`.`type` = ?s AND `posts`.`segment_id` = ?d ORDER BY `pub_date` ASC LIMIT 1';
        }
        $id = $this->db->query($query, $post['pub_date'], $post['type'], $post['segment_id'])->getCell();
        return Post::getById($id);
    }
}
