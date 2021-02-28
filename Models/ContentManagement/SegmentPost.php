<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 10.03.15
 * Time: 16:18
 */

namespace Models\ContentManagement;


class SegmentPost extends Post{
    const TABLE_PAGE_URLS = 'segment_text_url';

    protected static $loadFields = array('id', 'status', 'type', 'title', 'top', 'last_id', 'first_id',
        'comments', 'num', 'annotation', 'theme_id', 'segment_id', 'tags', 'data', 'key', 'page_url_id', 'complete_text', 'site_links_done', 'full_version', 'last_update'
    );
    /** разрешенные параметры для редактирования */
    protected static $updateFields = array(
        'type',    // у поста может быть тип, для того чтобы различать разные типы постов
        'status',  // у постов есть статусы, по умолчанию: new - новый, mod - на модерации, public - опубликован, close - закрыт(комменты писать нельзя), delete - удален
        'title',   // У постов могут быть заголовки
        'top',     // Посты можно прибивать "к верху", для того чтобы менять порядок следования их в выдаче
        'theme_id', // Посты можно разбивать по темам
        'num',       // Позиция поста
        'annotation', //Аннотация к посту
        'segment_id',
        'data',
        'key',
        'page_url_id',
        'tags',        // тэги для поиска
        'complete_text',  // текст поста со вставленными ссылками
        'site_links_done' // флаг о проведенной перелинковке поста
    );

    private $url = NULL;

    /**
     * Переопределяем getUrl, подставляем
     * @param $key
     * @return string
     */
    protected function getData($key){
        if ($key == 'url'){
            if (is_null($this->url)){
                $this->url = $this->db->query('SELECT `url` FROM ?# WHERE `id` = ?d', self::TABLE_PAGE_URLS, $this['page_url_id'])->getCell();
                if (empty($this->url) || strpos($this->url, '*') !== FALSE){
                    // Не отдаем урл, если в нем содержится *, тк невозможно однозначно определить,
                    // на какой странице он размещен
                    $this->url = FALSE;
                }
            }
            return $this->url;
        } else {
            return parent::getData($key);
        }
    }

    public static function getPostByKeyUrlAndSegment($key, $page_url_id, $segment_id){
        $params = array('key'=>$key, 'page_url_id' => $page_url_id);
        if (empty($segment_id)){
            $params['empty_segment_id'] = TRUE;
        } else {
            $params['segment_id'] = $segment_id;
            $params['not_empty_segment_id'] = TRUE;
        }
        $post = static::search($params, $count, 0, 1);
        return reset($post);
    }

    /**
     * Поиск постов к странице по урлу
     * @param string $url
     * @param int $segment_id
     * @param bool $public_only
     * @return array
     */
    public static function getPostsListByPageUrl($url, $segment_id, $public_only = FALSE){
        $db = \App\Builder::getInstance()->getDB();
        $segment_keys = $db->query('SELECT `key` FROM `' . \Models\Segments\Lang::TABLE . '` WHERE 1')->getCol('key', 'key');
        $url = preg_replace('~^\/(' . implode('|', $segment_keys) . ')\/~', '/', $url, 1);
        $urls = array('*', $url);
        $url = explode('?', $url);
        if (count($url) > 1){
            $urls[] = reset($url);
        }
        /*
         * Нам нужен массив из полного урла и подмножество неполных урлов, в которые он входит
         * например
         * /one/two/three/?param=value
         * /one/two/three/
         * /one/two/*
         * /one/*
         * *
         */
        $url = trim(reset($url), '/');
        $url_parts = explode('/', $url);
        array_pop($url_parts); //
        $tmp_url = '';
        foreach($url_parts as $part){
            $tmp_url .= '/'.$part;
            $urls[] = $tmp_url . '/*';
        }
        $url_ids = $db->query('SELECT `id` FROM `' . self::TABLE_PAGE_URLS . '` WHERE `url` IN (?l)', $urls)->getCol('id', 'id');
        $result = array();
        if (!empty($url_ids)){
            $post_ids = $db->query('SELECT `id`, `key`, `status`, `segment_id` FROM (SELECT `p`.`id`, `p`.`key`, `p`.`status`, IF (`p`.`segment_id` IS NOT NULL, `p`.`segment_id`, -1) AS `segment_id`
                FROM `' . self::TABLE_NAME . '` AS `p` INNER JOIN `' . self::TABLE_PAGE_URLS . '` as `u`
                ON `u`.`id` = `p`.`page_url_id` WHERE `u`.`id` IN (?i){ AND ' . (!is_null($segment_id) ? '(`p`.`segment_id` = ?d OR `p`.`segment_id` IS NULL)' : '`p`.`segment_id` IS NULL AND ?d') . '}
                { AND `p`.`status` IN ("public", "close") AND ?d}
                ORDER BY `p`.`key`, CHAR_LENGTH(`u`.`url`) DESC, `segment_id` DESC) AS `tbl` GROUP BY `key`',
                $url_ids,
                \LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE ? ($segment_id ? $segment_id : 1) : $db->skipIt(),
                $public_only ? 1 : $db->skipIt()
                )->getCol('id', 'id');
            if (!empty($post_ids)){
                $posts = self::factory($post_ids);
                foreach($posts as $post){
                    $result[$post['key']] = $post;
                }
            }
        }
        return $result;
    }

    /* ************************************** Урлы страниц ************************************** */

    public static function getPageUrlList(){
        return \App\Builder::getInstance()->getDB()->query('SELECT `id`, `url`, `title`, `position` FROM `' . self::TABLE_PAGE_URLS . '` ORDER BY `position`')->select('id');
    }

    /**
     * @param $id
     * @return array|null
     */
    public static function getPageUrlById($id){
        return \App\Builder::getInstance()->getDB()->query('SELECT `id`, `url`, `title`, `position` FROM `' . self::TABLE_PAGE_URLS . '` WHERE `id` = ?d', $id)->getRow();
    }

    /**
     * @param $id
     * @param $url
     * @param $title
     * @param array $errors
     * @return bool|int
     */
    public static function editPageUrl($id, $url, $title, &$errors = array()){
        $db = \App\Builder::getInstance()->getDB();
        $page_url = !empty($id) ? self::getPageUrlById($id) : NULL;
        if (empty($url) || empty($title)){
            if (empty($url)) $errors['url'] = 'empty';
            if (empty($title)) $errors['title'] = 'empty';
            return FALSE;
        }
        if (!empty($id) && empty($page_url)){
            $errors['id'] = 'not_found';
            return FALSE;
        } elseif ($db->query('SELECT 1 FROM `' . self::TABLE_PAGE_URLS . '` WHERE `url` = ?s{ AND `id` != ?d}', $url, !empty($id) ? $id : $db->skipIt())->getRow()){
            $errors['url'] = 'already_exists';
            return FALSE;
        } else {
            if (empty($id)){
                return $db->query('INSERT INTO `' . self::TABLE_PAGE_URLS . '` SET `url` = ?s, `title` = ?s, `position` = ?d', $url, $title, $db->query('SELECT MAX(`position`) FROM `' . self::TABLE_PAGE_URLS . '` WHERE 1')->getCell() + 1);
            } else {
                return $db->query('UPDATE `' . self::TABLE_PAGE_URLS . '` SET `url` = ?s, `title` = ?s WHERE `id` = ?d', $url, $title, $id);
            }
        }
    }

    /**
     * @param $id
     * @return FALSE|int|\MysqlSimple\Result
     */
    public static function deletePageUrl($id){
        $posts = static::search(array('page_url_id' => $id));
        if (!empty($posts)){
            foreach($posts as $post){
                $post->delete();
            }
        }
        return \App\Builder::getInstance()->getDB()->query('DELETE FROM `' . self::TABLE_PAGE_URLS . '` WHERE `id` = ?d', $id);
    }

    public static function movePageUrl($id, $position, &$errors = array()){
        $page_url = self::getPageUrlById($id);
        if (!empty($page_url)){
            if ($position != $page_url['position']){
                $db = \App\Builder::getInstance()->getDB();
                if ($position > $page_url['position']){
                    $db->query('UPDATE `'.self::TABLE_PAGE_URLS.'` SET `position` = `position` - 1 WHERE `position` > ?d AND `position` <= ?d', $page_url['position'], $position);
                } else {
                    $db->query('UPDATE `'.self::TABLE_PAGE_URLS.'` SET `position` = `position` + 1 WHERE `position` < ?d AND `position` >= ?d', $page_url['position'], $position);
                }
                $db->query('UPDATE `'.self::TABLE_PAGE_URLS.'` SET `position` = ?d WHERE `id` = ?d', $position, $id);
                return TRUE;
            } else {
                $errors['position'] = 'not_changed';
            }
        } else {
            $errors['id'] = 'not_found';
        }
        return false;
    }

} 