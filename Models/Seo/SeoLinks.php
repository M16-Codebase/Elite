<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 19.11.14
 * Time: 13:46
 */

namespace Models\Seo;


use App\Configs\SphinxConfig;
use Models\ContentManagement\Post;
use Models\SphinxManagement\SphinxSearch;

class SeoLinks {
    const TABLE = 'seo_links';
    const INSERTED_LINKS_LIST_TABLE = 'seo_links_inserted';

    private static $table_fields = array('id', 'phrase', 'url', 'page_limit', 'modified');

    private static $i = NULL;
    private $db = NULL;

    private $links = array();
    private $modified_links = array();
    /**
     * @var array счетчик вставленных на страницу урлов, формат array(<keyword> => array(<url> => <count>....)....)
     */
    private $inserted_links_counter = array();
    /**
     * @var array список вставленных ссылок, нужен для предотвращения взаимной залинковки страниц
     * формат array(<from_url> => array(<to_url> => 1, ...), ...)
     */
    private $inserted_links_list = array();
    /**
     * @var \Models\SphinxManagement\SphinxSearch
     */
    private $posts_sphinx = NULL;
    /**
     * @var \Models\SphinxManagement\SphinxSearch
     */
    private $metatags_sphinx = NULL;

    public static function getInstance(){
        if (empty(self::$i)){
            self::$i = new self;
        }
        return self::$i;
    }

    private function __construct(){
        $this->db = \App\Builder::getInstance()->getDB();
    }

    /**
     * @param string $phrase
     * @param string $url
     * @param int $limit
     * @return FALSE|int link id
     */
    public function addLink($phrase, $url, $limit){
        return $this->db->query('INSERT INTO `' . self::TABLE . '` SET `phrase` = ?s, `url` = ?s, `page_limit` = ?d, `modified` = 1', $phrase, $url, $limit);
    }

    /**
     * @param $id
     * @param $phrase
     * @param $url
     * @param $limit
     * @return FALSE|int
     */
    public function editLink($id, $phrase, $url, $limit){
        $link_data = $this->getLinkById($id);
        if (empty($link_data) || ($phrase == $link_data['phrase'] && $url == $link_data['url'] && $limit == $link_data['page_limit'])){
            return false;
        }
        return $this->db->query('UPDATE `' . self::TABLE . '` SET `phrase` = ?s, `url` = ?s, `page_limit` = ?d, `modified` = 1 WHERE `id` = ?d', $phrase, $url, $limit, $id);
    }

    /**
     * @param int|array $id
     * @return FALSE|int
     */
    public function deleteLink($id){
        return $this->db->query('DELETE FROM `' . self::TABLE . '` WHERE `id` ' . (is_array($id) ? 'IN (?i)' : '= ?d'), $id);
    }

    /**
     * @param int $id
     * @return array|NULL
     */
    public function getLinkById($id){
        return $this->db->query('SELECT `' . implode('`, `', self::$table_fields) . '` FROM `' . self::TABLE . '` WHERE `id` = ?d', $id)->getRow();
    }

    public function search($params = array()){
        return $this->db->query('SELECT `' . implode('`, `', self::$table_fields) . '` FROM `' . self::TABLE . '` WHERE 1
            { AND `modified` = ?d} ORDER BY `id`
            ', isset($params['modified']) ? (!empty($params['modified']) ? 1 : 0) : $this->db->skipIt()
        )->select('id');
    }

    public function buildLinks(){
        $this->loadRegisteredLinks();
        $this->links = $this->search();
        $this->modified_links = $this->search(array('modified' => 1));
        $this->inserted_links_counter = array();
        $this->posts_sphinx = SphinxSearch::factory(SphinxConfig::POSTS_KEY);
        $this->posts_sphinx->needRebuild();
        $this->metatags_sphinx = SphinxSearch::factory(SphinxConfig::METATAGS_KEY);
        // в процессе перелинковки мы будем помечать обработанные тексты
        $this->db->query('UPDATE `' . Post::TABLE_NAME . '` SET `site_links_done` = 0');
        $this->db->query('UPDATE `' . PagePersister::TABLE . '` SET `site_links_done` = 0');

        // сначала обрабатываем отредактированные правила перелинковки
        if (!empty($this->modified_links)){
            foreach($this->modified_links as $link){
                $this->processLink($link);
            }
        }

        // потом посты с пустым complete_text
        $posts = Post::search(array('empty_complete_text' => TRUE));
        if (!empty($posts)){
            foreach($posts as $post){
                $this->processPost($post);
            }
        }
        // и метатеги
        $metatags = PagePersister::getInstance()->search(array('complete_text_empty' => true), false);
        if (!empty($metatags)){
            foreach($metatags as $metatag){
                $this->processMetatag($metatag);
            }
        }

        $this->db->query('UPDATE `' . self::TABLE . '` SET `modified` = 0');
        $this->db->query('UPDATE `' . Post::TABLE_NAME . '` SET `site_links_done` = 1');
        $this->db->query('UPDATE `' . PagePersister::TABLE . '` SET `site_links_done` = 1');

    }

    /**
     * @param array $link_data
     */
    private function processLink($link_data){
        // Сначала посты
        $post_ids = $this->posts_sphinx->select('id', $link_data['phrase'])->getCol('id', 'id');
        if (!empty($post_ids)){
            $posts = Post::search(array('id' => $post_ids, 'site_links_done' => FALSE));
            foreach($posts as $post){
                $this->processPost($post);
            }
        }
        // Потом метатеги
        $metatag_ids = $this->metatags_sphinx->select('id', $link_data['phrase'])->getCol('id', 'id');
        if (!empty($metatag_ids)){
            $metatags = PagePersister::getInstance()->search(array('id' => $metatag_ids, 'site_links_done' => FALSE), FALSE);
            foreach($metatags as $metatag){
                $this->processMetatag($metatag);
            }
        }
    }

    /**
     *
     * @param Post $post
     */
    private function processPost(Post $post){
        $text = $post['raw_text'];
        $url = $post['post_location_url']; // Пост необязательно имеет свою отдельную страницу, нам нужен его фактический url, поэтому $post['url'] не подойдет
                                           // для постов не имеющих определенной страницы перелинковку не делаем из-за невозможности просчитать лимиты
        if (!empty($url) && !empty($text)){
            foreach($this->links as $link){
                $link_url = strpos($link['url'], '?') !== FALSE ? substr($link['url'], 0, strpos($link['url'], '?') + 1) : $link['url'];
                if (strlen($link_url) > 1){
                    $link_url = rtrim($link_url, '/');
                }
                if ($link_url == $url || !empty($this->inserted_links_list[$link_url][$url])){
                    // ссылка на себя или взаимная перелинковка двух страниц недопустима
                    continue;
                }
                $matches = $this->getPhraseMatchBlocks($text, $link['phrase']);
                if (!empty($matches)){
                    $text = $this->insertLinksToText($text, $url, $matches, $link);
                }
            }
            $post_data = $post->asArray();
            $post_data['complete_text'] = $text;
            $post_data['site_links_done'] = 1;
            $post->edit($post_data);
        } else {
            $post_data = $post->asArray();
            $post_data['site_links_done'] = 1;
            $post->edit($post_data);
        }
    }

    private function processMetatag($metatag){
        if (!empty($metatag['text'])){
            $text = $metatag['text'];
            $url = $metatag['page_uid'];
            if (strpos($url, '?') !== FALSE){
                $url = rtrim(substr($url, 0, strpos($url, '?') + 1), '/');
            }
            foreach($this->links as $link){
                $link_url = strpos($link['url'], '?') !== FALSE ? substr($link['url'], 0, strpos($link['url'], '?') + 1) : $link['url'];
                if (strlen($link_url) > 1){
                    $link_url = rtrim($link_url, '/');
                }
                if ($link_url == $url || !empty($this->inserted_links_list[$link_url][$url])){
                    // ссылка на себя или взаимная перелинковка двух страниц недопустима
                    continue;
                }
                $matches = $this->getPhraseMatchBlocks($text, $link['phrase']);
                if (!empty($matches)){
                    $text = $this->insertLinksToText($text, $url, $matches, $link);
                }
            }
            $this->db->query('UPDATE `' . PagePersister::TABLE . '` SET `site_links_done` = 1, `complete_text` = ?s WHERE `id` = ?d', $text, $metatag['id']);
        } else{
            $this->db->query('UPDATE `' . PagePersister::TABLE . '` SET `site_links_done` = 1 WHERE `id` = ?d', $metatag['id']);
        }
    }

    private function getPhraseMatchBlocks($text, $phrase){
        $matches = $this->posts_sphinx->getMatchBlocks(array($text), $phrase, $error);//, SphinxConfig::getOpts(SphinxConfig::SEO_MATCH_BLOCK_OPTS));
        preg_match_all('~>>>(.*?)<<<~', reset($matches), $matches);
        $matches = isset($matches[1]) ? $matches[1] : array();
        if (!empty($matches)){
            $search_words = array_filter(explode(' ', $phrase));
            foreach($matches as $key => $match){
                $matches[$key] = $match = ltrim(rtrim($match, ' \t\n\r\0\x0B<'), ' \t\n\r\0\x0B>');
                $found_words = array_filter(explode(' ', $match));
                if (count($search_words) != count($found_words) || preg_match('~<[^>]*>~', $match)){
                    unset($matches[$key]);
                }
            }
        }
        return $matches;
    }

    private function insertLinksToText($text, $text_url, $keyword_matches, $link_data){
        $replace_limit = isset($this->inserted_links_counter[$link_data['phrase']][$text_url])
            ? ($link_data['page_limit'] - $this->inserted_links_counter[$link_data['phrase']][$text_url])
            : $link_data['page_limit'];
        if ($replace_limit > 0){
            $result = preg_replace('~(' . implode('|', $keyword_matches) . ')(?![^<]*</a>)~i', '<a href="' . $link_data['url'] . '">$1</a>', $text, $replace_limit, $count);
            if ($count){
                $this->inserted_links_counter[$link_data['phrase']][$text_url] = isset($this->inserted_links_counter[$link_data['phrase']][$text_url])
                    ? $this->inserted_links_counter[$link_data['phrase']][$text_url] + $count
                    : $count;
                $link_url = strpos($link_data['url'], '?') !== FALSE ? substr($link_data['url'], 0, strpos($link_data['url'], '?') + 1) : $link_data['url'];
                if (strlen($link_url) > 1){
                    $link_url = rtrim($link_url, '/');
                }
                $this->registrateLink($text_url, $link_url);
                return $result;
            } else {
                return $text;
            }
        }
        return $text;
    }

    private function registrateLink($from, $to){
        if (empty($this->inserted_links_list[$from][$to])){
            $this->inserted_links_list[$from][$to] = 1;
            $this->db->query('INSERT INTO `' . self::INSERTED_LINKS_LIST_TABLE .'` SET `from` = ?s, `to` = ?s', $from, $to);
        }
    }

    private function loadRegisteredLinks(){
        $this->inserted_links_list = $this->db->query('SELECT `from`, `to`, 1 AS `linked` FROM `' . self::INSERTED_LINKS_LIST_TABLE . '` WHERE 1')->getCol(array('from', 'to'), 'linked');
    }

}

