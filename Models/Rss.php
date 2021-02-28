<?php

namespace Models;
use Models\ContentManagement\Post;
class Rss{
    const TABLE_NAME = 'rss_channels';
    const TABLE_LOG = 'rss_log';
    /**
     * @var self[]
     */
    private static $entities = array();
    private static $load_fields = array('id', 'post_type', 'segment_id', 'title', 'description', 'language', 'date_interval', 'max_post_count', 'filename', 'last_creation');
    
    private $channel_data = array();
    private $search_opts = array();
    private $domain = '';
    
    public static function resetFlag($post_type, $segment_id = NULL){
        $db = \App\Builder::getInstance()->getDB();
        $db->query('UPDATE `' . self::TABLE_NAME . '` SET `last_creation` = NULL WHERE `post_type` = ?s { AND `segment_id` = ?d}', 
                $post_type, 
                (!empty($segment_id)) ? $segment_id : $db->skipIt());
    }
    
    public static function getChannels($segment_id = 1){
        $db = \App\Builder::getInstance()->getDB();
        $result = $db->query('SELECT `post_type`, `filename` FROM `'.self::TABLE_NAME.'` WHERE `segment_id` '. (!empty($segment_id) ? '= ' . $db->escape_int($segment_id) : 'IS NULL'))->getCol('post_type', 'filename');
        return $result;
    }
    
    public static function updateChannels(){
        $channels = self::loadChannels();
        foreach($channels as $id=>$channel){
            if (empty(self::$entities[$id])){
                self::$entities[$id] = new self($channel);
            }
            self::$entities[$id]->updateRSS();
        }
    }
    /**
     * Загрузка настроек rss-каналов
     * @return array
     */
    private static function loadChannels(){
        $db = \App\Builder::getInstance()->getDB();
        $result = $db->query('SELECT `'.implode('`, `', self::$load_fields).'` FROM '.self::TABLE_NAME)->select('id');
        return $result;
    }
    
    private function __construct($channel_data) {
        $this->domain = \LPS\Config::isDev() ? \LPS\Config::DEV_DOMAIN_NAME : \LPS\Config::DOMAIN_NAME;
        $this->channel_data = $channel_data;
        $this->search_opts = array(
            'type' => $this->channel_data['post_type'],
            'status' => array(Post::STATUS_CLOSE, Post::STATUS_PUBLIC),
            'segment_id' => $this->channel_data['segment_id']
        );
        if (!is_null($this->channel_data['date_interval'])){
            $this->search_opts['from_dt'] = strtotime($this->channel_data['date_interval']);
            $this->search_opts['to_dt'] = time();
        }
        if (!is_null($channel_data['last_creation'])) {
            $this->channel_data['last_creation'] = strtotime($channel_data['last_creation']);
        }
    }
    /**
     * Проверка необходимости обновления
     * @return boolean
     */
    private function needUpdate(){
        if (is_null($this->channel_data['last_creation'])) {
            return true;
        }
        $post = Post::search($this->search_opts, $count, 0, 1, 'dt');
        $post = array_shift($post);
        if (is_null($post) || $this->channel_data['last_creation'] > $post['timestamp']){
            return false;
        }
        return true;
    }
    
    private function updateRSS(){
        $start = microtime(1);
        $db = \App\Builder::getInstance()->getDB();
        if ($this->needUpdate()) {
            $limit = 10000;
            if (!is_null($this->channel_data['max_post_count'])){
                $limit = $this->channel_data['max_post_count'];
            }
            $this->make(Post::search($this->search_opts, $count, 0, $limit, 'dt'));
            $db->query('UPDATE `'.self::TABLE_NAME.'` SET `last_creation` = \'' . date('Y-m-d H:i:s') .'\' WHERE `id` = '.$this->channel_data['id']);
            $message = 'Канал обновлен';
        } else {
            $message = 'Обновление не требуется';
        }
        $end = microtime(1);
        $db->query(
            'INSERT INTO `'.self::TABLE_LOG.'` (`post_type`, `segment_id`, `message`, `time_elapsed`) VALUES(?s, ?d, ?s, ?d)',
            $this->channel_data['post_type'],
            $this->channel_data['segment_id'],
            $message,
            $end - $start
        );
    }
    /**
     * Формирование xml файла
     * @param Post[] $posts массив постов
     */
    private function make($posts){
        $opts = $this->channel_data;
        $chanVals = array(
            'title' => $opts['title'],
            'link' => $this->domain,
            'description' => $opts['description'],
            'language' => $opts['language'],
            'pubDate' => date('r'),
            'lastBuildDate' => date('r'),
            'managingEditor' => \LPS\Config::getParametr('email', 'rss_editor'),
            'webMaster' => \LPS\Config::getParametr('email', 'rss_webmaster')
        );
        $rss = new \SimpleXMLElement('<rss></rss>');
        $rss->addAttribute('version', '2.0');
        $channel = $rss->addChild('channel');
        foreach($chanVals as $node=>$value){
            $channel->addChild($node, $value);
        }
        foreach($posts as $post){
            /* @var $post Post */
            $title = (strpos($post['title'], '&') !== false) ? str_replace('&', '&amp;', $post['title']) : $post['title'];
            $item = $channel->addChild('item');
            $item->addChild('guid', $this->domain . $post->getUrl($post['segment_id']));
            $item->addChild('title', $title);
            $item->addChild('link', $this->domain . $post->getUrl($post['segment_id']));
            $item->addChild('pubDate', date('r', $post['timestamp']));
            $descr = $item->addChild('description');
            self::addCDATA($descr, $post['text']);
        }
        if (!file_exists(\LPS\Config::getRealDocumentRoot() . '/data/rss/')) {
            \LPS\Components\FS::makeDirs(\LPS\Config::getRealDocumentRoot() . '/data/rss/', 0775);
        }
        $rss->asXML(\LPS\Config::getRealDocumentRoot() . '/data/rss/'.$opts['filename']);
    }
    
    private static function addCDATA($item, $value){
        $node = dom_import_simplexml($item);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($value));
    }
}
